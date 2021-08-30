<template>
    <div>
        <div class="row ">

            <div class="col-md-12 col-lg-12 col-xl-12 ">
                <div class="row" v-if="applyFilter">
                    <div class="col-lg-4 col-md-4 col-sm-12 pb-2 ml-3">
                        <div class="d-flex">
                            <div style="width:100px">
                                Filtrar por:
                            </div>
                            <el-select v-model="search.column"  placeholder="Select" @change="changeClearInput">
                                <el-option v-for="(label, key) in columns" :key="key" :value="key" :label="label"></el-option>
                            </el-select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-12 pb-2">
                        <template v-if="search.column=='date_of_issue'  || search.column=='date_of_payment' ">
                            <el-date-picker
                                v-model="search.value"
                                type="date"
                                style="width: 100%;"
                                placeholder="Buscar"
                                value-format="yyyy-MM-dd"
                                @change="getRecords">
                            </el-date-picker>
                        </template>
                        <template v-else>
                            <el-input placeholder="Buscar"
                                v-model="search.value"
                                style="width: 100%;"
                                prefix-icon="el-icon-search"
                                @input="getRecords">
                            </el-input>
                        </template>
                    </div>
                </div>

            <div class="col-md-12 col-lg-12 col-xl-12 ">

                <div class="row mt-2">
                        <div class="col-md-3">
                            <label class="control-label">Periodo</label>
                            <el-select v-model="form.period" @change="changePeriod">
                                <el-option key="month" value="month" label="Por mes"></el-option>
                                <el-option key="week" value="week" label="Por semana"></el-option>
                                <el-option key="between_dates" value="between_dates" label="Entre fechas"></el-option>
                            </el-select>
                        </div>
                        
                        <template v-if="form.period === 'week'">
                            <div class="col-md-3">
                                <label class="control-label">Semana</label>
                                <el-date-picker v-model="form.week" type="week" format="Week WW" @change="changeDisabledDates" :clearable="true"></el-date-picker>
                            </div>
                        </template>
                        <template v-if="form.period === 'month' ">
                            <div class="col-md-3">
                                <label class="control-label">Mes</label>
                                <el-date-picker v-model="form.month" type="month"
                                                @change="changeDisabledDates"
                                                value-format="yyyy-MM" format="MM/yyyy" :clearable="true"></el-date-picker>
                            </div>
                        </template>
                        <template v-if="form.period === 'date' || form.period === 'between_dates'">
                            <div class="col-md-3">
                                <label class="control-label">Fecha del</label>
                                <el-date-picker v-model="form.d_start" type="date"
                                                @change="changeDisabledDates"
                                                value-format="yyyy-MM-dd" format="dd/MM/yyyy" :clearable="true"></el-date-picker>
                            </div>
                        </template>
                        <template v-if="form.period === 'between_dates'">
                            <div class="col-md-3">
                                <label class="control-label">Fecha al</label>
                                <el-date-picker v-model="form.d_end" type="date"
                                                @change="changeDisabledDates"
                                                :picker-options="pickerOptionsDates"
                                                value-format="yyyy-MM-dd" format="dd/MM/yyyy" :clearable="true"></el-date-picker>
                            </div>
                        </template>
 
                    </div>

                </div>
            </div>
            </div>


            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <slot name="heading"></slot>
                        </thead>
                        <tbody>
                        <slot v-for="(row, index) in records" :row="row" :index="customIndex(index)"></slot>
                        </tbody>
                    </table>
                    <div>
                        <el-pagination
                                @current-change="getRecords"
                                layout="total, prev, pager, next"
                                :total="pagination.total"
                                :current-page.sync="pagination.current_page"
                                :page-size="pagination.per_page">
                        </el-pagination>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>


<script>

    import moment from 'moment'
    import queryString from 'query-string'

    export default {
        props: {
            resource: String,
            applyFilter:{
                type: Boolean,
                default: true,
                required: false
            }
        },
        data () {
            return {
                search: {
                    column: null,
                    form: null,
                    value: null
                },
                columns: [],
                records: [],
                form: {},
                pagination: {},
                pickerOptionsDates: {
                    disabledDate: (time) => {
                        time = moment(time).format('YYYY-MM-DD')
                        return this.form.date_start > time
                    }
                },
                pickerOptionsMonths: {
                    disabledDate: (time) => {
                        time = moment(time).format('YYYY-MM')
                        return this.form.month_start > time
                    }
                },
            }
        },
        computed: {
        },
        created() {
            this.initForm()
            this.$eventHub.$on('reloadData', () => {
                this.getRecords()
            })
        },
        async mounted () {
            let column_resource = _.split(this.resource, '/')
           // console.log(column_resource)
            await this.$http.get(`/${_.head(column_resource)}/columns`).then((response) => {
                this.columns = response.data
                this.search.column = _.head(Object.keys(this.columns))
            });
            await this.getRecords()

        },
        methods: {
            changeDisabledDates() {

                if (this.form.date_end < this.form.date_start) {
                    this.form.date_end = this.form.date_start
                }

                this.selectDate()
                
            },
            selectDate(){

                this.form.date_start = null
                this.form.date_end = null
                
                if(this.form.period === 'week' && this.form.week) {
                    this.form.date_start = moment(this.form.week).startOf('week').format('YYYY-MM-DD')
                    this.form.date_end = moment(this.form.week).endOf('week').format('YYYY-MM-DD')
                
                }else if(this.form.period === 'month' && this.form.month) {
                    this.form.date_start = moment(this.form.month).startOf('month').format('YYYY-MM-DD')
                    this.form.date_end = moment(this.form.month).endOf('month').format('YYYY-MM-DD')
                
                }else{
                    // console.log(this.form)

                    this.form.date_start = this.form.d_start
                    this.form.date_end = this.form.d_end
                }


                // if(this.form.date_start && this.form.date_end){
                    this.getRecords()
                // }

                // console.log(this.form)

            },
            changeDisabledMonths() {
                // if (this.form.month_end < this.form.month_start) {
                //     this.form.month_end = this.form.month_start
                // }
                // this.loadAll();
            },
            changePeriod() {

                this.form.date_start = null
                this.form.date_end = null
                this.form.week = null
                this.form.month = null
                this.form.d_start = null
                this.form.d_end = null

                this.getRecords()

            },
            initForm(){

                this.form = {
                    week: null,
                    month: null,
                    d_start: null,
                    d_end: null,
                    period: 'month',
                    date_start: null,
                    date_end: null,
                }

            },
            customIndex(index) {
                return (this.pagination.per_page * (this.pagination.current_page - 1)) + index + 1
            },
            getRecords() {
                this.search.form = JSON.stringify(this.form)
                return this.$http.get(`/${this.resource}/records?${this.getQueryParameters()}`).then((response) => {
                    this.records = response.data.data
                    this.pagination = response.data.meta
                    this.pagination.per_page = parseInt(response.data.meta.per_page)
                });
            },
            getQueryParameters() {
                return queryString.stringify({
                    page: this.pagination.current_page,
                    limit: this.limit,
                    ...this.search,
                })
            },
            changeClearInput(){
                this.search.value = ''
                this.getRecords()
            }
        }
    }
</script>

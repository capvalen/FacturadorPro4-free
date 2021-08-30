<template>
    <div>
        <div class="row">

            <div class="col-md-12 col-lg-12 col-xl-12 ">

                <div class="row mt-2">
                        <div class="col-md-3">
                            <label class="control-label">Periodo
                                <el-tooltip class="item" effect="dark" content="Filtra por fecha de pago" placement="top-start">
                                    <i class="fa fa-info-circle"></i>
                                </el-tooltip>
                            </label>
                            <el-select v-model="form.period" @change="changePeriod">
                                <el-option key="month" value="month" label="Por mes"></el-option>
                                <el-option key="between_months" value="between_months" label="Entre meses"></el-option>
                                <el-option key="date" value="date" label="Por fecha"></el-option>
                                <el-option key="between_dates" value="between_dates" label="Entre fechas"></el-option>
                            </el-select>
                        </div>
                        <template v-if="form.period === 'month' || form.period === 'between_months'">
                            <div class="col-md-3">
                                <label class="control-label">Mes de</label>
                                <el-date-picker v-model="form.month_start" type="month"
                                                @change="changeDisabledMonths"
                                                value-format="yyyy-MM" format="MM/yyyy" :clearable="false"></el-date-picker>
                            </div>
                        </template>
                        <template v-if="form.period === 'between_months'">
                            <div class="col-md-3">
                                <label class="control-label">Mes al</label>
                                <el-date-picker v-model="form.month_end" type="month"
                                                :picker-options="pickerOptionsMonths"
                                                value-format="yyyy-MM" format="MM/yyyy" :clearable="false"></el-date-picker>
                            </div>
                        </template>
                        <template v-if="form.period === 'date' || form.period === 'between_dates'">
                            <div class="col-md-3">
                                <label class="control-label">Fecha del</label>
                                <el-date-picker v-model="form.date_start" type="date"
                                                @change="changeDisabledDates"
                                                value-format="yyyy-MM-dd" format="dd/MM/yyyy" :clearable="false"></el-date-picker>
                            </div>
                        </template>
                        <template v-if="form.period === 'between_dates'">
                            <div class="col-md-3">
                                <label class="control-label">Fecha al</label>
                                <el-date-picker v-model="form.date_end" type="date"
                                                :picker-options="pickerOptionsDates"
                                                value-format="yyyy-MM-dd" format="dd/MM/yyyy" :clearable="false"></el-date-picker>
                            </div>
                        </template> 
                    
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Tipo</label>
                                <el-select v-model="form.payment_type" clearable>
                                    <el-option v-for="option in payment_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Destino</label>
                                <el-select v-model="form.destination_type" clearable>
                                    <el-option v-for="option in destination_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                            </div>
                        </div>

                        <div class="col-lg-7 col-md-7 col-md-7 col-sm-12" style="margin-top:29px">
                            <el-button class="submit" type="primary" @click.prevent="getRecordsByFilter" :loading="loading_submit" icon="el-icon-search" >Buscar</el-button>

                            <template v-if="records.length>0">

                                <el-button class="submit" type="danger"  icon="el-icon-tickets" @click.prevent="clickDownload('pdf')" >Exportar PDF</el-button>

                                <el-button class="submit" type="success" @click.prevent="clickDownload('excel')"><i class="fa fa-file-excel" ></i>  Exportal Excel</el-button>

                            </template>

                        </div>

                </div>
                <div class="row mt-1 mb-4">

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
                        <tfoot v-if="resource == 'finances/global-payments'">
                            <tr>
                                <td colspan="9"></td>
                                <td ><strong>Totales PEN</strong></td> 
                                <td>{{totals.total_pen}}</td>
                            </tr>
                            <tr>
                                <td colspan="9"></td>
                                <td ><strong>Totales USD</strong></td> 
                                <td>{{totals.total_usd}}</td>

                            </tr>
                        </tfoot>
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
<style>
.font-custom{
    font-size:15px !important
}
</style>
<script>

    import moment from 'moment'
    import queryString from 'query-string'

    export default {
        props: {
            resource: String,
            applyCustomer: { type : Boolean, required: false, default: false}
        },
        data () {
            return {
                loading_submit:false,
                loading_search:false,
                columns: [],
                records: [],
                headers: headers_token,
                pagination: {},
                search: {},
                payment_types: [],
                destination_types: [],
                form: {},
                totals: {},
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
                sellers: []
            }
        },
        computed: {
        },
        created() {
            this.initForm()
            this.initTotals()
            this.$eventHub.$on('reloadData', () => {
                this.getRecords()
            })
        },
        async mounted () {

            await this.$http.get(`/${this.resource}/filter`)
                .then(response => {
                    this.payment_types = response.data.payment_types; 
                    this.destination_types = response.data.destination_types; 
                });


            await this.getRecords()

        },
        methods: {
            initTotals(){

                this.totals = {
                    total_pen : 0,
                    total_usd : 0,
                }

            },
            clickDownload(type) {
                let query = queryString.stringify({
                    ...this.form
                });
                window.open(`/${this.resource}/${type}/?${query}`, '_blank');
            },
            initForm(){

                this.form = {
                    payment_type: null,
                    destination_type: null,
                    period: 'month',
                    date_start: moment().format('YYYY-MM-DD'),
                    date_end: moment().format('YYYY-MM-DD'),
                    month_start: moment().format('YYYY-MM'),
                    month_end: moment().format('YYYY-MM'),
                }

            }, 
            customIndex(index) {
                return (this.pagination.per_page * (this.pagination.current_page - 1)) + index + 1
            },
            async getRecordsByFilter(){

                this.loading_submit = await true
                await this.getRecords()
                this.loading_submit = await false

            },
            getRecords() {
                return this.$http.get(`/${this.resource}/records?${this.getQueryParameters()}`).then((response) => {
                    this.records = response.data.data
                    this.pagination = response.data.meta
                    this.pagination.per_page = parseInt(response.data.meta.per_page)
                    this.getTotals(response.data.data)
                    this.loading_submit = false
                });


            },
            getTotals(records){

                this.initTotals()
                this.totals.total_pen = _.round(_.sumBy(_.filter(records, {currency_type_id : 'PEN'}), (row) => { return parseFloat(row.total) }), 2)
                this.totals.total_usd = _.round(_.sumBy(_.filter(records, {currency_type_id : 'USD'}), (row) => { return parseFloat(row.total) }), 2)
 
            },
            getQueryParameters() {
                return queryString.stringify({
                    page: this.pagination.current_page,
                    limit: this.limit,
                    ...this.form
                })
            },

            changeDisabledDates() {
                if (this.form.date_end < this.form.date_start) {
                    this.form.date_end = this.form.date_start
                }
                // this.loadAll();
            },
            changeDisabledMonths() {
                if (this.form.month_end < this.form.month_start) {
                    this.form.month_end = this.form.month_start
                }
                // this.loadAll();
            },
            changePeriod() {
                if(this.form.period === 'month') {
                    this.form.month_start = moment().format('YYYY-MM');
                    this.form.month_end = moment().format('YYYY-MM');
                }
                if(this.form.period === 'between_months') {
                    this.form.month_start = moment().startOf('year').format('YYYY-MM'); //'2019-01';
                    this.form.month_end = moment().endOf('year').format('YYYY-MM');;
                }
                if(this.form.period === 'date') {
                    this.form.date_start = moment().format('YYYY-MM-DD');
                    this.form.date_end = moment().format('YYYY-MM-DD');
                }
                if(this.form.period === 'between_dates') {
                    this.form.date_start = moment().startOf('month').format('YYYY-MM-DD');
                    this.form.date_end = moment().endOf('month').format('YYYY-MM-DD');
                }
                // this.loadAll();
            },
        }
    }
</script>

<template>
    <div>
        <div class="row">

            <div class="col-md-12 col-lg-12 col-xl-12 ">
                  
                <div class="row mt-2"> 
                    
                        <div class="col-lg-6 col-md-6" >
                            <div class="form-group"> 
                                <label class="control-label">Cliente
                                </label>
                                
                                <el-select v-model="form.person_id" filterable remote  popper-class="el-select-customers"  clearable
                                    placeholder="Nombre o número de documento"
                                    :remote-method="searchRemotePersons"
                                    :loading="loading_search"
                                    @change="changePersons">
                                    <el-option v-for="option in persons" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
 
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="control-label">Periodo</label>
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
                        
                        <!-- <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Establecimiento</label>
                                <el-select v-model="form.establishment_id" clearable>
                                    <el-option v-for="option in establishments" :key="option.id" :value="option.id" :label="option.name"></el-option>
                                </el-select>
                            </div>
                        </div>
                        <div class="col-md-3" >
                            <div class="form-group">
                                <label class="control-label">Tipo de documento</label>
                                <el-select v-model="form.document_type_id" clearable>
                                    <el-option v-for="option in document_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                            </div>
                        </div> -->
                        
                        <div class="col-lg-7 col-md-7 col-md-7 col-sm-12" style="margin-top:29px"> 
                            <el-button class="submit" type="primary" @click.prevent="getRecordsByFilter" :loading="loading_submit" icon="el-icon-search" >Buscar</el-button>
                            
                            <template v-if="records.length>0"> 

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
        },
        data () {
            return {
                loading_submit:false,
                persons: [],
                all_persons: [],
                loading_search:false,
                columns: [],
                records: [],
                headers: headers_token,
                document_types: [],
                pagination: {}, 
                search: {}, 
                totals: {}, 
                establishment: null,
                establishments: [],       
                form: {},
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
            this.initTotals()
            this.$eventHub.$on('reloadData', () => {
                this.getRecords()
            })
        },
        async mounted () { 

            await this.$http.get(`/${this.resource}/filter`)
                .then(response => {
                    this.establishments = response.data.establishments;
                    this.all_persons = response.data.persons
                    this.document_types = response.data.document_types;
                    // this.form.establishment_id = (this.establishments.length > 0)?this.establishments[0].id:null;
                });


            // await this.getRecords()
            await this.filterPersons()
            // await this.getTotals()
            this.form.type_person = 'customers'

        },
        methods: { 
            changePersons(){
                // this.form.type_person = 'customers'
            },
            searchRemotePersons(input) {  
                
                if (input.length > 0) { 

                    this.loading_search = true
                    let parameters = `input=${input}`
                    
                    this.form.type_person = 'customers'

                    this.$http.get(`/reports/data-table/persons/${this.form.type_person}?${parameters}`)
                            .then(response => { 
                                this.persons = response.data.persons
                                this.loading_search = false
                                
                                if(this.persons.length == 0){
                                    this.filterPersons()
                                }
                            })  
                } else {
                    this.filterPersons()
                }

            },
            filterPersons() { 
                this.persons = this.all_persons
            }, 
            clickDownload(type) {                 
                let query = queryString.stringify({
                    ...this.form
                });
                window.open(`/${this.resource}/${type}/?${query}`, '_blank');
            },
            initForm(){
 
                this.form = {
                    establishment_id: null,
                    person_id: null,
                    type_person:null,
                    document_type_id:null,
                    period: 'month',
                    date_start: moment().format('YYYY-MM-DD'),
                    date_end: moment().format('YYYY-MM-DD'),
                    month_start: moment().format('YYYY-MM'),
                    month_end: moment().format('YYYY-MM'),
                }

            }, 
            initTotals(){
                
                this.totals = {
                    acum_total_taxed : 0,
                    acum_total_igv : 0,
                    acum_total : 0,      
                    acum_total_exonerated : 0,
                    acum_total_unaffected : 0,         
                    acum_total_free : 0,

                    acum_total_taxed_usd : 0,
                    acum_total_igv_usd : 0,
                    acum_total_usd : 0,
                }
            },
            customIndex(index) {
                return (this.pagination.per_page * (this.pagination.current_page - 1)) + index + 1
            }, 
            async getRecordsByFilter(){
                
                if(!this.form.person_id){
                    return this.$message.error('Debe seleccionar un cliente')
                }

                this.loading_submit = await true
                await this.getRecords()
                this.loading_submit = await false

            },
            getRecords() {
                return this.$http.get(`/${this.resource}/records?${this.getQueryParameters()}`).then((response) => {
                    this.records = response.data.data
                    this.pagination = response.data.meta
                    this.pagination.per_page = parseInt(response.data.meta.per_page)
                    this.loading_submit = false
                    // this.initTotals()
                    if(this.resource == 'reports/sales') this.getTotals(response.data.data)
                });


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
<template>
    <div>
        <div class="row">

            <div class="col-md-12 col-lg-12 col-xl-12 ">
                  <div class="row">
                    <div class="col-lg-8 col-md-8 mb-2">
                        <div class="form-group"> 
                            <label class="control-label font-custom"><strong>Filtros de busqueda</strong></label> 
                            <template v-if="!see_more">
                                <a class="control-label font-weight-bold text-info font-custom" href="#" @click="clickSeeMore"><strong> [+ Ver más]</strong></a> 
                            </template>
                            <template v-else>
                                <a class="control-label font-weight-bold text-info font-custom" href="#" @click="clickSeeMore"><strong> [- Ver menos]</strong></a> 
                            </template>
                        </div>
                    </div> 
                </div>
                <div class="row mt-2" v-if="see_more"> 
                    <div class="col-lg-4 col-md-4">
                        <div class="form-group"  >
                            <label class="control-label">RUC Cliente</label> 
                            <el-input placeholder="Ingresar"
                                v-model="form.number">
                            </el-input>
                        </div>
                    </div> 
                    <div class="col-lg-4 col-md-4 ">
                        <div class="form-group"> 
                            <label class="control-label">Tipo cliente</label>
                            <el-select v-model="form.person_type_id" filterable clearable>
                                <el-option v-for="option in person_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                            </el-select>
                        </div>
                    </div> 
                    
                    <div class="col-lg-4 col-md-4 col-sm-4 pb-2">
                        <div class="form-group"  >
                            <label class="control-label">Tipo producto</label>
                            <el-select v-model="form.category_id" filterable clearable>
                                <el-option v-for="option in categories" :key="option.id" :value="option.id" :label="option.name"></el-option>
                            </el-select> 
                        </div>
                    </div>      
                    
                </div>
                <div class="row mt-1 mb-3">
                    
                    <div class="col-lg-9 col-md-9 col-md-9 col-sm-12" style="margin-top:29px"> 
                        <el-button class="submit" type="primary" @click.prevent="getRecordsByFilter" :loading="loading_submit" icon="el-icon-search" >Buscar</el-button>
                        
                        <template v-if="records.length>0"> 
                            <el-button class="submit" type="success" @click.prevent="clickDownload('excel')"><i class="fa fa-file-excel" ></i>  Exportal Excel</el-button>
                        </template>

                    </div>     
                </div> 
            </div>
            <div class="row mt-2">  

            </div>
                <div class="row mt-1 mb-4">
                    
                </div> 
            </div>

            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <slot name="heading">sds sdsd</slot>
                      
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
                columns: [],
                records: [],
                headers: headers_token,
                document_types: [],
                pagination: {}, 
                see_more:false,
                search: {}, 
                totals: {}, 
                establishment: null,
                establishments: [],       
                person_types: [],       
                categories: [],       
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

            
            await this.$http.get(`/${this.resource}/data_table`).then((response) => {
                this.categories = response.data.categories
                this.person_types = response.data.person_types 

            });

            await this.$http.get(`/${this.resource}/filter`)
                .then(response => {
                    this.establishments = response.data.establishments;
                    this.document_types = response.data.document_types;
                    // this.form.establishment_id = (this.establishments.length > 0)?this.establishments[0].id:null;
                });


            await this.getRecords()
            // await this.getTotals()

        },
        methods: { 
            clickSeeMore(){
                this.see_more = (this.see_more) ? false : true
            }, 
            clickDownload(type) {                 
                let query = queryString.stringify({
                    ...this.form
                });
                window.open(`/${this.resource}/${type}/?${query}`, '_blank');
            },
            initForm(){
 
                this.form = {
                    category_id: null,
                    person_type_id:null,
                    number:null,  
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
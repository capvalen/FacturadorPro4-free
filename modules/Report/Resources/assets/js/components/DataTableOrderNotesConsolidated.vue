<template>
    <div>
        <div class="row">

            <div class="col-md-12 col-lg-12 col-xl-12 ">
                  
                <div class="row mt-2"> 
                    
                        <div class="col-lg-3 col-md-3" >
                            <div class="form-group"> 
                                <label class="control-label">Tipo de rango
                                </label>
                                
                                <el-select v-model="form.date_range_type_id" filterable >
                                    <el-option v-for="option in date_range_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
 
                            </div>
                        </div> 

                        <div class="col-md-3">
                            <label class="control-label">Fecha del</label>
                            <el-date-picker v-model="form.date_start" type="date"
                                            @change="changeDisabledDates"
                                            value-format="yyyy-MM-dd" format="dd/MM/yyyy" :clearable="false"></el-date-picker>
                        </div>

                        <div class="col-md-3">
                            <label class="control-label">Fecha al</label>
                            <el-date-picker v-model="form.date_end" type="date"
                                            :picker-options="pickerOptionsDates"
                                            value-format="yyyy-MM-dd" format="dd/MM/yyyy" :clearable="false"></el-date-picker>
                        </div>
                        
                        <template v-if="resource != 'reports/sales-consolidated'">
                            <div class="col-lg-3 col-md-3" >
                                <div class="form-group"> 
                                    <label class="control-label">Estado
                                    </label>
                                    
                                    <el-select v-model="form.order_state_type_id" filterable >
                                        <el-option v-for="option in order_state_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                    </el-select>
    
                                </div>
                            </div> 
                        </template>
                        <template v-else>
                            <div class="col-lg-3 col-md-3" >
                                <div class="form-group"> 
                                    <label class="control-label">Tipo documento
                                    </label>
                                    <el-select v-model="form.document_type_id" filterable clearable>
                                        <el-option v-for="option in document_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                    </el-select>
    
                                </div>
                            </div> 
                        </template>

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
 
                        <div class="col-lg-6 col-md-6" >
                            <div class="form-group"> 
                                <label class="control-label">Vendedor
                                </label>
                                
                                <el-select v-model="form.seller_id" filterable  popper-class="el-select-customers"  clearable
                                    placeholder="Nombre"
                                    @change="changeSellers">
                                    <el-option v-for="option in sellers" :key="option.id" :value="option.id" :label="option.name"></el-option>
                                </el-select>
 
                            </div>
                        </div> 
 
                        
                        <div class="col-lg-7 col-md-7 col-md-7 col-sm-12" style="margin-top:29px"> 
                            <el-button class="submit" type="primary" @click.prevent="getRecordsByFilter" :loading="loading_submit" icon="el-icon-search" >Buscar</el-button>
                            
                            <template v-if="records.length>0"> 

                                <!-- <el-button class="submit" type="success" @click.prevent="clickDownload('excel')"><i class="fa fa-file-excel" ></i>  Exportal Excel</el-button> -->
                                <el-button class="submit" type="danger"  icon="el-icon-tickets" @click.prevent="clickDownload('pdf')" >Exportar PDF</el-button>

                            </template>

                        </div>             
                    
                </div>
                <div class="row mt-1 mb-4">
                    
                </div> 
            </div>


            <div class="col-md-12" v-if="records.length>0">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <slot name="heading"></slot>
                        </thead>
                        <tbody> 
                            <slot v-for="(row, index) in records" :row="row" :index="customIndex(index)"></slot>
                        </tbody> 
                        <tfoot v-if="resource == 'reports/order-notes-consolidated' || resource == 'reports/sales-consolidated'">
                            <tr>
                                <td :colspan="resource == 'reports/order-notes-consolidated' ? 3:0"></td>
                                <td ><strong>Total</strong></td>
                                <td class="text-center">{{totals}}</td>
                                <td class="text-center">S/ {{totals_amount}}</td>
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
        },
        data () {
            return {
                loading_submit:false,
                persons: [],
                all_persons: [],
                loading_search:false,
                columns: [],
                records: [],
                date_range_types: [],
                document_types: [],
                order_state_types: [],
                sellers: [],
                pagination: {}, 
                search: {}, 
                totals: {}, 
                establishment: null,
                form: {}, 
                pickerOptionsDates: {
                    disabledDate: (time) => {
                        time = moment(time).format('YYYY-MM-DD')
                        return this.form.date_start > time
                    }
                },
                totals:0,
                totals_amount:0
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

            await this.$http.get(`/${this.resource}/filter`)
                .then(response => {
                    this.all_persons = response.data.persons
                    this.order_state_types = response.data.order_state_types
                    this.date_range_types = response.data.date_range_types
                    this.sellers = response.data.sellers
                    this.document_types = response.data.document_types
                });


            // await this.getRecords()
            await this.filterPersons()
            // await this.getTotals()
            this.form.type_person = 'customers'

        },
        methods: { 
            changeDisabledDates() {
                if (this.form.date_end < this.form.date_start) {
                    this.form.date_end = this.form.date_start
                }
                // this.loadAll();
            },
            changePersons(){
                this.form.seller_id = null
                this.$eventHub.$emit('changeFilterColumn', 'person')
                // this.records = []
            },
            changeSellers(){
                this.form.person_id = null
                this.$eventHub.$emit('changeFilterColumn', 'seller')
                // this.records = []
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
                    person_id: null,
                    document_type_id: null,
                    date_range_type_id: 'date_of_issue',
                    order_state_type_id: 'pending',
                    type_person:null,
                    seller_id:null,
                    date_start: moment().startOf('month').format('YYYY-MM-DD'), 
                    date_end: moment().endOf('month').format('YYYY-MM-DD'), 
                }

            },  
            customIndex(index) {
                return (this.pagination.per_page * (this.pagination.current_page - 1)) + index + 1
            }, 
            async getRecordsByFilter(){
                
                if(!this.form.person_id &&  !this.form.seller_id){
                    return this.$message.error('Debe seleccionar un cliente o vendedor')
                }

                this.loading_submit = await true
                await this.getRecords()
                // await this.getTotals()
                this.loading_submit = await false

            },
            getTotals(){
                this.totals = _.sumBy(this.records, (it) => parseFloat(it.item_quantity));
                this.totals_amount = _.sumBy(this.records, (it) => parseFloat(it.total));
            },
            getRecords() {
                return this.$http.get(`/${this.resource}/records?${this.getQueryParameters()}`).then((response) => {

                    if(this.resource == 'reports/order-notes-consolidated'){
                        this.records = _.orderBy(response.data.data, ['user'], ['asc'])
                    }else{
                        this.records = response.data.data
                    }

                    this.pagination = response.data.meta
                    this.pagination.per_page = parseInt(response.data.meta.per_page)
                    this.getTotals()
                    this.loading_submit = false
                });


            },
            getQueryParameters() {
                return queryString.stringify({
                    page: this.pagination.current_page,
                    limit: this.limit,
                    ...this.form
                })
            }, 
        }
    }
</script>
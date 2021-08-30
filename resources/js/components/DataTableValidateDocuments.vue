<template>
    <div>
        <div class="row ">

            <div class="col-md-12 col-lg-12 col-xl-12 ">
                  
                <div class="row mt-2"> 
                    <div class="col-lg-4 col-md-4 ">
                        <div class="form-group" :class="{'has-danger': errors.document_type_id}"> 
                            <label class="control-label">Tipo comprobante<span class="text-danger"> *</span></label>
                            <el-select v-model="search.document_type_id" @change="changeDocumentType" popper-class="el-select-document_type" filterable>
                                <el-option v-for="option in document_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.document_type_id" v-text="errors.document_type_id[0]"></small>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2">
                        <div class="form-group"  :class="{'has-danger': errors.series}">
                            <label class="control-label">Serie<span class="text-danger"> *</span></label>
                            <el-select v-model="search.series" filterable>
                                <el-option v-for="option in series" :key="option.number" :value="option.number" :label="option.number"></el-option>
                            </el-select> 
                            <small class="form-control-feedback" v-if="errors.series" v-text="errors.series[0]"></small>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2">
                        <div class="form-group"  :class="{'has-danger': errors.start_number}">
                            <label class="control-label">Número inicial<span class="text-danger"> *</span></label> 
                            <el-input placeholder="Ingresar"
                                v-model="search.start_number">
                            </el-input>
                            <small class="form-control-feedback" v-if="errors.start_number" v-text="errors.start_number[0]"></small>
                        </div>
                    </div> 
                    <div class="col-lg-2 col-md-2">
                        <div class="form-group"  >
                            <label class="control-label">Número final</label> 
                            <el-input placeholder="Ingresar"
                                v-model="search.end_number">
                            </el-input>
                        </div>
                    </div> 
                     
                    <div class="col-lg-8 col-md-8 col-md-8 col-sm-12 mt-4"> 
                        <el-button class="submit" type="primary" @click.prevent="getRecordsByFilter" :loading="loading_submit" icon="el-icon-check" >Validar documentos</el-button>
                        <el-button class="submit" type="info" @click.prevent="cleanInputs"  icon="el-icon-delete" >Limpiar </el-button>

                        <template v-if="records.length > 0">
                            <el-button class="submit" type="danger" @click.prevent="clickRegularizeDocuments" icon="el-icon-edit" >Regularizar documentos</el-button>
                        </template>

                    </div>             
                    
                </div>
                <div class="row mt-1 mb-3">
                    
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
                columns: [],
                records: [],
                customers: [],
                document_types: [],
                state_types: [],
                pagination: {}, 
                errors: {}, 
                search: {}, 
                all_series: [], 
                series: [],            
                see_more:false, 
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

            await this.$http.get(`/${this.resource}/data_table`).then((response) => {
                this.document_types = response.data.document_types
                this.all_series = response.data.series
                this.search.document_type_id = (this.document_types.length > 0) ? this.document_types[0].id : null
                this.changeDocumentType()
            });


            // await this.getRecords()

        },
        methods: {
            clickRegularizeDocuments() {

                this.$confirm('¿Desea actualizar el estado de los documentos con los ubicados en SUNAT?', 'Regularizar documentos', {
                    confirmButtonText: 'Regularizar',
                    cancelButtonText: 'Cancelar',
                    type: 'warning'
                }).then(() => {

                    this.$eventHub.$emit('valueLoadingRegularize', true)

                    this.$http.post(`/${this.resource}/regularize`, this.search)
                        .then(response => {
                            if (response.data.success) {
                                this.$message.success(response.data.message)
                                this.getRecordsByFilter()
                            } else {
                                this.$message.error(response.data.message)
                            }
                        })
                        .catch(error => {
                            if (error.response.status === 500) {
                                this.$message.error('Error al intentar regularizar');
                            } else {
                                console.log(error.response.data.message)
                            }
                        })
                        .then(()=>{
                            this.$eventHub.$emit('valueLoadingRegularize', false)
                        })

                }).catch(error => {
                    console.log(error)
                });
            },
            clickSeeMore(){
                this.see_more = (this.see_more) ? false : true
            },
            initForm(){

                this.search = { 
                    document_type_id:null,
                    series:null, 
                    start_number:null, 
                    end_number:null, 
                }
            },
            changeDocumentType(){                
                this.filterSeries();
            },
            async filterSeries() {
                this.search.series = null
                this.series = await _.filter(this.all_series, {'document_type_id': this.search.document_type_id});
                this.search.series = (this.series.length > 0)?this.series[0].number:null
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
                
                this.$eventHub.$emit('valueLoading', true)
                return this.$http.get(`/${this.resource}/records?${this.getQueryParameters()}`).then((response) => {
                    this.records = response.data.data
                    this.pagination = response.data.meta
                    this.pagination.per_page = parseInt(response.data.meta.per_page)
                    this.loading_submit = false
                }).catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data 
                        console.log(error.response.data)
                    } else {
                        console.log(error.response)
                    }
                })
                .then(() => {
                    this.loading_submit = false
                    this.$eventHub.$emit('valueLoading', false)
                });

            },
            getQueryParameters() {
                return queryString.stringify({
                    page: this.pagination.current_page,
                    limit: this.limit,
                    ...this.search
                })
            },
            changeClearInput(){
                this.search.value = ''
                // this.getRecords()
            }, 
            cleanInputs(){
                this.initForm()
            }
        }
    }
</script>
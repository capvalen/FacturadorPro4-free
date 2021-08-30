<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <div class="card-header bg-info">
            <h3 class="my-0">Nueva Retención</h3>
        </div>
        <div class="card-body">
            <form autocomplete="off" @submit.prevent="submit">
                <div class="form-body">
                    <div class="row">
                        <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.establishment_id}">
                                <label class="control-label">Establecimiento</label>
                                <el-select v-model="form.establishment_id" @change="changeEstablishment">
                                    <el-option v-for="option in establishments" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.establishment_id" v-text="errors.establishment_id[0]"></small>
                            </div>
                        </div>
                        <!--<div class="col-lg-2">-->
                            <!--<div class="form-group" :class="{'has-danger': errors.document_type_id}">-->
                                <!--<label class="control-label">Tipo de comprobante</label>-->
                                <!--<el-select v-model="form.document_type_id" @change="changeDocumentType">-->
                                    <!--<el-option v-for="option in document_types" :key="option.id" :value="option.id" :label="option.description"></el-option>-->
                                <!--</el-select>-->
                                <!--<small class="form-control-feedback" v-if="errors.document_type_id" v-text="errors.document_type_id[0]"></small>-->
                            <!--</div>-->
                        <!--</div>-->
                        <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.series_id}">
                                <label class="control-label">Serie</label>
                                <el-select v-model="form.series_id">
                                    <el-option v-for="option in series" :key="option.id" :value="option.id" :label="option.number"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.series" v-text="errors.series[0]"></small>
                            </div>
                        </div>
                        <!--<div class="col-lg-2">-->
                            <!--<div class="form-group" :class="{'has-danger': errors.currency_type_id}">-->
                                <!--<label class="control-label">Moneda</label>-->
                                <!--<el-select v-model="form.currency_type_id" @change="changeCurrencyType">-->
                                    <!--<el-option v-for="option in currency_types" :key="option.id" :value="option.id" :label="option.description"></el-option>-->
                                <!--</el-select>-->
                                <!--<small class="form-control-feedback" v-if="errors.currency_type_id" v-text="errors.currency_type_id[0]"></small>-->
                            <!--</div>-->
                        <!--</div>-->
                        <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.date_of_issue}">
                                <label class="control-label">Fecha de emisión</label>
                                <el-date-picker v-model="form.date_of_issue" type="date" value-format="yyyy-MM-dd" :clearable="false" :picker-options="disabledDateOfIssue"></el-date-picker>
                                <small class="form-control-feedback" v-if="errors.date_of_issue" v-text="errors.date_of_issue[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group" :class="{'has-danger': errors.supplier_id}">
                                <label class="control-label">
                                    Proveedor
                                    <a href="#" @click.prevent="showDialogNewSupplier = true">[+ Nuevo]</a>
                                </label>
                                <el-select v-model="form.supplier_id" filterable>
                                    <el-option v-for="option in suppliers" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.supplier_id" v-text="errors.supplier_id[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.retention_type_id}">
                                <label class="control-label">Tipo de retención</label>
                                <el-select v-model="form.retention_type_id" @change="changeRetentionType">
                                    <el-option v-for="option in retention_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.retention_type_id" v-text="errors.retention_type_id[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group" :class="{'has-danger': errors.observations}">
                                <label class="control-label">Observaciones</label>
                                <el-input v-model="form.observations" type="textarea" autosize></el-input>
                                <small class="form-control-feedback" v-if="errors.observations" v-text="errors.observations[0]"></small>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-lg-2 col-md-6 d-flex align-items-end pt-2">
                            <div class="form-group">
                                <button type="button" class="btn waves-effect waves-light btn-primary" @click.prevent="showDialogAddDocument = true">+ Agregar Documento</button>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" v-if="form.documents.length > 0">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tipo de comprobante</th>
                                        <th>Comprobante</th>
                                        <th>Fec. Emisión</th>
                                        <th>Fec. Retención</th>
                                        <th>Moneda</th>
                                        <th class="text-right">T. Retención</th>
                                        <th class="text-right">T. Comprobante</th>
                                        <th class="text-right">T. A pagar</th>
                                        <th class="text-right">T. Pagado</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(row, index) in form.documents" :key="index">
                                        <td>{{ index+1 }}</td>
                                        <td><span v-text="row.document_type_description"></span></td>
                                        <td><span>{{row.series}}-{{row.number}}</span></td>
                                        <!-- <td><span v-text="row.document_type_id"></span></td> -->
                                        <td><span v-text="row.date_of_issue"></span></td>
                                        <td><span v-text="row.date_of_retention"></span></td>
                                        <td><span v-text="row.currency_type_id"></span></td>
                                        <td class="text-right">
                                            <span v-text="row.total_retention"></span>
                                        </td>
                                        <td class="text-right">
                                            <span v-text="row.total_document"></span>
                                        </td>
                                        <td class="text-right">
                                            <span v-text="row.total_to_pay"></span>
                                        </td>
                                        <td class="text-right">
                                            <span v-text="row.total_payment"></span>
                                        </td>
                                        <td class="text-right">
                                            <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickRemoveDocument(index)">x</button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <p class="text-right" v-if="form.total_retention > 0">Total Retención :   {{ form.total_retention }}</p>
                            <template v-if="form.total > 0">
                                <h3 class="text-right"><b>Total : </b>  {{ form.total }}</h3>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="form-actions text-right mt-4">
                    <el-button @click.prevent="close()">Cancelar</el-button>
                    <el-button type="primary" native-type="submit" :loading="loading_submit" v-if="form.documents.length > 0 && form.total > 0">Generar</el-button>
                </div>
            </form>
        </div>

        <retention-form-document :showDialog.sync="showDialogAddDocument"
                           :active-retention-type="activeRetentionType"
                           @add="addDocument"></retention-form-document>

        <supplier-form :showDialog.sync="showDialogNewSupplier"
                       type="suppliers"
                       :external="true"></supplier-form>

        <retention-options :showDialog.sync="showDialogOptions"
                            :recordId="recordId"
                            :showClose="false"></retention-options>
    </div>
</template>

<script>

    import RetentionFormDocument from './partials/document.vue'
    import SupplierForm from '../persons/form.vue'
    import RetentionOptions from './partials/options.vue'

    export default {
        components: {RetentionFormDocument, SupplierForm, RetentionOptions},
        data() {
            return {
                resource: 'retentions',
                showDialogAddDocument: false,
                showDialogNewSupplier: false,
                loading_submit: false,
                errors: {},
                form: {},  
                activeRetentionType:{},
                suppliers: [], 
                establishments: [],
                all_series: [],
                series: [],
                retention_types: [], 
                showDialogOptions: false,
                recordId: null,
                disabledDateOfIssue: {
                  disabledDate(time) {
                    return time.getTime() > moment().subtract(1, 'days');
                  }
                },
            }
        },
        created() {
            this.initForm()
            this.$http.get(`/${this.resource}/tables`)
                .then(response => {
 
                    this.suppliers = response.data.suppliers 
                    this.establishments = response.data.establishments
                    this.all_series = response.data.series
                    this.retention_types = response.data.retention_types 
                    this.form.establishment_id = (this.establishments.length > 0)?this.establishments[0].id:null
                    this.form.retention_type_id = (this.retention_types.length > 0)?this.retention_types[0].id:null
                    this.form.document_type_id = '20'
                    this.changeDocumentType()
                    this.changeRetentionType()

                })
            this.$eventHub.$on('reloadDataPersons', (supplier_id) => {
                this.reloadDataSuppliers(supplier_id)
            })
        },
        methods: {
            initForm() { 
                this.errors = {}
                this.form = {
                    id: null,
                    user_id: null,
                    establishment_id: null,
                    external_id: null,
                    soap_type_id: null,
                    state_type_id: '01',
                    ubl_version: '2.0',
                    document_type_id: null,
                    series_id: null,
                    number: '#',
                    date_of_issue: moment().subtract(1, 'days').format('YYYY-MM-DD'),
                    time_of_issue: moment().format('HH:mm:ss'),
                    supplier_id: null,
                    currency_type_id: null,
                    observations: null,
                    retention_type_id: null,
                    percent: 0,
                    total_retention: 0,
                    total: 0,
                    has_xml: 0,
                    has_pdf: 0,
                    has_cdr: 0,
                    documents: [],
                }
            }, 
            resetForm() {

                this.initForm()
                // this.form.soap_type_id = this.company.soap_type_id
                this.form.establishment_id = (this.establishments.length > 0)?this.establishments[0].id:null
                this.form.retention_type_id = (this.retention_types.length > 0)?this.retention_types[0].id:null
                this.form.document_type_id = '20'
                this.changeDocumentType()
                this.changeRetentionType()

            },
            async changeRetentionType(){
                let retention_type = await _.find(this.retention_types,{'id' : this.form.retention_type_id})
                this.activeRetentionType = retention_type
            },
            changeEstablishment() {
                this.filterSeries()
            },
            changeDocumentType() {
                // this.form.group_id = (this.form.document_type_id === '01000001')?'01':'02'
                this.filterSeries()
            },
            filterSeries() {
                this.series = _.filter(this.all_series, {'establishment_id': this.form.establishment_id,
                                                         'document_type_id': this.form.document_type_id})
                this.form.series_id = (this.series.length > 0)?this.series[0].id:null
            },
            addDocument(row) {
                this.form.documents.push(row);
                this.calculateTotal()
            },
            clickRemoveDocument(index) {
                this.form.documents.splice(index, 1)
                this.calculateTotal()  
            },
            changeCurrencyType() {
                this.currency_symbol = (this.form.currency_type_code === 'PEN')?'S/':'$'
            },
            calculateTotal() {

                let total = 0
                let total_retention = 0

                this.form.documents.forEach((row) => {
                    total += parseFloat(row.total_payment)
                    // total += parseFloat(row.total_document)
                    total_retention += parseFloat(row.total_retention)
                });

                this.form.total = _.round(total, 2)
                this.form.total_retention = _.round(total_retention, 2)
            },
            submit() {
                this.loading_submit = true
                this.$http.post(`/${this.resource}`, this.form)
                    .then(response => {
                        if (response.data.success) {
                            
                            this.resetForm()
                            this.recordId = response.data.data.id
                            this.showDialogOptions = true
                            // this.$message.success(response.data.message)
                            // location.href = '/retentions'

                        } else {
                            this.$message.error(response.data.message)
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            // console.log(error.response.data)
                            this.errors = error.response.data
                        } else {
                            this.$message.error(error.response.data.message)
                        }
                    })
                    .then(() => {
                        this.loading_submit = false
                    })
            },
            close() {
                location.href = '/retentions'
            },
            reloadDataSuppliers(supplier_id) {
                this.$http.get(`/${this.resource}/table/suppliers`).then((response) => {
                    this.suppliers = response.data
                    this.form.supplier_id = supplier_id
                })
            },
        }
    }
</script>
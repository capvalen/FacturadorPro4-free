<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <div class="card-header bg-info">
            <h3 class="my-0">Nueva Percepción</h3>
        </div>
        <div class="card-body">
            <form autocomplete="off" @submit.prevent="submit">
                <div class="form-body">
                    <div class="row">
                        <!-- <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.document_type_id}">
                                <label class="control-label">Tipo de comprobante</label>
                                <el-select v-model="form.document_type_id" @change="changeDocumentType">
                                    <el-option v-for="option in document_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.document_type_id" v-text="errors.document_type_id[0]"></small>
                            </div>
                        </div> -->
                        <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.establishment_id}">
                                <label class="control-label">Establecimiento</label>
                                <el-select v-model="form.establishment_id" @change="changeEstablishment">
                                    <el-option v-for="option in establishments" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.establishment_id" v-text="errors.establishment_id[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.series_id}">
                                <label class="control-label">Serie</label>
                                <el-select v-model="form.series_id">
                                    <el-option v-for="option in series" :key="option.id" :value="option.id" :label="option.number"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.series" v-text="errors.series[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.date_of_issue}">
                                <label class="control-label">Fecha de emisión</label>
                                <el-date-picker v-model="form.date_of_issue" type="date" value-format="yyyy-MM-dd" :clearable="false"></el-date-picker>
                                <small class="form-control-feedback" v-if="errors.date_of_issue" v-text="errors.date_of_issue[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group" :class="{'has-danger': errors.perception_type_id}">
                                <label class="control-label">Tipo de percepción</label>
                                <el-select v-model="form.perception_type_id" @change="changePerceptionType">
                                    <el-option v-for="option in perception_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.perception_type_id" v-text="errors.perception_type_id[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.currency_type_id}">
                                <label class="control-label">Moneda</label>
                                <el-select v-model="form.currency_type_id" @change="changeCurrencyType">
                                    <el-option v-for="option in currency_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.currency_type_id" v-text="errors.currency_type_id[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group" :class="{'has-danger': errors.customer_id}">
                                <label class="control-label">
                                    Cliente
                                    <a href="#" @click.prevent="showDialogNewCustomer = true">[+ Nuevo]</a>
                                </label>
                                <el-select v-model="form.customer_id" filterable>
                                    <el-option v-for="option in customers" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.customer_id" v-text="errors.customer_id[0]"></small>
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
                                        <th>Fec. Percepción</th>
                                        <th>Moneda</th>
                                        <th class="text-right">T. Percepción</th>
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
                                        <td><span v-text="row.date_of_perception"></span></td>
                                        <td><span v-text="row.currency_type_id"></span></td>
                                        <td class="text-right">
                                            <span v-text="row.total_perception"></span>
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
                            <p class="text-right" v-if="form.total_perception > 0">Total Percepción : {{ currency_symbol }} {{ form.total_perception }}</p>
                            <template v-if="form.total > 0">
                                <h3 class="text-right"><b>Total : </b>{{ currency_symbol }} {{ form.total }}</h3>
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

        <perception-form-document :showDialog.sync="showDialogAddDocument"
                           :active-perception-type="activePerceptionType"
                           @add="addDocument"></perception-form-document>

        <customer-form :showDialog.sync="showDialogNewCustomer"
                        type="customers"
                       :external="true"></customer-form>
    </div>
</template>

<script>

    import PerceptionFormDocument from './partials/document.vue'
    import CustomerForm from '../persons/form.vue'

    export default {
        components: {PerceptionFormDocument, CustomerForm},
        data() {
            return {
                resource: 'perceptions',
                showDialogAddDocument: false,
                showDialogNewCustomer: false,
                loading_submit: false,
                errors: {},
                activePerceptionType:{},
                form: {}, 
                document_types: [],
                currency_types: [],
                discounts: [],
                charges: [],
                documents: [],
                customers: [],
                company: null,
                establishments: [],
                perception_types: [],
                all_series: [],
                series: [],
                currency_symbol: 'S/',
            }
        },
        created() {
            this.initForm()
            this.$http.get(`/${this.resource}/tables`)
                .then(response => {
                    this.currency_types = response.data.currency_types
                    this.perception_types = response.data.perception_types
                    this.customers = response.data.customers
                    this.establishments = response.data.establishments
                    this.all_series = response.data.series
                    this.form.currency_type_id = (this.currency_types.length > 0)?this.currency_types[0].id:null
                    this.form.establishment_id = (this.establishments.length > 0)?this.establishments[0].id:null
                    this.form.perception_type_id = (this.perception_types.length > 0)?this.perception_types[0].id:null
                    this.changeDocumentType()
                    this.changePerceptionType()
                })
            this.$eventHub.$on('reloadDataPersons', (customer_id) => {
                this.reloadDataCustomers(customer_id)
            })
        },
        methods: {
            initForm() { 
                this.errors = {}
                this.form = {
                    id: null,
                    user_id: null,
                    establishment_id: null,
                    external_id: '-',
                    soap_type_id: null,
                    state_type_id: '01',
                    ubl_version: null,
                    document_type_id: '40',
                    series_id: null,
                    number: '#',
                    date_of_issue: moment().format('YYYY-MM-DD'),
                    time_of_issue: moment().format('HH:mm:ss'),
                    customer_id: null,
                    currency_type_id: null,
                    observations: null,
                    perception_type_id: '01',
                    percent: 0,
                    total_perception: 0,
                    total: 0,
                    has_xml: 0,
                    has_pdf: 0,
                    has_cdr: 0,
                    documents: [],
                }
            }, 
            resetForm() {
                this.initForm()
                this.form.soap_type_id = this.company.soap_type_id
                this.form.establishment_id = this.establishment.id
                this.changeDocumentType()
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
            async changePerceptionType(){
                let perception_type = await _.find(this.perception_types,{'id' : this.form.perception_type_id})
                this.activePerceptionType = perception_type
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
                let total_perception = 0

                this.form.documents.forEach((row) => {
                    total += parseFloat(row.total_document)
                    total_perception += parseFloat(row.total_perception)
                });

                this.form.total = _.round(total, 2)
                this.form.total_perception = _.round(total_perception, 2)

            },
            submit() {
                this.loading_submit = true
                this.$http.post(`/${this.resource}`, this.form)
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success(response.data.message)
                            location.href = '/perceptions'
                        } else {
                            this.$message.error(response.data.message)
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
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
                location.href = '/perceptions'
            },
            reloadDataCustomers(customer_id) {
                this.$http.get(`/${this.resource}/table/customers`).then((response) => {
                    this.customers = response.data
                    this.form.customer_id = customer_id
                })
            },
        }
    }
</script>
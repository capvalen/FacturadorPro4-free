<template>
    <div>
        <el-dialog :title="titleDialog" :visible="show" @open="create" width="50%"
                :close-on-click-modal="false"
                :close-on-press-escape="false"
                :show-close="false">
            <div class="row">
                <div class="col-lg-8">
                    <div class="form-group" :class="{'has-danger': errors.document_type_id}">
                        <label class="control-label">Tipo comprobante</label>
                        <el-select v-model="document.document_type_id" @change="changeDocumentType" popper-class="el-select-document_type" dusk="document_type_id" class="border-left rounded-left border-info">
                            <el-option v-for="option in document_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                        </el-select>
                        <small class="form-control-feedback" v-if="errors.document_type_id" v-text="errors.document_type_id[0]"></small>
                        <!-- <el-checkbox  v-model="generate_dispatch">Generar Guía Remisión</el-checkbox> -->
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group" :class="{'has-danger': errors.series_id}">
                        <label class="control-label">Serie</label>
                        <el-select v-model="document.series_id">
                            <el-option v-for="option in series" :key="option.id" :value="option.id" :label="option.number"></el-option>
                        </el-select>
                        <small class="form-control-feedback" v-if="errors.series_id" v-text="errors.series_id[0]"></small>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label class="control-label">Observaciones</label>
                        <el-input
                                type="textarea"
                                autosize
                                v-model="document.additional_information">
                        </el-input>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group" :class="{'has-danger': errors.payment_condition_id}">
                        <!--<label class="control-label">Fecha de emisión</label>-->
                        <label class="control-label">Condición de pago</label>
                        <el-select v-model="document.payment_condition_id" @change="changePaymentCondition" popper-class="el-select-document_type" dusk="document_type_id" style="max-width: 200px;">
                            <el-option value="02" label="Crédito"></el-option>
                            <el-option value="01" label="Contado"></el-option>
                        </el-select>
                        <small
                            class="form-control-feedback"
                            v-if="errors.date_of_due"
                            v-text="errors.date_of_due[0]"
                        ></small>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="form-group" :class="{'has-danger': errors.date_of_issue}">
                        <label class="control-label">Fecha de emisión</label>
                        <el-date-picker
                            readonly
                            v-model="document.date_of_issue"
                            type="date"
                            value-format="yyyy-MM-dd"
                            :clearable="false"
                            @change="changeDateOfIssue"
                        ></el-date-picker>
                        <small
                            class="form-control-feedback"
                            v-if="errors.date_of_issue"
                            v-text="errors.date_of_issue[0]"
                        ></small>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="form-group" :class="{'has-danger': errors.date_of_issue}">
                        <!--<label class="control-label">Fecha de emisión</label>-->
                        <label class="control-label">Fecha de vencimiento</label>
                        <el-date-picker
                            v-model="document.date_of_due"
                            type="date"
                            value-format="yyyy-MM-dd"
                            :clearable="false"
                        ></el-date-picker>
                        <small
                            class="form-control-feedback"
                            v-if="errors.date_of_due"
                            v-text="errors.date_of_due[0]"
                        ></small>
                    </div>
                </div>
                <br />
                <div class="col-lg-8 mt-3">
                    <div class="form-group" :class="{'has-danger': errors.dipatch_id}">
                        <!-- <label class="control-label">Tipo comprobante</label> -->
                        <el-checkbox  v-model="generate_dispatch">Generar Guía Remisión</el-checkbox>
                        <el-select v-model="dispatch_id" popper-class="el-select-document_type" filterable  class="border-left rounded-left border-info" v-if="generate_dispatch">
                            <el-option v-for="option in dispatches" :key="option.id" :value="option.id" :label="option.number_full"></el-option>
                        </el-select>
                        <small class="form-control-feedback" v-if="errors.dipatch_id" v-text="errors.dipatch_id[0]"></small>
                    </div>
                </div>

                <div class="col-lg-12" v-show="document.payment_condition_id === '02'">
                    <table v-if="document.fee.length>0" width="100%">
                        <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Monto</th>
                            <th style="width: 30px">
                                <a
                                    style="font-size:18px"
                                    href="#"
                                    @click.prevent="clickAddFee"
                                    class="text-center font-weight-bold text-center text-info"
                                >[+]</a>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(row, index) in document.fee" :key="index">
                            <td v-if="document.fee.length>0">
                                <div class="form-group mb-2 mr-2">
                                    <el-date-picker v-model="row.date" type="date"
                                                    value-format="yyyy-MM-dd"
                                                    format="dd/MM/yyyy"
                                                    :clearable="false"></el-date-picker>
                                </div>
                            </td>
                            <td v-if="document.fee.length>0">
                                <div class="form-group mb-2 mr-2">
                                    <el-input v-model="row.amount"></el-input>
                                </div>
                            </td>
                            <td class="series-table-actions text-center">
                                <button
                                    type="button"
                                    class="btn waves-effect waves-light btn-xs btn-danger"
                                    @click.prevent="clickRemoveFee(index)"
                                >
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-lg-12" v-show="document.payment_condition_id != '02'">
                    <table>
                        <thead>
                            <tr width="100%">
                                <th v-if="document.payments.length>0">M.Pago</th>
                                <th v-if="document.payments.length>0">Destino</th>
                                <th v-if="document.payments.length>0">Referencia</th>
                                <th v-if="document.payments.length>0">Monto</th>
                                <th width="5%">
                                    <a
                                        style="font-size:18px"
                                        href="#"
                                        @click.prevent="clickAddPayment"
                                        class="text-center font-weight-bold text-center text-info"
                                    >[+]</a>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row, index) in document.payments" :key="index">
                                <td>
                                    <div class="form-group mb-2 mr-2">
                                        <el-select v-model="row.payment_method_type_id">
                                            <el-option
                                                v-for="option in payment_method_types"
                                                :key="option.id"
                                                :value="option.id"
                                                :label="option.description"
                                            ></el-option>
                                        </el-select>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-2 mr-2">
                                        <el-select
                                            v-model="row.payment_destination_id"
                                            filterable
                                            :disabled="row.payment_destination_disabled"
                                        >
                                            <el-option
                                                v-for="option in payment_destinations"
                                                :key="option.id"
                                                :value="option.id"
                                                :label="option.description"
                                            ></el-option>
                                        </el-select>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-2 mr-2">
                                        <el-input v-model="row.reference"></el-input>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-2 mr-2">
                                        <el-input v-model="row.payment"></el-input>
                                    </div>
                                </td>
                                <td class="series-table-actions text-center">
                                    <button
                                        type="button"
                                        class="btn waves-effect waves-light btn-xs btn-danger"
                                        @click.prevent="clickCancel(index)"
                                    >
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                                <br />
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <span slot="footer" class="dialog-footer">
                <el-button @click="clickClose">Cerrar</el-button>
                <el-button class="submit" type="primary" @click="submit" :loading="loading_submit" v-if="flag_generate">Generar</el-button>
            </span>

            <document-options :showDialog.sync="showDialogDocumentOptions"
                              :recordId="documentNewId"
                              :generatDispatch="generate_dispatch"
                              :dispatchId="dispatch_id"
                              :isContingency="false"
                              :showClose="true"></document-options>
            <div class="col-lg-12" v-show="document.total > 0">
                <div class="form-group pull-right">
                    <label class="control-label"> Total </label> <br>
                    <label class="control-label">{{document.currency_type_id}} {{ document.total }}</label>
                </div>
                <br>
            </div>
        </el-dialog>
    </div>
</template>

<script>

    import DocumentOptions from '../../documents/partials/options.vue'
    import moment from "moment";

    export default {
        components: {DocumentOptions},

        props: ['show', 'recordId', 'showClose','showGenerate'],
        data() {
            return {
                titleDialog: null,
                loading: false,
                resource: 'sale-notes',
                resource_documents: 'documents',
                errors: {},
                form: {},
                document:{},
                document_types: [],
                all_document_types: [],
                all_series: [],
                series: [],
                generate:false,
                loading_submit:false,
                showDialogDocumentOptions: false,
                documentNewId: null,
                flag_generate:true,
                dispatches: [],
                generate_dispatch:false,
                dispatch_id:null,
                payment_destinations: [],
                payment_method_types: [],
                payment_condition_id: '01',
                fee: [],
            }
        },
        created() {
            this.initForm()
            this.initDocument()

           // console.log(moment().format('YYYY-MM-DD'))
        },
        methods: {
            changePaymentCondition() {
                this.document.fee = [];
                this.document.payments = [];
                if(this.document.payment_condition_id === '01') {
                    this.document.payments = this.form.sale_note.payments;
                    if(this.document.payments === undefined || this.document.payments.length < 1) {
                        this.clickAddPayment();
                    }
                }
                if(this.document.payment_condition_id === '02') {
                    this.document.fee = this.form.sale_note.fee;
                    if(this.document.fee === undefined || this.document.fee.length < 1){
                        this.clickAddFee();
                    }
                }
            },
            changeDateOfIssue() {
                this.document.date_of_due = this.document.date_of_issue;
            },
            clickRemoveFee(index) {
                this.document.fee.splice(index, 1);
                this.calculateFee();
            },
            clickAddFee() {
                if(this.document.fee === undefined)this.document.fee = [];
                this.document.fee.push({
                    id: null,
                    date: moment().format('YYYY-MM-DD'),
                    currency_type_id: this.document.currency_type_id,
                    amount: 0,
                });
                this.calculateFee();
            },
            calculateFee() {
                let fee_count = this.document.fee.length;
                let total = this.document.total;
                let accumulated = 0;
                let amount = _.round(total / fee_count, 2);
                _.forEach(this.document.fee, row => {
                    accumulated += amount;
                    if (total - accumulated < 0) {
                        amount = _.round(total - accumulated + amount, 2);
                    }
                    row.amount = amount;
                })
            },
            clickCancel(index) {
                this.document.payments.splice(index, 1);
            },
            clickAddPayment() {
                this.document.payments.push({
                    id: null,
                    document_id: null,
                    date_of_payment: moment().format("YYYY-MM-DD"),
                    payment_method_type_id: "01",
                    payment_destination_id: null,
                    reference: null,
                    payment: 0,
                });
            },
            initForm() {
                this.generate = (this.showGenerate) ? true:false
                this.errors = {}
                this.form = {
                    id: null,
                    external_id: null,
                    identifier: null,
                    date_of_issue:null,
                    sale_note:null,
                }
                this.generate_dispatch = false
            },
            initDocument(){
                this.document = {
                    document_type_id:null,
                    series_id:null,
                    establishment_id: null,
                    number: '#',
                    date_of_issue: null,
                    time_of_issue: null,
                    customer_id: null,
                    currency_type_id: null,
                    purchase_order: null,
                    exchange_rate_sale: 0,
                    total_prepayment: 0,
                    total_charge: 0,
                    total_discount: 0,
                    total_exportation: 0,
                    total_free: 0,
                    total_taxed: 0,
                    total_unaffected: 0,
                    total_exonerated: 0,
                    total_igv: 0,
                    total_base_isc: 0,
                    total_isc: 0,
                    total_base_other_taxes: 0,
                    total_other_taxes: 0,
                    total_taxes: 0,
                    total_value: 0,
                    total: 0,
                    operation_type_id: null,
                    date_of_due: null,
                    items: [],
                    charges: [],
                    discounts: [],
                    attributes: [],
                    guides: [],
                    additional_information:null,
                    actions: {
                        format_pdf:'a4',
                    },
                    quotation_id:null,
                    sale_note_id:null,
                    payments: [],
                    fee: [],
                    hotel: {},
                }
            },
            resetDocument(){
                this.generate = (this.showGenerate) ? true:false
                this.initDocument()
                this.document.document_type_id = (this.document_types.length > 0)?this.document_types[0].id:null
                this.changeDocumentType()
            },
            validatePaymentDestination(){

                let error_by_item = 0

                this.document.payments.forEach((item)=>{
                    if(item.payment_destination_id == null) error_by_item++;
                })

                return  {
                    error_by_item : error_by_item,
                }

            },
            async submit() {

                if(this.generate_dispatch){
                    if(!this.dispatch_id){
                        return this.$message.error('Debe seleccionar una guía base')
                    }
                }

                let validate_payment_destination = await this.validatePaymentDestination()

                if(validate_payment_destination.error_by_item > 0) {
                    return this.$message.error('El destino del pago es obligatorio');
                }

                this.loading_submit = true;

                this.document.exchange_rate_sale = 1;

                await this.$http.post(`/${this.resource_documents}`, this.document).then(response => {
                        if (response.data.success) {
                            this.documentNewId = response.data.data.id;
                            this.showDialogDocumentOptions = true;
                            this.$http.get(`/${this.resource}/changed/${this.form.id}`).then(() => {
                                this.$eventHub.$emit('reloadData');
                                this.flag_generate = false
                            });
                            this.resetDocument()

                            this.$emit('update:show', false)
                        } else {
                            this.$message.error(response.data.message);
                        }
                    }).catch(error => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data;
                        }
                        else {
                            this.$message.error(error.response.data.message);
                        }
                    }).then(() => {
                        this.loading_submit = false;
                        $.each($('.v-modal'),function(a,b){
                            /* v-modal se le resta 5 z-index para que no se sobreponga en el modal*/
                            $(b).css('z-index', $(b).css('z-index') - 5);
                        })
                    });
            },
            assignDocument(){
                let q = this.form.sale_note;
                // console.log(q);

                this.document.establishment_id = q.establishment_id
                this.document.date_of_issue =  moment().format('YYYY-MM-DD')//q.date_of_issue
                this.document.date_of_due = moment().format('YYYY-MM-DD') //q.date_of_issue
                this.document.time_of_issue = q.time_of_issue
                this.document.customer_id = q.customer_id
                this.document.currency_type_id = q.currency_type_id
                this.document.purchase_order = null
                this.document.exchange_rate_sale = q.exchange_rate_sale
                this.document.total_prepayment = q.total_prepayment
                this.document.total_charge = q.total_charge
                this.document.total_discount = q.total_discount
                this.document.total_exportation = q.total_exportation
                this.document.total_free = q.total_free
                this.document.total_taxed = q.total_taxed
                this.document.total_unaffected = q.total_unaffected
                this.document.total_exonerated = q.total_exonerated
                this.document.total_igv = q.total_igv
                this.document.total_base_isc = q.total_base_isc
                this.document.total_isc = q.total_isc
                this.document.total_base_other_taxes = q.total_base_other_taxes
                this.document.total_other_taxes = q.total_other_taxes
                this.document.total_taxes = q.total_taxes
                this.document.total_value = q.total_value
                this.document.total = q.total
                this.document.operation_type_id = '0101'

                this.document.items = q.items
                this.document.purchase_order = q.purchase_order || ''
                this.document.additional_information = q.observation || ''
                this.document.charges = q.charges
                this.document.discounts = q.discounts
                this.document.attributes = []
                this.document.guides = q.guides;
                this.document.actions = {
                    format_pdf : 'a4'
                };
                this.document.sale_note_id = this.form.id;
                this.document.payments = q.payments;
                this.document.seller_id = q.user_id;
                this.document.user_id = q.user_id;
                this.document.fee = [];
                this.document.payment_condition_id =q.payment_condition_id;
                if(this.document.payment_condition_id === undefined || this.document.payments.length > 0) {
                    this.document.payment_condition_id = "01";
                }
                //console.log(this.document);
            },
            async create() {

                await this.$http.get(`/${this.resource}/option/tables`).then(response => {
                    this.all_document_types = response.data.document_types_invoice;
                    this.all_series = response.data.series;
                    this.payment_destinations = response.data.payment_destinations;
                    this.payment_method_types = response.data.payment_method_types;
                    // this.document.document_type_id = (this.document_types.length > 0)?this.document_types[0].id:null;
                    // this.changeDocumentType();
                });

                await this.$http.get(`/${this.resource}/record/${this.recordId}`)
                    .then(response => {
                        this.form = response.data.data
                        this.validateIdentityDocumentType()

                        this.assignDocument();
                        this.titleDialog = 'Nota de venta registrada: '+this.form.identifier
                    })


                await this.$http.get(`/${this.resource}/dispatches`)
                    .then(response => {
                        this.dispatches = response.data
                    })
            },
            changeDocumentType() {
                this.filterSeries();
            },
            async validateIdentityDocumentType(){

                let identity_document_types = ['0','1']


                if(identity_document_types.includes(this.form.sale_note.customer.identity_document_type_id)){

                    this.document_types = _.filter(this.all_document_types,{'id':'03'})

                }else{
                    this.document_types = this.all_document_types

                }

                this.document.document_type_id = (this.document_types.length > 0)?this.document_types[0].id:null
                await this.changeDocumentType()

            },
            filterSeries() {
                this.document.series_id = null
                this.series = _.filter(this.all_series, {'document_type_id': this.document.document_type_id})
                this.document.series_id = (this.series.length > 0)?this.series[0].id:null
            },
            clickFinalize() {
                location.href = `/${this.resource}`
            },
            clickNewQuotation() {
                this.clickClose()
            },
            clickClose() {
                this.$emit('update:showDialog', false)
                this.initForm()
                this.resetDocument()
                this.flag_generate = true
            },
            clickToPrint(){
                window.open(`/downloads/saleNote/sale_note/${this.form.external_id}`, '_blank');
            }
        }
    }
</script>

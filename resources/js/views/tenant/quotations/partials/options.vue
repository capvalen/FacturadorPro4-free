<template>
    <div>
        <el-dialog
            :title="titleDialog"
            :visible="showDialog"
            @open="create"
            width="50%"
            :close-on-click-modal="false"
            :close-on-press-escape="false"
            :show-close="false"
        >
            <div class="row" v-show="!showGenerate">
                <div class="col-lg-4 col-md-4 col-sm-4 text-center font-weight-bold">
                    <p>Imprimir A4</p>
                    <button
                        type="button"
                        class="btn btn-lg btn-info waves-effect waves-light"
                        @click="clickToPrint('a4')"
                    >
                        <i class="fa fa-file-alt"></i>
                    </button>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4 text-center font-weight-bold">
                    <p>Imprimir A5</p>
                    <button
                        type="button"
                        class="btn btn-lg btn-info waves-effect waves-light"
                        @click="clickToPrint('a5')"
                    >
                        <i class="fa fa-file-alt"></i>
                    </button>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4 text-center font-weight-bold">
                    <p>Imprimir Ticket</p>
                    <button
                        type="button"
                        class="btn btn-lg btn-info waves-effect waves-light"
                        @click="clickToPrint('ticket')"
                    >
                        <i class="fa fa-receipt"></i>
                    </button>
                </div>
            </div>
            <br />
            <div class="row" v-show="!showGenerate">
                <div class="col-md-12">
                    <el-input v-model="customer_email">
                        <el-button
                            slot="append"
                            icon="el-icon-message"
                            @click="clickSendEmail"
                            :loading="loading"
                        >Enviar</el-button>
                    </el-input>
                    <!--<small class="form-control-feedback" v-if="errors.customer_email" v-text="errors.customer_email[0]"></small> -->
                </div>
            </div>
            <br />
            <div class="row" v-if="typeUser == 'admin'">
                <div class="col-md-9" v-show="!showGenerate">
                    <div class="form-group">
                        <el-checkbox v-model="generate">Generar comprobante electrónico</el-checkbox>
                    </div>
                </div>
            </div>
            <div class="row" v-if="generate">
                <div class="col-lg-12 pb-2">
                    <div class="form-group">
                        <label class="control-label font-weight-bold text-info">Cliente</label>
                        <el-select
                            v-model="document.customer_id"
                            filterable
                            remote
                            class="border-left rounded-left border-info"
                            popper-class="el-select-customers"
                            dusk="customer_id"
                            placeholder="Escriba el nombre o número de documento del cliente"
                            :remote-method="searchRemoteCustomers"
                            @change="changeCustomer"
                            :loading="loading_search"
                        >
                            <el-option
                                v-for="option in customers"
                                :key="option.id"
                                :value="option.id"
                                :label="option.description"
                            ></el-option>
                        </el-select>
                        <small
                            class="form-control-feedback"
                            v-if="errors.customer_id"
                            v-text="errors.customer_id[0]"
                        ></small>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="form-group" :class="{'has-danger': errors.document_type_id}">
                        <label class="control-label">Tipo comprobante</label>
                        <el-select
                            v-model="document.document_type_id"
                            @change="changeDocumentType"
                            popper-class="el-select-document_type"
                            dusk="document_type_id"
                            class="border-left rounded-left border-info"
                        >
                            <el-option
                                v-for="option in document_types"
                                :key="option.id"
                                :value="option.id"
                                :label="option.description"
                            ></el-option>
                            <el-option key="nv" value="nv" label="NOTA DE VENTA"></el-option>
                        </el-select>
                        <small
                            class="form-control-feedback"
                            v-if="errors.document_type_id"
                            v-text="errors.document_type_id[0]"
                        ></small>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group" :class="{'has-danger': errors.series_id}">
                        <label class="control-label">Serie</label>
                        <el-select v-model="document.series_id">
                            <el-option
                                v-for="option in series"
                                :key="option.id"
                                :value="option.id"
                                :label="option.number"
                            ></el-option>
                        </el-select>
                        <small
                            class="form-control-feedback"
                            v-if="errors.series_id"
                            v-text="errors.series_id[0]"
                        ></small>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="form-group" :class="{'has-danger': errors.payment_condition_id}">
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
                <div class="col-lg-4">
                    <div class="form-group" v-show="document.document_type_id == '03'">
                        <el-checkbox
                            v-model="document.is_receivable"
                            class="font-weight-bold"
                        >¿Es venta por cobrar?</el-checkbox>
                    </div>
                </div>
                <br />
                <div class="col-lg-12" v-show="is_document_type_invoice && document.payment_condition_id === '02'">
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
                <div class="col-lg-12" v-show="is_document_type_invoice && document.payment_condition_id != '02'">
                    <table>
                        <thead>
                            <tr width="100%">
                                <th v-if="document.payments.length>0">F.Pago</th>
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
                                    <el-date-picker
                                        v-model="row.date_of_payment"
                                        type="date"
                                        value-format="yyyy-MM-dd"
                                        :clearable="false"
                                    ></el-date-picker>
                                </td>
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

            <series-form v-if="generate && form.quotation" :items="form.quotation.items"></series-form>
            <div class="col-lg-12" v-show="document.total > 0">
                <div class="form-group pull-right" >
                    <label class="control-label"> Total </label> <br>
                    <label class="control-label">{{document.currency_type_id}} {{ document.total }}</label>
                </div>

                <br>
            </div>
            <span slot="footer" class="dialog-footer">
                <template v-if="showClose">
                    <el-button @click="clickClose">Cerrar</el-button>
                    <el-button
                        class="submit"
                        type="primary"
                        @click="submit"
                        :loading="loading_submit"
                        v-if="generate"
                    >Generar</el-button>
                </template>
                <template v-else>
                    <el-button
                        class="submit"
                        type="primary"
                        plain
                        @click="submit"
                        :loading="loading_submit"
                        v-if="generate"
                    >Generar comprobante</el-button>
                    <el-button @click="clickFinalize" v-else>Ir al listado</el-button>
                    <el-button type="primary" @click="clickNewQuotation">Nueva cotización</el-button>
                </template>
            </span>
        </el-dialog>

        <document-options
            :showDialog.sync="showDialogDocumentOptions"
            :recordId="documentNewId"
            :isContingency="false"
            :showClose="true"
        ></document-options>

        <sale-note-options
            :showDialog.sync="showDialogSaleNoteOptions"
            :recordId="documentNewId"
            :showClose="true"
        ></sale-note-options>
    </div>
</template>

<script>
import DocumentOptions from "../../documents/partials/options.vue";
import SaleNoteOptions from "../../sale_notes/partials/options.vue";
import SeriesForm from "./series_form.vue";
import moment from "moment";

export default {
    components: { DocumentOptions, SaleNoteOptions, SeriesForm },

    props: [
        "showDialog",
        "recordId",
        "showClose",
        "showGenerate",
        "type",
        "typeUser",
    ],
    data() {
        return {
            customer_email: "",
            titleDialog: null,
            loading: false,
            resource: "quotations",
            resource_documents: "documents",
            errors: {},
            form: {},
            document: {},
            document_types: [],
            all_document_types: [],
            all_series: [],
            series: [],
            customers: [],
            generate: false,
            loading_submit: false,
            showDialogDocumentOptions: false,
            showDialogSaleNoteOptions: false,
            documentNewId: null,
            is_document_type_invoice: true,
            payment_destinations: [],
            loading_search: false,
            payment_method_types: [],
        };
    },
    created() {
        this.initForm();
        this.initDocument();
        this.clickAddPayment();
    },
    methods: {
        changePaymentCondition() {
            this.document.fee = [];
            this.document.payments = [];
            if(this.document.payment_condition_id === '01') {
                this.document.payments = this.form.quotation.payments;
                if(this.document.payments === undefined || this.document.payments.length < 1) {
                    this.clickAddPayment();
                }
            }
            if(this.document.payment_condition_id === '02') {
                this.document.fee = this.form.quotation.fee;
                if(this.document.fee === undefined || this.document.fee.length < 1){
                    this.clickAddFee();
                }
            }
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
            if(this.document.payments === undefined) this.document.payments = [];
            this.document.payments.push({
                id: null,
                document_id: null,
                date_of_payment: moment().format("YYYY-MM-DD"),
                payment_method_type_id: "01",
                payment_destination_id: null,
                reference: null,
                payment: parseFloat(this.form.quotation.total),
            });
        },
        initForm() {
            this.generate = this.showGenerate ? true : false;
            this.errors = {};
            this.form = {
                id: null,
                external_id: null,
                identifier: null,
                date_of_issue: null,
                quotation: null,
            };
        },
        getCustomer() {
            this.$http
                .get(
                    `/${this.resource_documents}/search/customer/${this.form.quotation.customer_id}`
                )
                .then((response) => {
                    this.customers = response.data.customers;
                    this.document.customer_id = this.form.quotation.customer_id;
                    this.changeCustomer();
                });
        },
        changeCustomer() {
            this.validateIdentityDocumentType();
        },
        searchRemoteCustomers(input) {
            if (input.length > 0) {
                this.loading_search = true;
                let parameters = `input=${input}&document_type_id=${this.form.document_type_id}&operation_type_id=${this.form.operation_type_id}`;

                this.$http
                    .get(`/${this.resource}/search/customers?${parameters}`)
                    .then((response) => {
                        this.customers = response.data.customers;
                        this.loading_search = false;
                    });
            }
        },
        initDocument() {
            this.document = {
                document_type_id: null,
                series_id: null,
                establishment_id: null,
                number: "#",
                date_of_issue: moment().format("YYYY-MM-DD"),
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
                payment_condition_id: '01',
                fee: [],
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
                date_of_due: moment().format("YYYY-MM-DD"),
                items: [],
                charges: [],
                discounts: [],
                attributes: [],
                guides: [],
                additional_information: null,
                actions: {
                    format_pdf: "a4",
                },
                quotation_id: null,
                is_receivable: false,
                payments: [],
                hotel: {},
            };
        },
        changeDateOfIssue() {
            this.document.date_of_due = this.document.date_of_issue;
        },
        resetDocument() {
            this.generate = this.showGenerate ? true : false;
            this.initDocument();
            this.document.document_type_id =
                this.document_types.length > 0
                    ? this.document_types[0].id
                    : null;
            this.changeDocumentType();
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

            let validate_items = await this.validateQuantityandSeries();
            if (!validate_items.success)
                return this.$message.error(validate_items.message);

            await this.assignDocument();

            let validate_payment_destination = await this.validatePaymentDestination()

            if(validate_payment_destination.error_by_item > 0) {
                return this.$message.error('El destino del pago es obligatorio');
            }

            this.loading_submit = true;

            if (this.document.document_type_id === "nv") {
                this.document.prefix = "NV";
                this.resource_documents = "sale-notes";
            } else {
                this.document.prefix = null;
                this.resource_documents = "documents";
            }

            this.$http
                .post(`/${this.resource_documents}`, this.document)
                .then((response) => {
                    if (response.data.success) {
                        this.documentNewId = response.data.data.id;

                        this.$http
                            .get(`/${this.resource}/changed/${this.form.id}`)
                            .then(() => {
                                this.$eventHub.$emit("reloadData");
                            });
                        // console.log(this.document.document_type_id)
                        if (this.document.document_type_id === "nv") {
                            this.showDialogSaleNoteOptions = true;
                        } else {
                            this.showDialogDocumentOptions = true;
                        }

                        this.$eventHub.$emit("reloadData");
                        this.resetDocument();
                        this.document.customer_id = this.form.quotation.customer_id;
                        this.changeCustomer();
                    } else {
                        this.$message.error(response.data.message);
                    }
                })
                .catch((error) => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data;
                    } else {
                        this.$message.error(error.response.data.message);
                    }
                })
                .then(() => {
                    this.loading_submit = false;
                });
        },
        assignDocument() {
            let q = this.form.quotation;

            this.document.establishment_id = q.establishment_id;
            // this.document.date_of_issue = q.date_of_issue
            this.document.time_of_issue = moment().format("HH:mm:ss");
            // this.document.customer_id = q.customer_id
            this.document.currency_type_id = q.currency_type_id;
            this.document.purchase_order = null;
            this.document.exchange_rate_sale = q.exchange_rate_sale;
            this.document.total_prepayment = q.total_prepayment;
            this.document.total_charge = q.total_charge;
            this.document.total_discount = q.total_discount;
            this.document.total_exportation = q.total_exportation;
            this.document.total_free = q.total_free;
            this.document.total_taxed = q.total_taxed;
            this.document.total_unaffected = q.total_unaffected;
            this.document.total_exonerated = q.total_exonerated;
            this.document.total_igv = q.total_igv;
            this.document.total_base_isc = q.total_base_isc;
            this.document.total_isc = q.total_isc;
            this.document.total_base_other_taxes = q.total_base_other_taxes;
            this.document.total_other_taxes = q.total_other_taxes;
            this.document.total_taxes = q.total_taxes;
            this.document.total_value = q.total_value;
            this.document.total = q.total;
            this.document.operation_type_id = "0101";
            // this.document.date_of_due = q.date_of_issue
            this.document.items = q.items;
            this.document.charges = q.charges;
            this.document.discounts = q.discounts;
            this.document.attributes = [];
            // this.document.payments = q.payments;
            this.document.guides = q.guides;
            this.document.additional_information = null;
            this.document.actions = {
                format_pdf: "a4",
            };
            this.document.quotation_id = this.form.id;
            _.forEach(this.document.items, row => {
                row.name_product_pdf = row.item.name_product_pdf;
            })
        },
        async create() {
            await this.$http
                .get(`/${this.resource}/option/tables`)
                .then((response) => {
                    this.all_document_types =
                        response.data.document_types_invoice;
                    this.all_series = response.data.series;
                    this.payment_destinations =
                        response.data.payment_destinations;
                    this.payment_method_types =
                        response.data.payment_method_types;
                    // this.document.document_type_id = (this.all_document_types.length > 0)?this.all_document_types[0].id:null
                    // this.changeDocumentType()
                });

            await this.$http
                .get(`/${this.resource}/record2/${this.recordId}`)
                .then((response) => {
                    this.form = response.data.data;
                    this.document.payments =
                        response.data.data.quotation.payments;
                    this.document.total = this.form.quotation.total;
                    this.document.currency_type_id =this.form.quotation.currency_type_id;
                    this.document.payment_condition_id =this.form.quotation.payment_condition_id;
                    if(this.document.payment_condition_id === undefined || this.document.payments.length > 0) {
                        this.document.payment_condition_id = "01";
                    }

                     // console.log(this.form)
                    // this.validateIdentityDocumentType()
                    this.getCustomer();
                    let type = this.type == "edit" ? "editada" : "registrada";
                    this.titleDialog =
                        `Cotización ${type}: ` + this.form.identifier;
                });
        },
        changeDocumentType() {
            // this.filterSeries()
            this.document.is_receivable = false;
            this.series = [];
            if (this.document.document_type_id !== "nv") {
                this.filterSeries();
                this.is_document_type_invoice = true;
            } else {
                this.series = _.filter(this.all_series, {
                    document_type_id: "80",
                });
                this.document.series_id =
                    this.series.length > 0 ? this.series[0].id : null;

                this.is_document_type_invoice = false;
            }
        },
        async validateIdentityDocumentType() {
            let identity_document_types = ["0", "1"];
            // console.log(this.document)
            let customer = _.find(this.customers, {
                id: this.document.customer_id,
            });

            if (
                identity_document_types.includes(
                    customer.identity_document_type_id
                )
            ) {
                this.document_types = _.filter(this.all_document_types, {
                    id: "03",
                });
            } else {
                this.document_types = this.all_document_types;
            }

            this.document.document_type_id =
                this.document_types.length > 0
                    ? this.document_types[0].id
                    : null;
            await this.changeDocumentType();
        },
        filterSeries() {
            this.document.series_id = null;
            this.series = _.filter(this.all_series, {
                document_type_id: this.document.document_type_id,
            });
            this.document.series_id =
                this.series.length > 0 ? this.series[0].id : null;
        },
        clickFinalize() {
            location.href = `/${this.resource}`;
        },
        clickNewQuotation() {
            this.clickClose();
        },
        clickClose() {
            this.$emit("update:showDialog", false);
            this.initForm();
            this.resetDocument();
        },
        clickToPrint(format) {
            window.open(
                `/${this.resource}/print/${this.form.external_id}/${format}`,
                "_blank"
            );
        },
        clickSendEmail() {
            this.loading = true;
            this.$http
                .post(`/${this.resource}/email`, {
                    customer_email: this.customer_email,
                    id: this.form.id,
                    customer_id: this.form.quotation.customer_id,
                })
                .then((response) => {
                    if (response.data.success) {
                        this.$message.success(
                            "El correo fue enviado satisfactoriamente"
                        );
                    } else {
                        this.$message.error("Error al enviar el correo");
                    }
                })
                .catch((error) => {
                    this.$message.error("Error al enviar el correo");
                })
                .then(() => {
                    this.loading = false;
                });
        },
        async validateQuantityandSeries() {
            let error = 0;
            await this.form.quotation.items.forEach((element) => {
                if (element.item.series_enabled) {
                    const select_lots = _.filter(element.item.lots, {
                        has_sale: true,
                    }).length;
                    if (select_lots != element.quantity) error++;
                }
            });
            if (error > 0)
                return {
                    success: false,
                    message:
                        "Las cantidades y series seleccionadas deben ser iguales.",
                };

            return { success: true };
        },
    },
};
</script>

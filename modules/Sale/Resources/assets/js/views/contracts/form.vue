<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <!-- <div class="card-header bg-info">
            <h3 class="my-0">Nuevo Comprobante</h3>
        </div> -->
        <div class="tab-content" v-if="loading_form">
            <div class="invoice">
                <header class="clearfix">
                    <div class="row">
                        <div class="col-sm-2 text-center mt-3 mb-0">
                            <logo url="/" :path_logo="(company.logo != null) ? `/storage/uploads/logos/${company.logo}` : ''" ></logo>
                        </div>
                        <div class="col-sm-6 text-left mt-3 mb-0">
                            <address class="ib mr-2" >
                                <span class="font-weight-bold d-block">CONTRATO</span>
                                <span class="font-weight-bold d-block">CNT-XXX</span>
                                <span class="font-weight-bold">{{company.name}}</span>
                                <br>
                                <div v-if="establishment.address != '-'">{{ establishment.address }}, </div> {{ establishment.district.description }}, {{ establishment.province.description }}, {{ establishment.department.description }} - {{ establishment.country.description }}
                                <br>
                                {{establishment.email}} - <span v-if="establishment.telephone != '-'">{{establishment.telephone}}</span>
                            </address>
                        </div>
                        <div class="col-sm-4">
                            <!-- <el-checkbox class="mt-3" v-model="form.active_terms_condition" @change="changeTermsCondition">Términos y condiciones del contrato</el-checkbox> -->
                        </div>
                    </div>
                </header>
                <form autocomplete="off" @submit.prevent="submit">
                    <div class="form-body">
                        <div class="row mt-1">
                             <div class="col-lg-6 pb-2">
                                <div class="form-group" :class="{'has-danger': errors.customer_id}">
                                    <label class="control-label font-weight-bold text-info">
                                        Cliente
                                        <a href="#" @click.prevent="showDialogNewPerson = true">[+ Nuevo]</a>
                                    </label>
                                    <el-select v-model="form.customer_id" filterable remote class="border-left rounded-left border-info" popper-class="el-select-customers"
                                        dusk="customer_id"
                                        placeholder="Escriba el nombre o número de documento del cliente"
                                        :remote-method="searchRemoteCustomers"
                                        :loading="loading_search">

                                        <el-option v-for="option in customers" :key="option.id" :value="option.id" :label="option.description"></el-option>

                                    </el-select>
                                    <small class="form-control-feedback" v-if="errors.customer_id" v-text="errors.customer_id[0]"></small>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group" :class="{'has-danger': errors.date_of_issue}">
                                    <!--<label class="control-label">Fecha de emisión</label>-->
                                    <label class="control-label">Fec. Emisión</label>
                                    <el-date-picker v-model="form.date_of_issue" type="date" value-format="yyyy-MM-dd" :clearable="false" @change="changeDateOfIssue"></el-date-picker>
                                    <small class="form-control-feedback" v-if="errors.date_of_issue" v-text="errors.date_of_issue[0]"></small>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group" :class="{'has-danger': errors.date_of_due}">
                                    <label class="control-label">Fec. Vencimiento</label>
                                    <el-date-picker v-model="form.date_of_due" type="date" value-format="yyyy-MM-dd" :clearable="true"></el-date-picker>
                                    <small class="form-control-feedback" v-if="errors.date_of_due" v-text="errors.date_of_due[0]"></small>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group" :class="{'has-danger': errors.delivery_date}">
                                    <label class="control-label">Fec. Entrega
                                        <el-tooltip class="item" effect="dark" content="Fecha de entrega de proyecto" placement="top-end">
                                            <i class="fa fa-info-circle"></i>
                                        </el-tooltip>
                                    </label>
                                    <el-date-picker v-model="form.delivery_date" type="date" value-format="yyyy-MM-dd" :clearable="true"></el-date-picker>
                                    <small class="form-control-feedback" v-if="errors.delivery_date" v-text="errors.delivery_date[0]"></small>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group" >
                                    <label class="control-label">Dirección de envío
                                    </label>
                                    <el-input v-model="form.shipping_address"></el-input>
                                    <small class="form-control-feedback" v-if="errors.shipping_address" v-text="errors.shipping_address[0]"></small>
                                </div>
                            </div>

                            <div class="col-lg-2">
                                <div class="form-group" :class="{'has-danger': errors.payment_method_type_id}">
                                    <label class="control-label">
                                        Término de pago
                                    </label>
                                    <el-select v-model="form.payment_method_type_id" filterable @change="changePaymentMethodType">
                                        <el-option v-for="option in payment_method_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                    </el-select>
                                    <small class="form-control-feedback" v-if="errors.payment_method_type_id" v-text="errors.payment_method_type_id[0]"></small>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group" >
                                    <label class="control-label">Número de cuenta
                                    </label>
                                    <el-input v-model="form.account_number"></el-input>
                                    <small class="form-control-feedback" v-if="errors.account_number" v-text="errors.account_number[0]"></small>
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
                            <div class="col-lg-2">
                                <div class="form-group" :class="{'has-danger': errors.exchange_rate_sale}">
                                    <label class="control-label">Tipo de cambio
                                        <el-tooltip class="item" effect="dark" content="Tipo de cambio del día, extraído de SUNAT" placement="top-end">
                                            <i class="fa fa-info-circle"></i>
                                        </el-tooltip>
                                    </label>
                                    <el-input v-model="form.exchange_rate_sale"></el-input>
                                    <small class="form-control-feedback" v-if="errors.exchange_rate_sale" v-text="errors.exchange_rate_sale[0]"></small>
                                </div>
                            </div>
                            <div class="col-12 pt-3">
                                <div class="row">
                                    <div class="form-group col-12 col-md-2">
                                        <label>Vendedor</label>
                                        <el-select v-model="form.seller_id" clearable>
                                            <el-option v-for="sel in sellers" :key="sel.id" :value="sel.id" :label="sel.name">{{ sel.name }}</el-option>
                                        </el-select>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group" :class="{'has-danger': errors.exchange_rate_sale}">
                                            <label class="control-label">Descripcion</label>
                                            <el-input  type="textarea"  :rows="3" v-model="form.description"></el-input>
                                            <small class="form-control-feedback" v-if="errors.description" v-text="errors.description[0]"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-8 mt-2" v-if="showPayments && !form.quotation_id">

                                <table>
                                    <thead>
                                        <tr width="100%">
                                            <th v-if="form.payments.length>0" class="pb-2">Método de pago</th>
                                            <th v-if="form.payments.length>0" class="pb-2">Destino</th>
                                            <th v-if="form.payments.length>0" class="pb-2">Referencia</th>
                                            <th v-if="form.payments.length>0" class="pb-2">Monto</th>
                                            <th width="15%"><a href="#" @click.prevent="clickAddPayment" class="text-center font-weight-bold text-info">[+ Agregar]</a></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(row, index) in form.payments" :key="index">
                                            <td>
                                                <div class="form-group mb-2 mr-2">
                                                    <el-select v-model="row.payment_method_type_id" >
                                                        <el-option v-for="option in payment_method_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                                    </el-select>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group mb-2 mr-2">
                                                    <el-select v-model="row.payment_destination_id" filterable >
                                                        <el-option v-for="option in payment_destinations" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                                    </el-select>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group mb-2 mr-2"  >
                                                    <el-input v-model="row.reference"></el-input>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group mb-2 mr-2" >
                                                    <el-input v-model="row.payment"></el-input>
                                                </div>
                                            </td>
                                            <td class="series-table-actions text-center">
                                                <button  type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickCancel(index)">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                            <br>
                                        </tr>
                                    </tbody>
                                </table>


                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th class="font-weight-bold">Descripción</th>
                                                <th class="text-center font-weight-bold">Unidad</th>
                                                <th class="text-right font-weight-bold">Cantidad</th>
                                                <th class="text-right font-weight-bold">Valor Unitario</th>
                                                <th class="text-right font-weight-bold">Precio Unitario</th>
                                                <th class="text-right font-weight-bold">Subtotal</th>
                                                <!--<th class="text-right font-weight-bold">Cargo</th>-->
                                                <th class="text-right font-weight-bold">Total</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody v-if="form.items.length > 0">
                                            <tr v-for="(row, index) in form.items" :key="index">
                                                <td>{{index + 1}}</td>
                                                <td>{{row.item.description}} {{row.item.presentation.hasOwnProperty('description') ? row.item.presentation.description : ''}}<br/><small>{{row.affectation_igv_type.description}}</small></td>
                                                <td class="text-center">{{row.item.unit_type_id}}</td>
                                                <td class="text-right">{{row.quantity}}</td>
                                                <!-- <td class="text-right">{{currency_type.symbol}} {{row.unit_price}}</td> -->
                                                <td class="text-right">{{currency_type.symbol}} {{getFormatUnitPriceRow(row.unit_value)}}</td>
                                                <td class="text-right">{{ currency_type.symbol }} {{ getFormatUnitPriceRow(row.unit_price) }}</td>

                                                <td class="text-right">{{currency_type.symbol}} {{row.total_value}}</td>
                                                <!--<td class="text-right">{{ currency_type.symbol }} {{ row.total_charge }}</td>-->
                                                <td class="text-right">{{currency_type.symbol}} {{row.total}}</td>
                                                <td class="text-right">
                                                    <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickRemoveItem(index)">x</button>
                                                </td>
                                            </tr>
                                            <tr><td colspan="9"></td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-6 d-flex align-items-end">
                                <div class="form-group">
                                    <button type="button" class="btn waves-effect waves-light btn-primary" @click.prevent="showDialogAddItem = true">+ Agregar Producto</button>
                                </div>
                            </div>

                            <div class="col-md-8 mt-3">

                            </div>

                            <div class="col-md-4">
                                <p class="text-right" v-if="form.total_exportation > 0">OP.EXPORTACIÓN: {{ currency_type.symbol }} {{ form.total_exportation }}</p>
                                <p class="text-right" v-if="form.total_free > 0">OP.GRATUITAS: {{ currency_type.symbol }} {{ form.total_free }}</p>
                                <p class="text-right" v-if="form.total_unaffected > 0">OP.INAFECTAS: {{ currency_type.symbol }} {{ form.total_unaffected }}</p>
                                <p class="text-right" v-if="form.total_exonerated > 0">OP.EXONERADAS: {{ currency_type.symbol }} {{ form.total_exonerated }}</p>
                                <p class="text-right" v-if="form.total_taxed > 0">OP.GRAVADA: {{ currency_type.symbol }} {{ form.total_taxed }}</p>
                                <p class="text-right" v-if="form.total_igv > 0">IGV: {{ currency_type.symbol }} {{ form.total_igv }}</p>
                                <h3 class="text-right" v-if="form.total > 0"><b>TOTAL A PAGAR: </b>{{ currency_type.symbol }} {{ form.total }}</h3>
                            </div>

                        </div>

                    </div>


                    <div class="form-actions text-right mt-4">
                        <el-button @click.prevent="close()">Cancelar</el-button>
                        <el-button class="submit" type="primary" native-type="submit" :loading="loading_submit" v-if="form.items.length > 0">Generar</el-button>
                    </div>
                </form>
            </div>
        </div>

        <contract-form-item :showDialog.sync="showDialogAddItem"
                           :currency-type-id-active="form.currency_type_id"
                           :exchange-rate-sale="form.exchange_rate_sale"
                           @add="addRow"></contract-form-item>

        <person-form :showDialog.sync="showDialogNewPerson"
                       type="customers"
                       :external="true"
                       :document_type_id = form.document_type_id></person-form>


        <contract-options-pdf :showDialog.sync="showDialogOptionsPdf"
                            :contractNewId="contractNewId"
                            :showClose="true"></contract-options-pdf>

        <terms-condition :showDialog.sync="showDialogTermsCondition"
                          :form="form"
                          :showClose="false"></terms-condition>
    </div>
</template>

<script>
    import TermsCondition from './partials/terms_condition.vue'
    import ContractFormItem from './partials/item.vue'
    import PersonForm from '@views/persons/form.vue'
    import ContractOptionsPdf from './partials/options_pdf.vue'
    import {functions, exchangeRate} from '@mixins/functions'
    import {calculateRowItem} from '@helpers/functions'
    import Logo from '@views/companies/logo.vue'

    export default {
        props:['typeUser', 'quotationId', 'id', 'showPayments'],
        components: {ContractFormItem, PersonForm, ContractOptionsPdf, Logo, TermsCondition},
        mixins: [functions, exchangeRate],
        data() {
            return {
                sellers: [],
                resource: 'contracts',
                showDialogTermsCondition: false,
                showDialogAddItem: false,
                showDialogNewPerson: false,
                showDialogOptionsPdf: false,
                loading_submit: false,
                loading_form: false,
                errors: {},
                form: {},
                currency_types: [],
                discount_types: [],
                charges_types: [],
                all_customers: [],
                payment_method_types: [],
                customers: [],
                recordId:null,
                company: null,
                establishments: [],
                establishment: null,
                quotation: {},
                currency_type: {},
                contractNewId: null,
                payment_destinations:  [],
                configuration: {},
                activePanel: 0,
                loading_search:false
            }
        },
        async created() {
            // console.log(this.showPayments)
            await this.initForm()
            await this.$http.get(`/${this.resource}/tables`)
                .then(response => {
                    const data = response.data;
                    this.currency_types = data.currency_types
                    this.establishments = data.establishments
                    this.all_customers = data.customers
                    this.discount_types = data.discount_types
                    this.charges_types = data.charges_types
                    this.company = data.company
                    this.form.currency_type_id = (this.currency_types.length > 0)?this.currency_types[0].id:null
                    this.form.establishment_id = (this.establishments.length > 0)?this.establishments[0].id:null
                    this.payment_method_types = data.payment_method_types
                    this.payment_destinations = data.payment_destinations
                    this.configuration = data.configuration
                    this.sellers = data.sellers;

                    this.changeEstablishment()
                    this.changeDateOfIssue()
                    this.changeCurrencyType()
                    this.allCustomers()
                    this.selectDestinationSale()
                })
            this.loading_form = true
            this.$eventHub.$on('reloadDataPersons', (customer_id) => {
                this.reloadDataCustomers(customer_id)
            })

            await this.isUpdate()
            await this.generateFromQuotation()
        },
        methods: {
            selectDestinationSale() {

                if(this.configuration.destination_sale && this.payment_destinations.length > 0 && this.showPayments) {
                    let cash = _.find(this.payment_destinations, {id : 'cash'})
                    this.form.payments[0].payment_destination_id = (cash) ? cash.id : this.payment_destinations[0].id
                }

            },
            getPaymentDestinationId() {

                if(this.configuration.destination_sale && this.payment_destinations.length > 0) {

                    let cash = _.find(this.payment_destinations, {id : 'cash'})

                    return (cash) ? cash.id : this.payment_destinations[0].id

                }

                return null

            },
            async generateFromQuotation(){

                if(this.quotationId){


                    await this.$http.get(`/quotations/record/${this.quotationId}`)
                        .then(response => {
                            this.quotation = response.data.data.quotation;
                        })

                    this.reloadDataCustomers(this.quotation.customer_id)
                    this.form.establishment_id = this.quotation.establishment_id
                    this.form.customer_id = this.quotation.customer_id
                    this.form.currency_type_id = this.quotation.currency_type_id
                    this.form.items = this.quotation.items
                    // this.form.payments = this.quotation.payments
                    this.form.total_exportation = this.quotation.total_exportation
                    this.form.total_free = this.quotation.total_free
                    this.form.total_taxed = this.quotation.total_taxed
                    this.form.total_unaffected = this.quotation.total_unaffected
                    this.form.total_exonerated = this.quotation.total_exonerated
                    this.form.total_igv = this.quotation.total_igv
                    this.form.total_taxes = this.quotation.total_taxes
                    this.form.total_value = this.quotation.total_value
                    this.form.total = this.quotation.total
                    this.form.quotation_id = this.quotation.id
                    // console.log(this.form)
                    this.form.account_number = this.quotation.account_number
                    this.form.description = this.quotation.description
                    this.form.payment_method_type_id = this.quotation.payment_method_type_id
                    this.form.shipping_address = this.quotation.shipping_address
                }

            },
            async isUpdate(){

                if(this.id) {
                    await this.$http.get(`/${this.resource}/record/${this.id}`)
                        .then(response => {
                            this.form = response.data.data.contract;
                            this.reloadDataCustomers(this.form.customer_id)

                        })
                }

            },
            changeTermsCondition(){

                if(this.form.active_terms_condition){

                    this.showDialogTermsCondition = true

                }else{
                    this.form.terms_condition = null
                }
            },
            clickAddPayment() {
                this.form.payments.push({
                    id: null,
                    contract_id: null,
                    date_of_payment:  moment().format('YYYY-MM-DD'),
                    payment_method_type_id: '01',
                    reference: null,
                    payment_destination_id: this.getPaymentDestinationId(),
                    payment: 0,

                });
            },
            clickCancel(index) {
                this.form.payments.splice(index, 1);
            },
            getFormatUnitPriceRow(unit_price){
                return _.round(unit_price, 6)
                // return unit_price.toFixed(6)
            },
            async changePaymentMethodType(flag_submit = true){
                let payment_method_type = await _.find(this.payment_method_types, {'id':this.form.payment_method_type_id})
                if(payment_method_type){

                    if(payment_method_type.number_days){
                        this.form.date_of_issue =  moment().add(payment_method_type.number_days,'days').format('YYYY-MM-DD');
                        this.changeDateOfIssue()
                    }
                    // else{
                    //     if(flag_submit){
                    //         this.form.date_of_issue = moment().format('YYYY-MM-DD')
                    //         this.changeDateOfIssue()
                    //     }
                    // }
                }
            },
            searchRemoteCustomers(input) {

                if (input.length > 0) {
                    this.loading_search = true
                    let parameters = `input=${input}`

                    this.$http.get(`/${this.resource}/search/customers?${parameters}`)
                            .then(response => {
                                this.customers = response.data.customers
                                this.loading_search = false
                                if(this.customers.length == 0){this.allCustomers()}
                            })
                } else {
                    this.allCustomers()
                }

            },
            initForm() {
                this.errors = {}
                this.form = {
                    description: '',
                    prefix:'CNT',
                    establishment_id: null,
                    date_of_issue: moment().format('YYYY-MM-DD'),
                    time_of_issue: moment().format('HH:mm:ss'),
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
                    delivery_date: null,
                    items: [],
                    charges: [],
                    discounts: [],
                    attributes: [],
                    guides: [],
                    payment_method_type_id:'10',
                    additional_information:null,
                    shipping_address:null,
                    account_number:null,
                    terms_condition:null,
                    active_terms_condition:false,
                    actions: {
                        format_pdf:'a4',
                    },
                    payments: [],
                    quotation_id:null,
                }

                if(this.showPayments){
                    this.clickAddPayment()
                }

            },
            resetForm() {
                this.activePanel = 0
                this.initForm()
                this.form.currency_type_id = (this.currency_types.length > 0)?this.currency_types[0].id:null
                this.form.establishment_id = (this.establishments.length > 0)?this.establishments[0].id:null
                this.changeEstablishment()
                this.changeDateOfIssue()
                this.changeCurrencyType()
                this.allCustomers()
            },
            changeEstablishment() {
                this.establishment = _.find(this.establishments, {'id': this.form.establishment_id})

            },
            cleanCustomer(){
                this.form.customer_id = null;
            },
            changeDateOfIssue() {
                // this.form.date_of_due = this.form.date_of_issue > this.form.date_of_due ? this.form.date_of_issue:null
                this.searchExchangeRateByDate(this.form.date_of_issue).then(response => {
                    this.form.exchange_rate_sale = response
                })
            },
            allCustomers() {
                this.customers = this.all_customers
            },
            addRow(row) {
                this.form.items.push(JSON.parse(JSON.stringify(row)));

                this.calculateTotal();
            },
            clickRemoveItem(index) {
                this.form.items.splice(index, 1)
                this.calculateTotal()
            },
            changeCurrencyType() {
                this.currency_type = _.find(this.currency_types, {'id': this.form.currency_type_id})
                let items = []
                this.form.items.forEach((row) => {
                    items.push(calculateRowItem(row, this.form.currency_type_id, this.form.exchange_rate_sale))
                });
                this.form.items = items
                this.calculateTotal()
            },
            calculateTotal() {
                let total_discount = 0
                let total_charge = 0
                let total_exportation = 0
                let total_taxed = 0
                let total_exonerated = 0
                let total_unaffected = 0
                let total_free = 0
                let total_igv = 0
                let total_value = 0
                let total = 0
                this.form.items.forEach((row) => {
                    total_discount += parseFloat(row.total_discount)
                    total_charge += parseFloat(row.total_charge)

                    if (row.affectation_igv_type_id === '10') {
                        total_taxed += parseFloat(row.total_value)
                    }
                    if (row.affectation_igv_type_id === '20') {
                        total_exonerated += parseFloat(row.total_value)
                    }
                    if (row.affectation_igv_type_id === '30') {
                        total_unaffected += parseFloat(row.total_value)
                    }
                    if (row.affectation_igv_type_id === '40') {
                        total_exportation += parseFloat(row.total_value)
                    }
                    if (['10', '20', '30', '40'].indexOf(row.affectation_igv_type_id) < 0) {
                        total_free += parseFloat(row.total_value)
                    }
                    if (['10', '20', '30', '40'].indexOf(row.affectation_igv_type_id) > -1) {
                        total_igv += parseFloat(row.total_igv)
                        total += parseFloat(row.total)
                    }
                    total_value += parseFloat(row.total_value)
                });

                this.form.total_exportation = _.round(total_exportation, 2)
                this.form.total_taxed = _.round(total_taxed, 2)
                this.form.total_exonerated = _.round(total_exonerated, 2)
                this.form.total_unaffected = _.round(total_unaffected, 2)
                this.form.total_free = _.round(total_free, 2)
                this.form.total_igv = _.round(total_igv, 2)
                this.form.total_value = _.round(total_value, 2)
                this.form.total_taxes = _.round(total_igv, 2)
                this.form.total = _.round(total, 2)
            },
            validate_payments(){

                //eliminando items de pagos
                for (let index = 0; index < this.form.payments.length; index++) {
                    if(parseFloat(this.form.payments[index].payment) === 0)
                        this.form.payments.splice(index, 1)
                }

                let error_by_item = 0
                let acum_total = 0

                this.form.payments.forEach((item)=>{
                    acum_total += parseFloat(item.payment)
                    if(item.payment <= 0 || item.payment == null) error_by_item++;
                })

                return  {
                    error_by_item : error_by_item,
                    acum_total : acum_total
                }

            },
            async submit() {

                let validate = await this.validate_payments()
                if(validate.acum_total > parseFloat(this.form.total) || validate.error_by_item > 0) {
                    return this.$message.error('Los montos ingresados superan al monto a pagar o son incorrectos');
                }

                if(this.form.date_of_issue > this.form.date_of_due)
                    return this.$message.error('La fecha de emisión no puede ser posterior a la de vencimiento');

                if(this.form.date_of_issue > this.form.delivery_date)
                    return this.$message.error('La fecha de emisión no puede ser posterior a la de entrega');

                this.loading_submit = true
                // await this.changePaymentMethodType(false)
                await this.$http.post(`/${this.resource}`, this.form).then(response => {
                    if (response.data.success) {

                        this.resetForm();
                        this.contractNewId = response.data.data.id;

                        // console.log(this.quotationId, this.id)

                        if(this.quotationId){

                            if(this.showPayments){
                                this.$http.get(`/quotations/changed/${this.quotationId}`).then(() => {
                                    this.$eventHub.$emit('reloadData');
                                });
                            }

                            this.$message.success(`El contrato ${response.data.data.number_full} fue generado`)
                            this.close()

                        }else{
                            this.showDialogOptionsPdf = true;
                            this.isUpdate()
                        }
                    }
                    else {
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
                });
            },
            close() {
                location.href = '/'+this.resource
            },
            reloadDataCustomers(customer_id) {
                this.$http.get(`/${this.resource}/search/customer/${customer_id}`).then((response) => {
                    this.customers = response.data.customers
                    this.form.customer_id = customer_id
                })
            },
        }
    }
</script>

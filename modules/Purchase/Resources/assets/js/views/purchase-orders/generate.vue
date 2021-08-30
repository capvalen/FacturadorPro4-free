<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <!-- <div class="card-header bg-info">
            <h3 class="my-0">Nueva Compra</h3>
        </div> -->
        <div class="tab-content" v-if="loading_form">
            <div class="invoice">
                <header class="clearfix">
                    <div class="row">
                        <div class="col-sm-2 text-center mt-3 mb-0">
                            <logo url="/" :path_logo="(company.logo != null) ? `/storage/uploads/logos/${company.logo}` : ''" ></logo>
                        </div>
                        <div class="col-sm-10 text-left mt-3 mb-0">
                            <address class="ib mr-2" >
                                <span class="font-weight-bold d-block">ORDEN DE COMPRA</span>
                                <span class="font-weight-bold d-block">OC-XXX</span>
                                <span class="font-weight-bold">{{company.name}}</span>
                                <br>
                                <div v-if="establishment.address != '-'">{{ establishment.address }}, </div> {{ establishment.district.description }}, {{ establishment.province.description }}, {{ establishment.department.description }} - {{ establishment.country.description }}
                                <br>
                                {{establishment.email}} - <span v-if="establishment.telephone != '-'">{{establishment.telephone}}</span>
                            </address>
                        </div>
                    </div>
                </header>
                <form autocomplete="off" @submit.prevent="submit">
                    <div class="form-body">

                        <div class="row">

                            <div class="col-lg-6">
                                <div class="form-group" :class="{'has-danger': errors.supplier_id}">
                                    <label class="control-label">
                                        Proveedor
                                        <a href="#" @click.prevent="showDialogNewPerson = true">[+ Nuevo]</a>
                                    </label>
                                    <el-select v-model="form.supplier_id" filterable @change="changeSupplier" ref="select_person" @keyup.native="keyupSupplier" @keyup.enter.native="keyupEnterSupplier">
                                        <el-option v-for="option in suppliers" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                    </el-select>
                                    <small class="form-control-feedback" v-if="errors.supplier_id" v-text="errors.supplier_id[0]"></small>
                                </div>
                            </div>

                            <div class="col-lg-2">
                                <div class="form-group" :class="{'has-danger': errors.date_of_issue}">
                                    <label class="control-label">Fec Emisión</label>
                                    <el-date-picker v-model="form.date_of_issue" type="date" value-format="yyyy-MM-dd" :clearable="false" @change="changeDateOfIssue"></el-date-picker>
                                    <small class="form-control-feedback" v-if="errors.date_of_issue" v-text="errors.date_of_issue[0]"></small>
                                </div>
                            </div>

                            <div class="col-lg-2">
                                <div class="form-group" :class="{'has-danger': errors.date_of_due}">
                                    <label class="control-label">Fec. Vencimiento</label>
                                    <el-date-picker v-model="form.date_of_due" type="date" value-format="yyyy-MM-dd" :clearable="false"></el-date-picker>
                                    <small class="form-control-feedback" v-if="errors.date_of_due" v-text="errors.date_of_due[0]"></small>
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
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group" :class="{'has-danger': errors.payment_method_type_id}">
                                    <label class="control-label">
                                        Forma de pago
                                    </label>
                                    <el-select v-model="form.payment_method_type_id" filterable @change="changePaymentMethodType">
                                        <el-option v-for="option in payment_method_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                    </el-select>
                                    <small class="form-control-feedback" v-if="errors.payment_method_type_id" v-text="errors.payment_method_type_id[0]"></small>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group" :class="{'has-danger': errors.exchange_rate_sale}">
                                    <label class="control-label">Tipo de cambio
                                        <el-tooltip class="item" effect="dark" content="Tipo de cambio del día, extraído de SUNAT" placement="top-end">
                                            <i class="fa fa-info-circle"></i>
                                        </el-tooltip>
                                    </label>
                                    <el-input v-model="form.exchange_rate_sale" :readonly="true"></el-input>
                                    <small class="form-control-feedback" v-if="errors.exchange_rate_sale" v-text="errors.exchange_rate_sale[0]"></small>
                                </div>
                            </div>
                            <div class="col-lg-3" style="margin-top:29px;">
                                <div class="form-group" :class="{'has-danger': errors.file}">
                                    <el-upload
                                            :data="{'type': 'purchase-order-attached'}"
                                            :headers="headers"
                                            :multiple="false"
                                            :action="`/${resource}/upload`"
                                            :show-file-list="true"
                                            :file-list="fileList"
                                            :on-remove="handleRemove"
                                            :on-success="onSuccess"
                                            :limit="1"
                                            >
                                        <el-button slot="trigger" type="primary">Seleccione un archivo (PDF/JPG)</el-button>
                                    </el-upload>
                                    <small class="form-control-feedback" v-if="errors.file" v-text="errors.file[0]"></small>
                                </div>
                            </div>

                            <!-- <div class="col-lg-12 col-md-6 d-flex align-items-end mt-4">
                                <div class="form-group">
                                    <button type="button" class="btn waves-effect waves-light btn-primary" @click.prevent="showDialogAddItem = true">+ Agregar Producto</button>
                                </div>
                            </div> -->
                        </div>
                        <div class="row mt-4" v-if="form.items.length > 0">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr width="100%">
                                                <th>#</th>
                                                <th>Descripción</th>
                                                <th class="text-center">Unidad</th>
                                                <th class="text-right">Cantidad</th>
                                                <th width="20%" class="text-right">Precio Unitario</th>
                                                <th class="text-right">Subtotal</th>
                                                <th class="text-right">Total</th>
                                                <!-- <th></th> -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(row, index) in form.items" :key="index" width="100%">
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ row.item.description }}<br/>
                                            <!-- <small>{{ row.affectation_igv_type.description }}</small> -->
                                            </td>
                                            <td class="text-center">{{ row.item.unit_type_id }}</td>
                                            <td class="text-right">{{ row.quantity }}</td>
                                            <td class="text-right" width="20%">
                                                <!-- {{ currency_type.symbol }} -->
                                                 <!-- {{ row.unit_price }} -->

                                                <div class="input-group input-group-sm mb-2">
                                                    <div v-if="currency_type.symbol" class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1">{{ currency_type.symbol }}</span>
                                                    </div>
                                                    <input v-model="row.unit_price" @change="inputUnitPrice(index)" type="number" min="0" step='0.01' class="form-control" aria-label="Precio Unitario" aria-describedby="basic-addon1">
                                                </div>
                                            </td>
                                            <td class="text-right">{{ currency_type.symbol }} {{ row.total_value }}</td>
                                            <td class="text-right">{{ currency_type.symbol }} {{ row.total }}</td>
                                            <!-- <td class="text-right">
                                                <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickRemoveItem(index)">x</button>
                                            </td> -->
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <p class="text-right" v-if="form.total_exportation > 0">OP.EXPORTACIÓN: {{ currency_type.symbol }} {{ form.total_exportation }}</p>
                                <p class="text-right" v-if="form.total_free > 0">OP.GRATUITAS: {{ currency_type.symbol }} {{ form.total_free }}</p>
                                <p class="text-right" v-if="form.total_unaffected > 0">OP.INAFECTAS: {{ currency_type.symbol }} {{ form.total_unaffected }}</p>
                                <p class="text-right" v-if="form.total_exonerated > 0">OP.EXONERADAS: {{ currency_type.symbol }} {{ form.total_exonerated }}</p>
                                <p class="text-right" v-if="form.total_taxed > 0">OP.GRAVADA: {{ currency_type.symbol }} {{ form.total_taxed }}</p>
                                <p class="text-right" v-if="form.total_igv > 0">IGV: {{ currency_type.symbol }} {{ form.total_igv }}</p>
                                <h3 class="text-right" v-if="form.total > 0"><b>TOTAL COMPRAS: </b>{{ currency_type.symbol }} {{ form.total }}</h3>

                            </div>
                        </div>
                    </div>
                    <div class="form-actions text-right mt-4">
                        <el-button @click.prevent="close()">Cancelar</el-button>
                        <el-button type="primary" native-type="submit" :loading="loading_submit" v-if="form.items.length > 0 && !hide_button && form.total>0">Generar</el-button>
                    </div>
                </form>
            </div>
        </div>

        <purchase-form-item :showDialog.sync="showDialogAddItem"
                           :currency-type-id-active="form.currency_type_id"
                           :exchange-rate-sale="form.exchange_rate_sale"
                           @add="addRow"></purchase-form-item>

        <person-form :showDialog.sync="showDialogNewPerson"
                       type="suppliers"
                        :input_person="input_person"
                       :external="true"></person-form>

        <purchase-options :showDialog.sync="showDialogOptions"
                          :recordId="purchaseNewId"
                          :showClose="false"></purchase-options>
    </div>
</template>

<script>

    import PurchaseFormItem from './partials/item.vue'
    import PurchaseOptions from './partials/options.vue'
    import {functions, exchangeRate} from '../../../../../../../resources/js/mixins/functions'
    import {calculateRowItem} from '../../../../../../../resources/js/helpers/functions'
    import Logo from '../../../../../../../resources/js/views/tenant/companies/logo.vue'
    import PersonForm from '../../../../../../../resources/js/views/tenant/persons/form.vue'

    export default {
        components: {PurchaseFormItem, PersonForm, PurchaseOptions, Logo},
        mixins: [functions, exchangeRate],
        props:['purchase_quotation'],
        data() {
            return {
                headers: headers_token,
                input_person:{},
                resource: 'purchase-orders',
                showDialogAddItem: false,
                showDialogNewPerson: false,
                showDialogOptions: false,
                loading_submit: false,
                hide_button: false,
                is_perception_agent: false,
                errors: {},
                loading_form: false,
                form: {},
                aux_supplier_id:null,
                total_amount:0,
                document_types: [],
                currency_types: [],
                discount_types: [],
                charges_types: [],
                payment_method_types: [],
                all_suppliers: [],
                suppliers: [],
                company: null,
                operation_types: [],
                establishment: {},
                all_series: [],
                series: [],
                currency_type: {},
                affectation_igv_types: [],
                system_isc_types: [],
                discount_types: [],
                charge_types: [],
                attribute_types: [],
                fileList: [],
                purchaseNewId: null
            }
        },
        async created() {
           // console.log(this.purchase_quotation)
            await this.initForm()
            await this.$http.get(`/${this.resource}/tables`)
                .then(response => {

                    this.currency_types = response.data.currency_types
                    this.establishment = response.data.establishment
                    this.suppliers = response.data.suppliers
                    this.payment_method_types = response.data.payment_method_types
                    this.company = response.data.company

                    this.form.currency_type_id = (this.currency_types.length > 0)?this.currency_types[0].id:null
                    this.form.establishment_id = (this.establishment.id) ? this.establishment.id:null

                    this.changeDateOfIssue()
                    // this.changeDocumentType()
                    this.changeCurrencyType()
                })

            await this.$http.get(`/${this.resource}/item/tables`).then(response => {
                    this.items = response.data.items
                    this.affectation_igv_types = response.data.affectation_igv_types
                    this.system_isc_types = response.data.system_isc_types
                    this.discount_types = response.data.discount_types
                    this.charge_types = response.data.charge_types
                    this.attribute_types = response.data.attribute_types
                    // this.filterItems()
                })

            this.$eventHub.$on('reloadDataPersons', (supplier_id) => {
                this.reloadDataSuppliers(supplier_id)
            })

            this.loading_form = true

            this.$eventHub.$on('initInputPerson', () => {
                this.initInputPerson()
            })

            await this.setData()
        },
        methods: {

            handleRemove(file, fileList) {                
                this.form.upload_filename = null
                this.form.temp_path = null
                this.fileList = []
            }, 
            onSuccess(response, file, fileList) {
                // console.log(response, file, fileList)
                this.fileList = fileList
                if (response.success) {
                    this.form.attached = response.data.filename
                    this.form.image_url = response.data.temp_image
                    this.form.attached_temp_path = response.data.temp_path
                } else {
                    this.cleanFileList()
                    this.$message.error(response.message)
                }
            },
            cleanFileList(){
                this.fileList = []

            },

            setData(){

                let oc = this.purchase_quotation

                // this.form.establishment_id =  null
                this.form.date_of_issue =  oc.date_of_issue
                this.form.date_of_due =  oc.date_of_issue
                this.form.time_of_issue =   oc.time_of_issue
                this.form.purchase_quotation_id =   oc.id

                oc.items.forEach(it => {
                    it.unit_price = 0
                    it.total_igv = 0
                    it.total_value = 0
                    it.total = 0
                    it.discounts = []
                    it.charges = []
                });

                this.form.items = oc.items

            },
            addRow(row) {
                this.form.items.push(row)
                this.calculateTotal()
            },
            async inputUnitPrice(index) {
                this.form.items[index].item = await _.find(this.items, {'id': this.form.items[index].item_id})
                // this.form.unit_price = this.form.item.purchase_unit_price
                this.form.items[index].affectation_igv_type_id = this.form.items[index].item.purchase_affectation_igv_type_id
                // this.form.item_unit_types = _.find(this.items, {'id': this.form.item_id}).item_unit_types
                await this.clickAddItem(index)
            },
            async clickAddItem(index) {
                this.form.items[index].item.unit_price = this.form.items[index].unit_price
                this.form.items[index].item.presentation = [];
                this.form.items[index].affectation_igv_type = _.find(this.affectation_igv_types, {'id': this.form.items[index].affectation_igv_type_id})
                this.row = await calculateRowItem(this.form.items[index], this.form.currency_type_id, this.exchangeRateSale)
                this.form.items[index] = this.row
                await this.calculateTotal()

                // this.initForm()
                // this.initializeFields()
                // this.$emit('add', this.row)
            },
            initInputPerson(){
                this.input_person = {
                    number:'',
                    identity_document_type_id:''
                }
            },
            keyupEnterSupplier(){

                if(this.input_person.number){

                    if(!isNaN(parseInt(this.input_person.number))){

                        switch (this.input_person.number.length) {
                            case 8:
                                this.input_person.identity_document_type_id = '1'
                                this.showDialogNewPerson = true
                                break;

                            case 11:
                                this.input_person.identity_document_type_id = '6'
                                this.showDialogNewPerson = true
                                break;
                            default:
                                this.input_person.identity_document_type_id = '6'
                                this.showDialogNewPerson = true
                                break;
                        }
                    }
                }
            },
            keyupSupplier(e){

                if(e.key !== "Enter"){

                    this.input_person.number = this.$refs.select_person.$el.getElementsByTagName('input')[0].value
                    let exist_persons = this.suppliers.filter((supplier)=>{
                        let pos = supplier.description.search(this.input_person.number);
                        return (pos >- 1)
                    })

                    this.input_person.number = (exist_persons.length == 0) ? this.input_person.number : null
                }

            },
            inputSeries(){

                const pattern = new RegExp('^[A-Z0-9]+$', 'i');
                if(!pattern.test(this.form.series)){
                    this.form.series = this.form.series.substring(0, this.form.series.length - 1);
                } else {
                    this.form.series = this.form.series.toUpperCase()
                }

            },
            changePaymentMethodType(flag_submit = true){
                let payment_method_type = _.find(this.payment_method_types, {'id':this.form.payment_method_type_id})
                if(payment_method_type.number_days){
                    this.form.date_of_issue =  moment().add(payment_method_type.number_days,'days').format('YYYY-MM-DD');
                    this.changeDateOfIssue()
                }else{
                    if(flag_submit){
                        this.form.date_of_issue = moment().format('YYYY-MM-DD')
                        this.changeDateOfIssue()
                    }
                }
            },
            inputTotalPerception(){
                this.total_amount = parseFloat(this.form.total) + parseFloat(this.form.total_perception)
                if(isNaN(this.total_amount)){
                    this.hide_button = true
                }else{
                    this.hide_button = false

                }
            },
            changeSupplier(){
                this.calculatePerception()
            },
            filterSuppliers() {

                if(this.form.document_type_id === '01') {
                    this.suppliers = _.filter(this.all_suppliers, {'identity_document_type_id': '6'})
                    this.selectSupplier()

                } else {
                    this.suppliers =  this.all_suppliers  //_.filter(this.all_suppliers, (c) => { return c.identity_document_type_id !== '6' })
                    this.selectSupplier()
                }
            },
            selectSupplier(){

                let supplier = _.find(this.suppliers, {'id': this.aux_supplier_id})
                this.form.supplier_id = (supplier) ? supplier.id : null
                this.aux_supplier_id = null

            },
            initForm() {
                this.errors = {}
                this.form = {
                    establishment_id: null,
                    document_type_id: null,
                    prefix:'OC',
                    series: null,
                    number: null,
                    date_of_issue: moment().format('YYYY-MM-DD'),
                    time_of_issue: moment().format('HH:mm:ss'),
                    supplier_id: null,
                    supplier: null,
                    payment_method_type_id:'01',
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
                    perception_date: null,
                    perception_number: null,
                    total_perception: 0,
                    date_of_due: moment().format('YYYY-MM-DD'),
                    items: [],
                    charges: [],
                    discounts: [],
                    attributes: [],
                    guides: [],
                    attached_temp_path: null,
                    attached: null
                }

                this.initInputPerson()
                this.fileList = []

            },
            resetForm() {
                this.initForm()
                this.form.currency_type_id = (this.currency_types.length > 0)?this.currency_types[0].id:null
                this.form.establishment_id = this.establishment.id
                this.form.document_type_id = (this.document_types.length > 0)?this.document_types[0].id:null

                this.changeDateOfIssue()
                this.changeDocumentType()
                this.changeCurrencyType()
            },
            changeDateOfIssue() {
                this.form.date_of_due = this.form.date_of_issue
                this.searchExchangeRateByDate(this.form.date_of_issue).then(response => {
                    this.form.exchange_rate_sale = (response == 0) ? 1 : response
                })
            },
            changeDocumentType() {
                // this.filterSuppliers()
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
                // console.log("aa")

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

                // console.log(this.form.items)

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

                    total_value += parseFloat(row.total_value)
                    total_igv += parseFloat(row.total_igv)
                    total += parseFloat(row.total)
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

                this.calculatePerception()


             },
            calculatePerception(){

                let supplier = _.find(this.suppliers,{'id':this.form.supplier_id})

                if(supplier){

                    this.form.supplier = supplier

                    if(supplier.perception_agent) {

                        let total_perception = 0
                        let quantity_item_perception = 0
                        let total_amount = 0
                        this.form.total_perception = 0

                        this.form.perception_date = moment().format('YYYY-MM-DD')

                        this.form.items.forEach((row) => {
                            quantity_item_perception += (row.item.has_perception) ? 1:0
                            total_perception += (row.item.has_perception) ? (parseFloat(row.unit_price) * parseFloat(row.quantity) * (parseFloat(row.item.percentage_perception)/100)) : 0
                        });

                        this.is_perception_agent = (quantity_item_perception > 0) ? true : false
                        this.form.total_perception = _.round(total_perception,2)
                        total_amount = this.form.total + parseFloat(this.form.total_perception)
                        this.total_amount = _.round(total_amount, 2)

                    }else{

                        this.is_perception_agent = false
                        this.form.perception_date = null
                        this.form.perception_number = null
                        this.form.total_perception = null

                    }

                }


            },
            async submit() {

                this.loading_submit = true
                await this.changePaymentMethodType(false)
                await this.$http.post(`/${this.resource}`, this.form)
                    .then(response => {

                        if (response.data.success) {
                            this.resetForm()
                            this.purchaseNewId = response.data.data.id
                            this.showDialogOptions = true
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
                location.href = '/purchase-orders'
            },
            reloadDataSuppliers(supplier_id) {

                this.$http.get(`/${this.resource}/table/suppliers`).then((response) => {

                    this.aux_supplier_id = supplier_id
                    this.all_suppliers = response.data
                    this.filterSuppliers()

                })
            },
        }
    }
</script>

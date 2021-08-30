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
                        <div class="col-sm-10 text-left mt-3 mb-0">
                            <address class="ib mr-2" >
                                <span class="font-weight-bold d-block">PEDIDO</span>
                                <span class="font-weight-bold d-block">PD-XXX</span>
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
                                    <label class="control-label">Fec. Entrega</label>
                                    <el-date-picker v-model="form.delivery_date" type="date" value-format="yyyy-MM-dd" :clearable="true"></el-date-picker>
                                    <small class="form-control-feedback" v-if="errors.delivery_date" v-text="errors.delivery_date[0]"></small>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group" :class="{'has-danger': errors.exchange_rate_sale}">
                                    <label class="control-label">Descripcion
                                    </label>
                                    <el-input  type="textarea"  :rows="3" v-model="form.description"></el-input>
                                    <small class="form-control-feedback" v-if="errors.description" v-text="errors.description[0]"></small>
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
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th class="font-weight-bold">Descripción</th>
                                                <th class="text-center font-weight-bold">Unidad</th>
                                                <th class="text-right font-weight-bold">Cantidad</th>
                                                <th class="text-right font-weight-bold">Precio Unitario</th>
                                                <th class="text-right font-weight-bold">Subtotal</th>
                                                <!--<th class="text-right font-weight-bold">Cargo</th>-->
                                                <th class="text-right font-weight-bold">Total</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody v-if="form.items.length > 0">
                                            <tr v-for="(row, index) in form.items">
                                                <td>{{index + 1}}</td>
                                                <td>
                                                    {{row.item.description}}
                                                    <template v-if="row.item.presentation">
                                                        {{row.item.presentation.hasOwnProperty('description') ? row.item.presentation.description : ''}}<br/><small>{{row.affectation_igv_type.description}}</small>
                                                    </template>
                                                </td>
                                                <td class="text-center">{{row.item.unit_type_id}}</td>
                                                <td class="text-right">{{row.quantity}}</td>
                                                <!-- <td class="text-right">{{currency_type.symbol}} {{row.unit_price}}</td> -->
                                                <td class="text-right">{{ currency_type.symbol }} {{ getFormatUnitPriceRow(row.unit_price) }}</td>

                                                <td class="text-right">{{currency_type.symbol}} {{row.total_value}}</td>
                                                <!--<td class="text-right">{{ currency_type.symbol }} {{ row.total_charge }}</td>-->
                                                <td class="text-right">{{currency_type.symbol}} {{row.total}}</td>
                                                <td class="text-right">

                                                    <template v-if="row.id">
                                                        <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickDeleteONItem(row.id, index)">x</button>
                                                    </template>
                                                    <template v-else>
                                                        <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickRemoveItem(index)">x</button>
                                                    </template>
                                                </td>
                                            </tr>
                                            <tr><td colspan="8"></td></tr>
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
                        <el-button class="submit" type="primary" native-type="submit" :loading="loading_submit" v-if="form.items.length > 0">Guardar cambios</el-button>
                    </div>
                </form>
            </div>
        </div>

        <order-note-form-item :showDialog.sync="showDialogAddItem"
                           :currency-type-id-active="form.currency_type_id"
                           :typeUser="typeUser"
                           :exchange-rate-sale="form.exchange_rate_sale"
                           @add="addRow"></order-note-form-item>

        <person-form :showDialog.sync="showDialogNewPerson"
                       type="customers"
                       :external="true"
                       :document_type_id = form.document_type_id></person-form>

        <order-note-options :type="type" :showDialog.sync="showDialogOptions"
                          :recordId="orderNoteNewId"
                          :showGenerate="false"
                          :typeUser="typeUser"
                          :showClose="false"></order-note-options>
    </div>
</template>

<script>
    import OrderNoteFormItem from './partials/item.vue'
    import PersonForm from '@views/persons/form.vue'
    import OrderNoteOptions from './partials/options.vue'
    import {functions, exchangeRate} from '@mixins/functions'
    import {calculateRowItem} from '@helpers/functions'
    import Logo from '@views/companies/logo.vue'

    export default {
        components: {OrderNoteFormItem, PersonForm, OrderNoteOptions, Logo},
        props: {
            'resourceId': {
                required: true,

                default: 0
            },
            'typeUser': {
                required: true,
            },
        },
        mixins: [functions, exchangeRate],
        data() {
            return {
                type:  'edit',
                resource: 'order-notes',
                showDialogAddItem: false,
                showDialogNewPerson: false,
                showDialogOptions: false,
                loading_submit: false,
                loading_form: false,
                errors: {},
                form: {},
                currency_types: [],
                discount_types: [],
                charges_types: [],
                all_customers: [],
                customers: [],
                company: null,
                establishments: [],
                establishment: null,
                currency_type: {},
                orderNoteNewId: null,
                payment_method_types: [],
                activePanel: 0,
                loading_search:false
            }
        },
        async created() {
            await this.initForm()
            await this.$http.get(`/${this.resource}/tables`)
                .then(response => {
                    this.currency_types = response.data.currency_types
                    this.establishments = response.data.establishments
                    this.all_customers = response.data.customers
                    this.discount_types = response.data.discount_types
                    this.charges_types = response.data.charges_types
                    this.company = response.data.company
                    this.form.currency_type_id = (this.currency_types.length > 0)?this.currency_types[0].id:null
                    this.form.establishment_id = (this.establishments.length > 0)?this.establishments[0].id:null
                    this.payment_method_types = response.data.payment_method_types

                    this.changeEstablishment()
                    this.changeDateOfIssue()
                    this.changeCurrencyType()
                    this.allCustomers()
                    this.initRecord()

                })
            this.loading_form = true
            this.$eventHub.$on('reloadDataPersons', (customer_id) => {
                this.reloadDataCustomers(customer_id)
            })

        },
        methods: {

            async clickDeleteONItem(id, index){

                this.$confirm('¿Desea eliminar el item?', 'Eliminar', {
                    confirmButtonText: 'Eliminar',
                    cancelButtonText: 'Cancelar',
                    type: 'warning'
                }).then(() => {
                    this.$http.delete(`/${this.resource}/destroy_order_note_item/${id}`)
                        .then(res => {

                            if(res.data.success) {

                                this.$message.success(res.data.message)
                                this.clickRemoveItem(index)
                                this.$eventHub.$emit('reloadDataItems', null)

                                this.$http.post(`/${this.resource}/update`, this.form).then(response => {
                                    if (response.data.success) {
                                        this.isUpdate()
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
                                })

                            }else{
                                this.$message.error(res.data.message)
                            }

                        })
                        .catch(error => {
                            if (error.response.status === 500) {
                                this.$message.error('Error al intentar eliminar');
                            } else {
                                console.log(error.response.data.message)
                            }
                        })
                }).catch(error => {
                    console.log(error)
                });


            },
            async isUpdate(){
                if (this.resourceId) {
                    await this.$http.get(`/${this.resource}/record2/${this.resourceId}`)
                        .then(response => {
                            this.form = response.data.data.order_note;
                        })
                }
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
            initRecord()
            {
                this.$http.get(`/${this.resource}/record/${this.resourceId}` )
                .then(response => {

                    let dato = response.data.data.order_note
                  //  console.log(dato)
                    this.form.id = dato.id
                    this.form.customer_id = dato.customer_id
                    this.form.currency_type_id = dato.currency_type_id
                    this.form.payment_method_type_id = dato.payment_method_type_id
                    this.form.date_of_due = dato.date_of_due
                    this.form.date_of_issue = dato.date_of_issue
                    this.form.delivery_date = dato.delivery_date
                    this.form.exchange_rate_sale = dato.exchange_rate_sale
                    this.form.description = dato.description
                    this.form.items = dato.items
                    this.calculateTotal()
                    //console.log(response.data)
                })

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
                    id: 0,
                    description: '',
                    prefix:'PD',
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
                    payment_method_type_id:null,
                    operation_type_id: null,
                    date_of_due: moment().format('YYYY-MM-DD'),
                    delivery_date: null,
                    items: [],
                    charges: [],
                    discounts: [],
                    attributes: [],
                    guides: [],
                    additional_information:null,
                    actions: {
                        format_pdf:'a4',
                    }
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
                this.form.date_of_due = this.form.date_of_issue
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
            async submit() {
                // await this.changePaymentMethodType(false)

                if(this.form.date_of_issue > this.form.date_of_due)
                    return this.$message.error('La fecha de emisión no puede ser posterior a la de vencimiento');

                if(this.form.date_of_issue > this.form.delivery_date)
                    return this.$message.error('La fecha de emisión no puede ser posterior a la de entrega');

                this.loading_submit = true
                await this.$http.post(`/${this.resource}/update`, this.form).then(response => {
                    if (response.data.success) {
                        this.resetForm();
                        this.orderNoteNewId = response.data.data.id;
                        this.$message.success('Se guardaron los cambios correctamente.')
                         this.showDialogOptions = true;
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
                location.href = `/${this.resource}`
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

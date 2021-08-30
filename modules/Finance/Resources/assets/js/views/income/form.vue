<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <div class="card-header bg-info">
            <h3 class="my-0">Nuevo Ingreso</h3>
        </div>
        <div class="tab-content">
            <form autocomplete="off" @submit.prevent="submit">
                <div class="form-body">

                    <div class="row">
                         <div class="col-lg-4">
                            <div class="form-group" :class="{'has-danger': errors.income_type_id}">
                                <label class="control-label">Tipo comprobante</label>
                                <el-select v-model="form.income_type_id"  >
                                    <el-option v-for="option in income_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.income_type_id" v-text="errors.income_type_id[0]"></small>
                            </div>
                        </div>

                        <!-- <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.number}">
                                <label class="control-label">Número <span class="text-danger">*</span></label>
                                <el-input v-model="form.number"></el-input>

                                <small class="form-control-feedback" v-if="errors.number" v-text="errors.number[0]"></small>
                            </div>
                        </div> -->
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
                            <div class="form-group" :class="{'has-danger': errors.date_of_issue}">
                                <label class="control-label">Fec Emisión</label>
                                <el-date-picker v-model="form.date_of_issue" type="date" value-format="yyyy-MM-dd" :clearable="false" @change="changeDateOfIssue"></el-date-picker>
                                <small class="form-control-feedback" v-if="errors.date_of_issue" v-text="errors.date_of_issue[0]"></small>
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
                        <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.income_reason_id}">
                                <label class="control-label">Motivo</label>
                                <el-select v-model="form.income_reason_id"  >
                                    <el-option v-for="option in income_reasons" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.income_reason_id" v-text="errors.income_reason_id[0]"></small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group" :class="{'has-danger': errors.customer}">
                                <label class="control-label">
                                    Cliente
                                </label>
                                <el-input v-model="form.customer"></el-input>
                                <small class="form-control-feedback" v-if="errors.customer" v-text="errors.customer[0]"></small>
                            </div>
                        </div>


                    </div>
                    <div class="row col-lg-8 mt-3">

                        <table>
                            <thead>
                                <tr width="100%">
                                    <th v-if="form.payments.length>0">Método de ingreso</th>
                                    <th v-if="form.payments.length>0" >Destino</th>
                                    <th v-if="form.payments.length>0">Referencia</th>
                                    <th v-if="form.payments.length>0">Monto</th>
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
                                            <el-select v-model="row.payment_destination_id" filterable :disabled="row.payment_destination_disabled">
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
                    <div class="row">
                        <div class="col-lg-2 col-md-6 mt-4">
                            <div class="form-group">
                                <button type="button" class="btn waves-effect waves-light btn-primary" @click.prevent="showDialogAddItem = true">+ Agregar detalle</button>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" v-if="form.items.length > 0">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th class="font-weight-bold">Descripción</th>
                                            <th class="text-right font-weight-bold">Total</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(row, index) in form.items" :key="index">
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ row.description }}</td>
                                            <td class="text-right">{{ currency_type.symbol }} {{ row.total }}</td>
                                            <td class="text-right">
                                                <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickRemoveItem(index)">x</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <h3 class="text-right" v-if="form.total > 0"><b>TOTAL: </b>{{ currency_type.symbol }} {{ form.total }}</h3>
                        </div>
                    </div>
                </div>
                <div class="form-actions text-right mt-4">
                    <el-button @click.prevent="close()">Cancelar</el-button>
                    <el-button type="primary" native-type="submit" :loading="loading_submit" v-if="form.items.length > 0">Generar</el-button>
                </div>
            </form>
        </div>

        <income-form-item :showDialog.sync="showDialogAddItem"
                           :currency-type="currency_type"
                           :exchange-rate-sale="form.exchange_rate_sale"
                           @add="addRow"></income-form-item>


        <income-options :showDialog.sync="showDialogOptions"
                          :recordId="incomeNewId"
                          :showClose="false"></income-options>
    </div>
</template>

<script>

    import IncomeFormItem from './partials/item.vue'
    import IncomeOptions from './partials/options.vue'
    import {functions, exchangeRate} from '@mixins/functions'


    export default {
        components: {IncomeFormItem, IncomeOptions},
        mixins: [functions, exchangeRate],
        data() {
            return {
                resource: 'finances/income',
                showDialogAddItem: false,
                showDialogNewPerson: false,
                showDialogOptions: false,
                loading_submit: false,
                errors: {},
                form: {},
                aux_customer:null,
                income_types: [],
                currency_types: [],
                suppliers: [],
                establishment: {},
                currency_type: {},
                payment_method_types: [],
                payment_destinations:  [],
                income_reasons: [],
                expenseNewId: null,
                incomeNewId:null
            }
        },
        created() {
            this.initForm()
            this.$http.get(`/${this.resource}/tables`)
                .then(response => {

                    this.income_reasons = response.data.income_reasons
                    this.payment_method_types = response.data.payment_method_types
                    this.income_types = response.data.income_types
                    this.currency_types = response.data.currency_types
                    this.establishment = response.data.establishment
                    this.form.currency_type_id = (this.currency_types.length > 0) ? this.currency_types[0].id : null
                    this.form.establishment_id = (this.establishment.id) ? this.establishment.id : null
                    this.form.income_type_id = (this.income_types.length > 0) ? this.income_types[0].id : null
                    this.form.income_reason_id = (this.income_reasons.length > 0)?this.income_reasons[0].id:null
                    this.payment_destinations = response.data.payment_destinations

                    this.changeDateOfIssue()

                    this.changeCurrencyType()
                })

            this.$eventHub.$on('reloadDataPersons', (customer) => {
                this.reloadDataSuppliers(customer)
           })
        },
        methods: {
            initForm() {
                this.errors = {}
                this.form = {
                    establishment_id: null,
                    income_type_id: null,
                    income_reason_id: null,
                    number: null,
                    date_of_issue: moment().format('YYYY-MM-DD'),
                    time_of_issue: moment().format('HH:mm:ss'),
                    customer: null,
                    currency_type_id: null,
                    exchange_rate_sale: 0,
                    total: 0,
                    items: [],
                    payments: [],
                }

                this.clickAddPayment()
                // this.changeExpenseMethodType()
            },
            resetForm() {
                this.initForm()
                this.form.currency_type_id = (this.currency_types.length > 0)?this.currency_types[0].id:null
                this.form.establishment_id = this.establishment.id
                this.form.income_type_id = (this.income_types.length > 0)?this.income_types[0].id:null
                this.form.income_reason_id = (this.income_reasons.length > 0)?this.income_reasons[0].id:null

                this.changeDateOfIssue()
                this.changeCurrencyType()
            },
            changeDateOfIssue() {
                this.form.date_of_due = this.form.date_of_issue
                this.searchExchangeRateByDate(this.form.date_of_issue).then(response => {
                    this.form.exchange_rate_sale = response
                })
            },
            clickCancel(index) {
                this.form.payments.splice(index, 1);
            },
            clickAddPayment() {
                this.form.payments.push({
                    id: null,
                    income_id: null,
                    date_of_payment:  moment().format('YYYY-MM-DD'),
                    payment_method_type_id: '01',
                    payment_destination_id: 'cash',
                    reference: null,
                    payment: 0,
                });
            },
            addRow(row) {
                this.form.items.push(row)
                this.calculateTotal()
            },
            clickRemoveItem(index) {
                this.form.items.splice(index, 1)
                this.calculateTotal()
            },
            changeCurrencyType() {
                this.currency_type = _.find(this.currency_types, {'id': this.form.currency_type_id})
                let items = []
                this.form.items.forEach((row) => {
                    items.push(this.calculateRowItem(row, this.form.currency_type_id, this.form.exchange_rate_sale))


                });
                this.form.items = items
                this.calculateTotal()
            },
            calculateRowItem(row, currency_type_id,exchange_rate_sale){

                let currency_type_id_old = row.currency_type_id

                row.total = row.total_original

                if (currency_type_id_old === 'PEN' && currency_type_id_old !== currency_type_id)
                {
                    row.total = row.total_original / exchange_rate_sale;
                }

                if (currency_type_id === 'PEN' && currency_type_id_old !== currency_type_id)
                {
                    row.total = row.total_original * exchange_rate_sale;
                }

                row.total = _.round(row.total,2)

                return row
            },
            calculateTotal() {
                let total = 0
                this.form.items.forEach((row) => {
                    total += parseFloat(row.total)
                });
                this.form.total = _.round(total, 2)
                this.form.payments[0].payment = this.form.total
            },
            submit() {

                let validate = this.validate_payments()
                if(validate.acum_total != parseFloat(this.form.total) || validate.error_by_item > 0) {
                    return this.$message.error('Los montos ingresados no coinciden con el monto total o son incorrectos');
                }

                if (this.form.customer == null) {
                    this.form.customer = '-';
                }

                this.loading_submit = true
                this.$http.post(`/${this.resource}`, this.form)
                    .then(response => {

                        if (response.data.success) {
                            this.resetForm()
                            this.incomeNewId = response.data.data.id
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
            validate_payments(){

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
            close() {
                location.href = `/${this.resource}`
            },
            reloadDataSuppliers(customer) {

                this.$http.get(`/${this.resource}/table/suppliers`).then((response) => {

                    this.form.customer = customer
                    this.suppliers = response.data

                })
            },
        }
    }
</script>

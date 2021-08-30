<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @close="close" @open="create">
        <form autocomplete="off" @submit.prevent="clickAddItem" >
            <div class="form-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group" :class="{'has-danger': errors.document_type_id}">
                            <label class="control-label">Tipo de comprobante</label>
                            <el-select v-model="form.document_type_id" @change="changeDocumentType">
                                <el-option v-for="option in document_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.document_type_id" v-text="errors.document_type_id[0]"></small>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group" :class="{'has-danger': errors.series}">
                            <label class="control-label">Serie</label>
                            <el-input v-model="form.series" :maxlength="4"></el-input>
                            <small class="form-control-feedback" v-if="errors.series" v-text="errors.series[0]"></small>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group" :class="{'has-danger': errors.number}">
                            <label class="control-label">Número</label>
                            <el-input v-model="form.number"></el-input>
                            <small class="form-control-feedback" v-if="errors.number" v-text="errors.number[0]"></small>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group" :class="{'has-danger': errors.date_of_issue}">
                            <label class="control-label">Fecha de emisión</label>
                            <el-date-picker v-model="form.date_of_issue"
                                            type="date"
                                            value-format="yyyy-MM-dd"
                                            :clearable="false"
                                            :picker-options="disabledDateOfIssue"
                                            @change="changeDateOfIssue"></el-date-picker>
                            <small class="form-control-feedback" v-if="errors.date_of_issue" v-text="errors.date_of_issue[0]"></small>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group" :class="{'has-danger': errors.currency_type_id}">
                            <label class="control-label">Moneda</label>
                            <el-select v-model="form.currency_type_id" @change="changeCurrencyType">
                                <el-option v-for="option in currency_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.currency_type_id" v-text="errors.currency_type_id[0]"></small>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group" :class="{'has-danger': errors.exchange_rate_sale}">
                            <label class="control-label">Tipo de cambio</label>
                            <el-input v-model="form.exchange_rate_sale" @input="changeExchangeRateSale"></el-input>
                            <small class="form-control-feedback" v-if="errors.exchange_rate_sale" v-text="errors.exchange_rate_sale[0]"></small>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group" :class="{'has-danger': errors.total_document}">
                            <label class="control-label">Total comprobante</label>
                            <el-input v-model="form.total_document" @input="inputTotalDocument"></el-input>
                            <small class="form-control-feedback" v-if="errors.total_document" v-text="errors.total_document[0]"></small>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group" :class="{'has-danger': errors.date_of_retention}">
                            <label class="control-label">Fecha de retención</label>
                            <el-date-picker v-model="form.date_of_retention" type="date" value-format="yyyy-MM-dd" :clearable="false" :picker-options="disabledDateOfIssue"></el-date-picker>
                            <small class="form-control-feedback" v-if="errors.date_of_retention" v-text="errors.date_of_retention[0]"></small>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group" :class="{'has-danger': errors.total_retention}">
                            <label class="control-label">Total retención
                                <el-tooltip class="item" effect="dark" content="Total comprobante por Tasa de retención" placement="top-start">
                                    <i class="fa fa-info-circle"></i>
                                </el-tooltip>
                            </label>
                            <el-input v-model="form.total_retention"></el-input>
                            <small class="form-control-feedback" v-if="errors.total_retention" v-text="errors.total_retention[0]"></small>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group" :class="{'has-danger': errors.total_to_pay}">
                            <label class="control-label">Total a pagar</label>
                            <el-input v-model="form.total_to_pay"></el-input>
                            <small class="form-control-feedback" v-if="errors.total_to_pay" v-text="errors.total_to_pay[0]"></small>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group" :class="{'has-danger': errors.total_payment}">
                            <label class="control-label">Total pagado
                                <el-tooltip class="item" effect="dark" content="Importe total a pagar (neto) = Total pagos - Total retención" placement="top-start">
                                    <i class="fa fa-info-circle"></i>
                                </el-tooltip>
                            </label>
                            <el-input v-model="form.total_payment"></el-input>
                            <small class="form-control-feedback" v-if="errors.total_payment" v-text="errors.total_payment[0]"></small>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-lg-2 col-md-6 d-flex align-items-end pt-2">
                        <div class="form-group">
                            <button type="button" class="btn waves-effect waves-light btn-primary" @click.prevent="clickAddPayment">+ Agregar Pago</button>
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
                                    <th>Fecha de Pago</th>
                                    <th>Moneda</th>
                                    <th class="text-right">Total
                                        <el-tooltip class="item" effect="dark" content="Importe del pago sin retención" placement="top-start">
                                            <i class="fa fa-info-circle"></i>
                                        </el-tooltip>
                                    </th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(row, index) in form.payments" :key="index">
                                    <td>{{ index + 1 }}</td>
                                    <td><el-date-picker v-model="row.date_of_payment"
                                                        type="date"
                                                        value-format="yyyy-MM-dd"
                                                        :picker-options="disabledDateOfIssue"
                                                        :clearable="false"></el-date-picker>
                                    </td>
                                    <td>
                                        <el-select v-model="row.currency_type_id" @change="changeCurrencyType">
                                            <el-option v-for="option in currency_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                        </el-select>
                                    </td>
                                    <td class="text-right">
                                        <el-input v-model="row.total_payment" @blur="blurTotalPayment(index)"></el-input>
                                    </td>
                                    <td class="text-right">
                                        <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickRemovePayment(index)">x</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions text-right pt-2">
                <el-button @click.prevent="close()">Cerrar</el-button>
                <el-button type="primary" native-type="submit">Agregar</el-button>
            </div>
        </form>
    </el-dialog>
</template>

<script>

    import {exchangeRate} from '../../../../mixins/functions'

    export default {
        props: ['showDialog', 'activeRetentionType'],
        mixins: [exchangeRate],
        data() {
            return {
                titleDialog: 'Agregar documento',
                resource: 'retentions',
                errors: {},
                form: {},
                document_types: [],
                currency_types: [],
                retention_types: [],
                disabledDateOfIssue: {
                  disabledDate(time) {
                    return time.getTime() > moment().subtract(1, 'days');
                  }
                },
            }
        },
        created() {
            this.initForm()
            this.$http.get(`/${this.resource}/document/tables`).then(response => {
                this.document_types = response.data.document_types
                this.currency_types = response.data.currency_types
                this.retention_types = response.data.retention_types
            })
        },
        methods: {
            blurTotalPayment(index = null){

                let sum_total_payment = _.sumBy(this.form.payments, (o) => { return parseFloat(o.total_payment); });
                // console.log(index, sum_total_payment)

                this.form.total_payment = sum_total_payment - this.form.total_retention

            },
            initForm() {
                this.errors = {}
                this.form = {
                    document_type_id: null,
                    document_type_description:null,
                    series: null,
                    number: null,
                    date_of_issue: moment().subtract(1, 'days').format('YYYY-MM-DD'),
                    currency_type_id: null,
                    total_document: 0,
                    date_of_retention: moment().subtract(1, 'days').format('YYYY-MM-DD'),
                    total_retention: 0,
                    total_to_pay: 0,
                    total_payment: 0,
                    exchange_rate_sale: 0,
                    exchange_rate: {
                        currency_type_id_source: null,
                        currency_type_id_target: 'PEN',
                        factor: 1,
                        date_of_exchange_rate: null,
                    },
                    payments: [],
                }
            },
            inputTotalDocument(){
                // console.log(this.activeRetentionType)
                if(this.activeRetentionType && this.form.total_document && this.form.total_document > 0){
                    this.form.total_retention = _.round(parseFloat(this.form.total_document) * (parseFloat(this.activeRetentionType.percentage)/100),2)
                    this.blurTotalPayment()
                }else{
                    this.form.total_retention = 0
                }
                
            },
            async changeDocumentType(){
                let doc_type = await _.find(this.document_types,{'id':this.form.document_type_id}) 
                this.form.document_type_description = doc_type.description
            },
            create() {
                this.form.currency_type_id = (this.currency_types.length > 0)?this.currency_types[0].id:null
                this.form.document_type_id = (this.document_types.length > 0)?this.document_types[0].id:null
                this.changeDateOfIssue()
                this.changeCurrencyType()
                this.changeDocumentType()
            },
            clickAddPayment() {

                if(this.form.payments.length == 1){
                    return this.$message.error('Solo puede agregar 1 pago')
                }

                this.form.payments.push({
                    date_of_payment: this.form.date_of_issue,
                    currency_type_id: this.form.currency_type_id,
                    total_payment: (this.form.payments.length == 0) ? this.form.total_document:0
                })

                this.blurTotalPayment()

            },
            clickRemovePayment(index) {
                this.form.payments.splice(index, 1)
            },
            changeDateOfIssue() {
                this.form.exchange_rate.date_of_exchange_rate = this.form.date_of_issue
                this.searchExchangeRateByDate(this.form.date_of_issue).then(response => {
                    this.form.exchange_rate_sale = parseFloat(response)
                })
            },
            changeCurrencyType() {
                this.form.exchange_rate.currency_type_id_source = this.form.currency_type_id
                this.changeExchangeRateSale()
            },
            changeExchangeRateSale() {
                if(this.form.exchange_rate.currency_type_id_source === this.form.exchange_rate.currency_type_id_target) {
                    this.form.exchange_rate.factor = 1
                } else {
                    this.form.exchange_rate.factor = (this.form.exchange_rate_sale === '')?0:this.form.exchange_rate_sale
                }
            },
            getErrors(){

                if(this.form.payments.length == 0)
                    return { success:false, message:'Debe agregar al menos un pago' }

                if(!this.form.series || !this.form.number)
                    return { success:false, message:'La serie o número son incorrectos' }

                if(this.form.total_document<=0 || this.form.total_retention<=0 || this.form.total_to_pay<=0 || this.form.total_payment<=0 )
                    return { success:false, message:'Los totales deben ser mayores a cero' }
                     
                let sum_total_payment = _.sumBy(this.form.payments, (o) => { return parseFloat(o.total_payment); });

                if(sum_total_payment > this.form.total_document)
                    return { success:false, message:'La sumatoria de pagos no puede ser mayor al Total del comprobante' }

                return {
                    success:true
                }
            },
            async clickAddItem() {

                let error = await this.getErrors()

                if(!error.success)
                    return this.$message.error(error.message)

                this.$emit('add', this.form)
                this.initForm()
                this.$emit('update:showDialog', false)
            },
            close() {
                this.initForm()
                this.$emit('update:showDialog', false)
            },
        }
    }

</script>
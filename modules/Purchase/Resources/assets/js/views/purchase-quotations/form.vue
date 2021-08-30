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
                                <span class="font-weight-bold d-block">COTIZACIÓN</span>
                                <span class="font-weight-bold d-block">COTC-XXX</span>
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
                            
                            <div class="col-lg-10 col-md-10">

                                <table width="100%">
                                    <thead>
                                        <tr width="100%">
                                            <th width="55%" v-if="form.suppliers.length>0" class="pb-2">Proveedor
                                                <a href="#" class="text-center font-weight-bold text-info" @click.prevent="showDialogNewPerson = true">[+ Nuevo]</a>
                                            </th>
                                            <th width="30%" v-if="form.suppliers.length>0" class="pb-2">Correo electrónico</th>
                                            <th width="15%"><a href="#" @click.prevent="clickAddSupplier" class="text-center font-weight-bold text-info">[+ Agregar]</a></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(row, index) in form.suppliers" :key="index" width="100%"> 
                                            <td width="55%" >
                                                <div class="form-group mb-1 mr-2">
                                                    <el-select v-model="row.supplier_id" filterable @change="changeSupplier(index)">
                                                        <el-option v-for="option in suppliers" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                                    </el-select>
                                                </div>
                                            </td>
                                            <td width="30%" >
                                                <div class="form-group mb-1 mr-2"  >
                                                    <el-input v-model="row.email"></el-input>
                                                </div>
                                            </td> 
                                            <td width="15%"  class="series-table-actions text-center" v-if="index > 0"> 
                                                <button  type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickCancel(index)">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td> 
                                            <br>
                                        </tr>
                                    </tbody> 
                                </table> 
                            

                            </div>

                            <div class="col-lg-2">
                                <div class="form-group" :class="{'has-danger': errors.date_of_issue}">
                                    <!--<label class="control-label">Fecha de emisión</label>-->
                                    <label class="control-label">Fec. Emisión</label>
                                    <el-date-picker v-model="form.date_of_issue" type="date" value-format="yyyy-MM-dd" :clearable="false" @change="changeDateOfIssue"></el-date-picker>
                                    <small class="form-control-feedback" v-if="errors.date_of_issue" v-text="errors.date_of_issue[0]"></small>
                                </div>
                            </div>  
                             
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr width="100%">
                                                <th width="5%">#</th>
                                                <th width="50%" class="font-weight-bold">Descripción</th>
                                                <th width="15%" class="text-center font-weight-bold">Unidad</th>
                                                <th width="15%" class="text-right font-weight-bold">Cantidad</th> 
                                                <th width="15%"></th>
                                            </tr>
                                        </thead>
                                        <tbody v-if="form.items.length > 0">
                                            <tr v-for="(row, index) in form.items" :key="index" width="100%">
                                                <td width="5%">{{index + 1}}</td>
                                                <td width="50%">{{row.item.description}}</td>
                                                <td width="15%" class="text-center">{{row.item.unit_type_id}}</td>
                                                <td width="15%" class="text-right">{{row.quantity}}</td> 
                                                <td width="15%" class="text-right">
                                                    <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickRemoveItem(index)">x</button>
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
  
                            
                        </div>

                    </div>

                    <div class="form-actions text-right mt-4">
                        <el-button @click.prevent="close()">Cancelar</el-button>
                        <el-button class="submit" type="primary" native-type="submit" :loading="loading_submit" v-if="form.items.length > 0">{{button_text}}</el-button>
                    </div>
                </form>
            </div>
        </div>

        <quotation-form-item :showDialog.sync="showDialogAddItem" 
                           :currency-type-id-active="form.currency_type_id"
                           :exchange-rate-sale="form.exchange_rate_sale"
                           @add="addRow"></quotation-form-item>

        <person-form :showDialog.sync="showDialogNewPerson"
                       type="suppliers"
                       :external="true"
                       :document_type_id = form.document_type_id></person-form>

        <purchase-quotation-options :showDialog.sync="showDialogOptions"
                          :recordId="purchaseQuotationNewId"
                          :showGenerate="false"
                          :isUpdate="propIsUpdate"
                          :showClose="false"></purchase-quotation-options>
    </div>
</template>

<script>
    import QuotationFormItem from './partials/item.vue'
    import PurchaseQuotationOptions from './partials/options.vue'
    import Logo from '../../../../../../../resources/js/views/tenant/companies/logo.vue'
    import PersonForm from '../../../../../../../resources/js/views/tenant/persons/form.vue'

    export default {
        props: ['id'],
        components: {QuotationFormItem, PersonForm, PurchaseQuotationOptions, Logo},
        data() {
            return {
                resource: 'purchase-quotations',
                showDialogAddItem: false,
                showDialogNewPerson: false,
                showDialogOptions: false,
                loading_submit: false,
                loading_form: false,
                errors: {},
                form: {},  
                suppliers: [],
                company: null,
                establishments: [],
                establishment: null, 
                currency_type: {},
                purchaseQuotationNewId: null,
                activePanel: 0,
                loading_search:false,
                propIsUpdate:false,
                button_text:'Generar'
            }
        },
        async created() {
            await this.initForm()
            await this.$http.get(`/${this.resource}/tables`)
                .then(response => { 
                    this.establishments = response.data.establishments 
                    this.suppliers = response.data.suppliers
                    this.company = response.data.company 
                    this.form.establishment_id = (this.establishments.length > 0)?this.establishments[0].id:null 

                    this.changeEstablishment()
                    this.changeDateOfIssue() 
                    this.allCustomers()
                })
            this.loading_form = true
            this.$eventHub.$on('reloadDataPersons', () => {
                this.reloadDataSuppliers()
            })

            await this.isUpdate()

        },
        methods: {
  
            async isUpdate(){

                if (this.id) {
                    // console.log(this.id);
                    await this.$http.get(`/${this.resource}/record/${this.id}`)
                        .then(response => {
                            // console.log(response)
                            this.form = response.data.data.purchase_quotation; 
                            this.form.suppliers = Object.values(response.data.data.purchase_quotation.suppliers); 
                        })

                    this.button_text = 'Actualizar'
                    this.propIsUpdate = true
                }

            },
            initForm() {
                this.errors = {}
                this.form = {
                    id:null,
                    user_id: null,
                    prefix:'COTC',
                    establishment_id: null, 
                    date_of_issue: moment().format('YYYY-MM-DD'),
                    time_of_issue: moment().format('HH:mm:ss'),
                    suppliers: [], 
                    items: [], 
                    actions: {
                        format_pdf:'a4',
                    }
                }
                
                this.clickAddSupplier()

            },
            async changeSupplier(index){  
                let supplier = await _.find(this.suppliers,{'id':this.form.suppliers[index].supplier_id})
                this.form.suppliers[index].email = supplier.email
            },
            
            clickAddSupplier() {
                this.form.suppliers.push({
                    supplier_id: null,
                    email: null
                });
            },       
            clickCancel(index) {
                this.form.suppliers.splice(index, 1);
            },
            resetForm() {
                this.initForm()
                this.form.establishment_id = (this.establishments.length > 0)?this.establishments[0].id:null 
                this.changeEstablishment() 
                this.changeDateOfIssue()
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
                // this.searchExchangeRateByDate(this.form.date_of_issue).then(response => {
                //     this.form.exchange_rate_sale = response
                // })
            }, 
            allCustomers() {
            }, 
            addRow(row) {
                // console.log(row)
                this.form.items.push(JSON.parse(JSON.stringify(row)));
            },
            clickRemoveItem(index) {
                this.form.items.splice(index, 1)
            }, 
            async validateSuppliers(){

                let cont = 0
                await this.form.suppliers.forEach(element => {
                    if(!element.email){
                        cont++
                    }
                });

                if(cont > 0)
                    return {success:false, message:'El campo correo electrónico es requerido'}

                return {success:true}
            },
            async submit() {
                 
                let validate = await this.validateSuppliers()
                if(!validate.success)
                    return this.$message.error(validate.message);


                this.loading_submit = true
                await this.$http.post(`/${this.resource}`, this.form).then(response => {
                    if (response.data.success) {
                        this.resetForm();
                        this.purchaseQuotationNewId = response.data.data.id;
                        this.showDialogOptions = true;
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
                }).then(() => {
                    this.loading_submit = false;
                });
            },
            close() {
                location.href = '/purchase-quotations'
            },
            reloadDataSuppliers() { 
                this.$http.get(`/${this.resource}/table/suppliers`).then((response) => {
                    this.suppliers = response.data
                })             
            },
        }
    }
</script>
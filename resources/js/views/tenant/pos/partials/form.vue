<template>
    <el-dialog   :title="titleDialog" :visible="showDialog" :close-on-click-modal="false" @close="close" @open="create" append-to-body top="7vh">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-12">  
                        <el-tabs v-model="activeName"   type="card">
                            <el-tab-pane label="General" name="first">


                                    <div class="row"> 
                                        <div class="col-md-3">
                                            <div class="form-group" >
                                                <label class="control-label">Imágen <span class="text-danger"></span></label>
                                                <el-upload class="avatar-uploader"
                                                        :data="{'type': 'items'}"
                                                        :headers="headers"
                                                        :action="`/${resource}/upload`"
                                                        :show-file-list="false"
                                                        :on-success="onSuccess">
                                                    <img v-if="form.image_url" :src="form.image_url" class="avatar">
                                                    <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                                                </el-upload>
                                            </div> 
                                        </div>

                                        <div class="col-md-9"> 
                                            <div class="row">

                                                <!-- <div class="short-div col-md-6"> 
                                                    <div class="form-group" :class="{'has-danger': errors.name}">
                                                        <label class="control-label">Nombre <span class="text-danger">*</span></label>
                                                        <el-input v-model="form.name" dusk="name"></el-input>
                                                        <small class="form-control-feedback" v-if="errors.name" v-text="errors.name[0]"></small>
                                                    </div>
                                                </div> -->

                                                <div class="short-div col-md-6"> 
                                                    <div class="form-group" :class="{'has-danger': errors.description}">
                                                        <label class="control-label">Nombre<span class="text-danger">*</span></label>
                                                        <el-input v-model="form.description" dusk="description"></el-input>
                                                        <small class="form-control-feedback" v-if="errors.description" v-text="errors.description[0]"></small>
                                                    </div>
                                                </div>

                                                <div class="short-div col-md-6"> 
                                                    <div class="form-group" :class="{'has-danger': errors.second_name}">
                                                        <label class="control-label">Nombre secundario</label>
                                                        <el-input v-model="form.second_name" dusk="second_name"></el-input>
                                                        <small class="form-control-feedback" v-if="errors.second_name" v-text="errors.second_name[0]"></small>
                                                    </div>
                                                </div>

                                                <!-- <div class="short-div col-md-8"> 
                                                    <div class="form-group" :class="{'has-danger': errors.description}">
                                                        <label class="control-label">Descripción <span class="text-danger">*</span></label>
                                                        <el-input v-model="form.description" dusk="description"></el-input>
                                                        <small class="form-control-feedback" v-if="errors.description" v-text="errors.description[0]"></small>
                                                    </div>
                                                </div> -->

                                                
                                                <div class="short-div col-md-8"> 
                                                    <div class="form-group" :class="{'has-danger': errors.name}">
                                                        <label class="control-label">Descripción</label>
                                                        <el-input v-model="form.name" dusk="name"></el-input>
                                                        <small class="form-control-feedback" v-if="errors.name" v-text="errors.name[0]"></small>
                                                    </div>
                                                </div>


                                                <div class="short-div col-md-4">
                                                    <div class="form-group" :class="{'has-danger': errors.unit_type_id}">
                                                        <label class="control-label">Unidad</label>
                                                        <el-select v-model="form.unit_type_id" dusk="unit_type_id">
                                                            <el-option v-for="option in unit_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                                        </el-select>
                                                        <small class="form-control-feedback" v-if="errors.unit_type_id" v-text="errors.unit_type_id[0]"></small>
                                                    </div>
                                                </div>
                                                <div class="short-div col-md-8">

                                                     <div class="form-group" >
                                                        <label class="control-label">Almacén
                                                        </label>
                                                        <el-input v-model="warehouse.description" readonly></el-input>
                                                    </div>

                                                </div>
                                                <div class="short-div col-md-4">

                                                     <div class="form-group" :class="{'has-danger': errors.internal_id}">
                                                        <label class="control-label">Código Interno
                                                            <el-tooltip class="item" effect="dark" content="Código interno de la empresa para el control de sus productos" placement="top-start">
                                                                <i class="fa fa-info-circle"></i>
                                                            </el-tooltip>
                                                        </label>
                                                        <el-input v-model="form.internal_id" dusk="internal_id"></el-input>
                                                        <small class="form-control-feedback" v-if="errors.internal_id" v-text="errors.internal_id[0]"></small>
                                                    </div>

                                                </div>

                                                <div class="short-div col-md-4">
                                                    <div class="form-group" :class="{'has-danger': errors.stock}">
                                                        <label class="control-label">Stock Inicial</label>
                                                        <el-input v-model="form.stock" ></el-input>
                                                        <small class="form-control-feedback" v-if="errors.stock" v-text="errors.stock[0]"></small>
                                                    </div>
                                                </div>
                                                <div class="short-div col-md-4">
                                                     <div class="form-group" :class="{'has-danger': errors.stock_min}">
                                                        <label class="control-label">Stock Mínimo</label>
                                                        <el-input v-model="form.stock_min"></el-input>
                                                        <small class="form-control-feedback" v-if="errors.stock_min" v-text="errors.stock_min[0]"></small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 
                                    </div>

                                
  

                            </el-tab-pane> 



                            <el-tab-pane label="Precio" name="second"> 

                                <div class="row">

                                    
                                    <div class="col-md-4">
                                        <div class="form-group" :class="{'has-danger': errors.sale_unit_price}">
                                            <label class="control-label">Precio Unitario (Venta) <span class="text-danger">*</span></label>
                                            <el-input v-model="form.sale_unit_price" dusk="sale_unit_price" @input="calculatePercentageOfProfitBySale"></el-input>
                                            <small class="form-control-feedback" v-if="errors.sale_unit_price" v-text="errors.sale_unit_price[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group" :class="{'has-danger': errors.sale_affectation_igv_type_id}">
                                            <label class="control-label">Tipo de afectación (Venta)</label>
                                            <el-select v-model="form.sale_affectation_igv_type_id" @change="changeAffectationIgvType">
                                                <el-option v-for="option in affectation_igv_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                            </el-select>
                                            <small class="form-control-feedback" v-if="errors.sale_affectation_igv_type_id" v-text="errors.sale_affectation_igv_type_id[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group" :class="{'has-danger': errors.currency_type_id}">
                                            <label class="control-label">Moneda</label>
                                            <el-select v-model="form.currency_type_id" dusk="currency_type_id">
                                                <el-option v-for="option in currency_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                            </el-select>
                                            <small class="form-control-feedback" v-if="errors.currency_type_id" v-text="errors.currency_type_id[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-4 center-el-checkbox">
                                        <div class="form-group" :class="{'has-danger': errors.calculate_quantity}">
                                            <el-checkbox v-model="form.calculate_quantity">Calcular cantidad por precio</el-checkbox><br>
                                            <small class="form-control-feedback" v-if="errors.calculate_quantity" v-text="errors.calculate_quantity[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-4 center-el-checkbox" v-show="show_has_igv">
                                        <div class="form-group" :class="{'has-danger': errors.has_igv}">
                                            <el-checkbox v-model="form.has_igv">Incluye Igv</el-checkbox><br>
                                            <small class="form-control-feedback" v-if="errors.has_igv" v-text="errors.has_igv[0]"></small>
                                        </div>
                                    </div>   

                                    <div class="col-md-4 center-el-checkbox mt-3">
                                        <div class="form-group" :class="{'has-danger': errors.has_plastic_bag_taxes}">
                                            <el-checkbox v-model="form.has_plastic_bag_taxes">Impuesto a la Bolsa Plástica</el-checkbox><br>
                                            <small class="form-control-feedback" v-if="errors.has_plastic_bag_taxes" v-text="errors.has_plastic_bag_taxes[0]"></small>
                                        </div>
                                    </div>
                                    
                                    
                                       

                                    <div class="col-md-12">
                                        <h5 class="separator-title">Campos adicionales</h5>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group" :class="{'has-danger': errors.purchase_unit_price}">
                                            <label class="control-label">Precio Unitario (Compra)</label>
                                            <el-input v-model="form.purchase_unit_price" dusk="purchase_unit_price" @input="calculatePercentageOfProfitByPurchase"></el-input>
                                            <small class="form-control-feedback" v-if="errors.purchase_unit_price" v-text="errors.purchase_unit_price[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group" :class="{'has-danger': errors.purchase_affectation_igv_type_id}">
                                            <label class="control-label">Tipo de afectación (Compra)</label>
                                            <el-select v-model="form.purchase_affectation_igv_type_id">
                                                <el-option v-for="option in affectation_igv_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                            </el-select>
                                            <small class="form-control-feedback" v-if="errors.purchase_affectation_igv_type_id" v-text="errors.purchase_affectation_igv_type_id[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group" :class="{'has-danger': errors.percentage_of_profit}">
                                            <label class="control-label">Porcentaje de ganancia (%)</label>
                                            <el-input v-model="form.percentage_of_profit" @input="calculatePercentageOfProfitByPercentage"></el-input>
                                            <small class="form-control-feedback" v-if="errors.percentage_of_profit" v-text="errors.percentage_of_profit[0]"></small>
                                        </div>
                                    </div>   
                                    
                                                              
                                </div>
                            </el-tab-pane>    

                            <el-tab-pane label="UNSPSC" name="third"> 
                                <div class="row">
                                    
                                    <div class="col-md-4">
                                        <div class="form-group" :class="{'has-danger': errors.item_code}">
                                            <label class="control-label">Código Sunat
                                                <el-tooltip class="item" effect="dark" content="Código proporcionado por SUNAT, campo obligatorio para exportaciones" placement="top">
                                                    <i class="fa fa-info-circle"></i>
                                                </el-tooltip>
                                            </label>
                                            <el-input v-model="form.item_code" dusk="item_code"></el-input>
                                            <small class="form-control-feedback" v-if="errors.item_code" v-text="errors.item_code[0]"></small>
                                        </div>
                                    </div>
                                </div>
                                    
                            </el-tab-pane>    

                        </el-tabs>
                    </div> 

                     
                </div>   
                    
                    
            </div>
            <div class="form-actions text-right mt-4">
                <el-button @click.prevent="close()">Cancelar</el-button>
                <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
            </div>
        </form>
    </el-dialog>
</template> 
<script>

    export default {
        props: ['showDialog', 'recordId', 'external'],
        data() {
            return {
                loading_submit: false,
                headers: headers_token,
                warehouse:{},

                titleDialog: null,
                resource: 'items',
                errors: {},
                form: {},
                unit_types: [],
                currency_types: [],
                system_isc_types: [],
                activeName: 'first',
                affectation_igv_types: [],
                show_has_igv:true,
                item_unit_type:{
                        id:null,
                        unit_type_id:null,
                        quantity_unit:0,
                        price1:0,
                        price2:0,
                        price3:0,
                        price_default:2,

                }
            }
        },
        async created() {
            
            await this.initForm()
            await this.$http.get(`/${this.resource}/tables`)
                .then(response => { 

                    this.unit_types = response.data.unit_types
                    this.currency_types = response.data.currency_types
                    this.system_isc_types = response.data.system_isc_types
                    this.affectation_igv_types = response.data.affectation_igv_types
                    this.warehouse = (response.data.warehouse) ? response.data.warehouse:{id:1, establishment_id:1, description:'Almacén Oficina Principal'}

                    this.form.sale_affectation_igv_type_id = (this.affectation_igv_types.length > 0)?this.affectation_igv_types[0].id:null
                    this.form.purchase_affectation_igv_type_id = (this.affectation_igv_types.length > 0)?this.affectation_igv_types[0].id:null
                })
        },
        methods: { 
            onSuccess(response, file, fileList) {
                if (response.success) {
                    this.form.image = response.data.filename
                    this.form.image_url = response.data.temp_image
                    this.form.temp_path = response.data.temp_path
                } else {
                    this.$message.error(response.message)
                }
            },   
            initForm() {
                this.loading_submit = false,
                this.errors = {}
                this.form = {
                    id: null,
                    item_type_id: '01',
                    internal_id: null,
                    item_code: null,
                    item_code_gs1: null,
                    description: null,
                    second_name:null,
                    name:null,
                    unit_type_id: 'NIU',
                    currency_type_id: 'PEN',
                    sale_unit_price: 0,
                    purchase_unit_price: 0,
                    has_isc: false,
                    system_isc_type_id: null,
                    percentage_isc: 0,
                    suggested_price: 0,
                    sale_affectation_igv_type_id: null,
                    purchase_affectation_igv_type_id: null,
                    calculate_quantity: false,
                    stock: 0,
                    stock_min: 1,
                    has_igv: true,
                    item_unit_types:[],
                    percentage_of_profit: 0,
                    image: null,
                    image_url: null,
                    temp_path: null,
                    has_plastic_bag_taxes: false,
                }
                this.show_has_igv = true
            },
            changeAffectationIgvType(){

                let affectation_igv_type_exonerated = [20,21,30,31,32,33,34,35,36,37]
                let is_exonerated = affectation_igv_type_exonerated.includes((parseInt(this.form.sale_affectation_igv_type_id)));

                if(is_exonerated){
                    this.show_has_igv = false
                    this.form.has_igv = true
                }else{
                    this.show_has_igv = true
                }

            },
            resetForm() {
                this.initForm()
                this.activeName = 'first'
                this.form.sale_affectation_igv_type_id = (this.affectation_igv_types.length > 0)?this.affectation_igv_types[0].id:null
                this.form.purchase_affectation_igv_type_id = (this.affectation_igv_types.length > 0)?this.affectation_igv_types[0].id:null
            },
            create() {
                this.titleDialog = (this.recordId)? 'Editar Producto':'Nuevo Producto'
                if (this.recordId) {
                    this.$http.get(`/${this.resource}/record/${this.recordId}`)
                        .then(response => {
                            this.form = response.data.data
                            this.changeAffectationIgvType()
                        })
                }
            },
            loadRecord(){
                if (this.recordId) {
                    this.$http.get(`/${this.resource}/record/${this.recordId}`)
                        .then(response => {
                            this.form = response.data.data
                            this.changeAffectationIgvType()
                        })
                }
            },
            calculatePercentageOfProfitBySale() {
                let difference = parseFloat(this.form.sale_unit_price) - parseFloat(this.form.purchase_unit_price);

                if(parseFloat(this.form.purchase_unit_price) === 0) {
                    this.form.percentage_of_profit = 0;
                } else {
                    this.form.percentage_of_profit = difference / parseFloat(this.form.purchase_unit_price) * 100;
                }
            },
            calculatePercentageOfProfitByPurchase() {
                if(this.form.percentage_of_profit === '') {
                    this.form.percentage_of_profit = 0;
                }
                this.form.sale_unit_price = (this.form.purchase_unit_price * (100 + parseFloat(this.form.percentage_of_profit))) / 100
            },
            calculatePercentageOfProfitByPercentage() {
                if(this.form.percentage_of_profit === '') {
                    this.form.percentage_of_profit = 0;
                }
                this.form.sale_unit_price = (this.form.purchase_unit_price * (100 + parseFloat(this.form.percentage_of_profit))) / 100
            },
            submit() {
                this.loading_submit = true
                this.$http.post(`/${this.resource}`, this.form)
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success(response.data.message)
                            if (this.external) {
                                this.$eventHub.$emit('reloadDataItems', response.data.id)
                            } else {
                                this.$eventHub.$emit('reloadData')
                            }
                            this.close()
                        } else {
                            this.$message.error(response.data.message)
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data
                        } else {
                            console.log(error)
                        }
                    })
                    .then(() => {
                        this.loading_submit = false
                    })
            },
            close() {
                this.$emit('update:showDialog', false)
                this.resetForm()
            },
            changeHasIsc() {
                this.form.system_isc_type_id = null
                this.form.percentage_isc = 0
                this.form.suggested_price = 0
            },
            changeSystemIscType() {
                if (this.form.system_isc_type_id !== '03') {
                    this.form.suggested_price = 0
                }
            }
        }
    }
</script>
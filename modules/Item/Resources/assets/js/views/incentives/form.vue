<template>
    <el-dialog :title="titleDialog" :visible="showDialog" :close-on-click-modal="false" @close="close" @open="create" append-to-body top="7vh">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
 
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.description}">
                            <label class="control-label">Producto</label>
                            <el-input v-model="form.description" readonly></el-input>
                            <small class="form-control-feedback" v-if="errors.description" v-text="errors.description[0]"></small>
                        </div>
                    </div>    
                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.commission_type}">
                            <label class="control-label">Tipo comisión</label>
                            <el-select v-model="form.commission_type">
                                <el-option v-for="option in commission_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.commission_type" v-text="errors.commission_type[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.commission_amount}">
                            <label class="control-label">Comisión</label>
                            <el-input v-model="form.commission_amount" ></el-input>
                            <small class="form-control-feedback" v-if="errors.commission_amount" v-text="errors.commission_amount[0]"></small>
                        </div>
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
        props: ['showDialog', 'recordId'],

        data() {
            return { 
                loading_submit: false,
                titleDialog: null,
                resource: 'incentives', 
                form: {}, 
                text_commission_type:null,
                commission_types:[]
            }
        },
        created() {
            
            this.commission_types = [{id:'amount', description:'Monto'}, {id:'percentage', description:'Porcentaje'}]
            
            this.initForm() 

        },
        methods: { 
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
                    name: null,
                    second_name: null,
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
                    has_perception: false,
                    item_unit_types:[],
                    percentage_of_profit: 0,
                    percentage_perception: 0,
                    image: null,
                    image_url: null,
                    temp_path: null,
                    is_set: false,
                    account_id: null,
                    category_id: null,
                    brand_id: null,
                    date_of_due:null,
                    commission_amount:0,
                    commission_type:'amount'
                }
                this.show_has_igv = true
                this.enabled_percentage_of_profit = false
            }, 
            resetForm() {
                this.initForm() 
            },
            create() {
                this.titleDialog = 'Registrar comisión'
                if (this.recordId) {
                    this.$http.get(`/${this.resource}/record/${this.recordId}`)
                        .then(response => {
                            this.form = response.data.data
                        })
                }
            }, 
            submit() { 

                this.loading_submit = true
                this.$http.post(`/${this.resource}`, this.form)
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success(response.data.message) 
                            this.$eventHub.$emit('reloadData')
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
        }
    }
</script>

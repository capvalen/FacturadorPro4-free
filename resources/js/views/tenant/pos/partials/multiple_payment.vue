<template>
    <el-dialog :title="titleDialog"   :visible="showDialog"  @open="create"  :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false">
         
        <div class="form-body">
            <div class="row" >
                <div class="col-lg-12">

                    <table>
                    <thead>
                        <tr width="100%">
                            <th v-if="payments.length>0">Método de pago</th>
                            <th v-if="payments.length>0">Destino</th>
                            <th v-if="payments.length>0">Referencia</th>
                            <th v-if="payments.length>0">Monto</th>
                            <th width="15%"><a href="#" @click.prevent="clickAddPayment()" class="text-center font-weight-bold text-info">[+ Agregar]</a></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row, index) in payments" :key="index"> 
                            <td>
                                <div class="form-group mb-2 mr-2">
                                    <el-select v-model="row.payment_method_type_id">
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
                
            </div>
        </div>
        
        <div class="form-actions text-right pt-2">
            <el-button @click.prevent="close()">Cerrar</el-button>
        </div>
    </el-dialog>
</template> 

<script>
    export default {
        props: ['showDialog', 'payments', 'total'],
        data() {
            return {
                titleDialog: 'Pagos',
                loading: false,
                errors: {},
                form: {},
                company: {},
                configuration: {},
                activeName: 'first',
                payment_method_types:[],
                payment_destinations: [],
                cards_brand:[],

            }
        },
        async created() {
            
            await this.$http.get(`/pos/payment_tables`)
                .then(response => { 
                    this.payment_method_types = response.data.payment_method_types  
                    this.cards_brand = response.data.cards_brand  
                    this.payment_destinations = response.data.payment_destinations
                    // this.clickAddPayment()
                    this.getFormPosLocalStorage()
                })  
        },
        methods: {
            getFormPosLocalStorage(){

                let form_pos = localStorage.getItem('form_pos');
                form_pos = JSON.parse(form_pos)
                if (form_pos) {
                    
                    if(form_pos.payments.length == 0){

                        this.clickAddPayment(this.total)

                    }else{
                        // console.log(form_pos.payments[0])
                        form_pos.payments[0].payment = this.total
                        this.$eventHub.$emit('localSPayments', (form_pos.payments))
                        // this.$eventHub.$emit('eventSetFormPosLocalStorage', form_pos)
                        this.$emit('add', form_pos.payments);
                    
                    }
                }

            },
            create(){
                
                
            },
            clickAddPayment(total = 0) {
                
                this.payments.push({
                    id: null,
                    document_id: null,
                    sale_note_id: null,
                    date_of_payment:  moment().format('YYYY-MM-DD'),
                    payment_method_type_id: '01',
                    payment_destination_id: 'cash',
                    reference: null,
                    payment: total,
                });

                this.$emit('add', this.payments);
            }, 
                   
            close() {
                this.$emit('update:showDialog', false)
                this.$emit('add', this.payments);
            },
            clickCancel(index) {
                this.payments.splice(index, 1);
                this.$emit('add', this.payments);
            },
        }
    }
</script>

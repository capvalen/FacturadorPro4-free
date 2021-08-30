<template>
    <div>
        <el-dialog :title="titleDialog" :visible="showDialog" @open="create" width="30%"
                > 
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 text-center font-weight-bold">
                    <p>Imprimir A4</p>
                    <button type="button" class="btn btn-lg btn-info waves-effect waves-light" @click="clickToPrint('a4')">
                        <i class="fa fa-print"></i>
                    </button>
                </div>  
                <div class="col-lg-6 col-md-6 col-sm-6 text-center font-weight-bold">
                    <p>Descargar A4</p>
                    <button type="button" class="btn btn-lg btn-info waves-effect waves-light" @click="clickDownload('a4')">
                        <i class="fa fa-download"></i>
                    </button>
                </div>  
            </div> 
            <br>
            <div class="row mt-3">
                <div class="col-md-12">
                    <el-input v-model="form.customer_email">
                        <el-button slot="append" icon="el-icon-message" @click="clickSendEmail" :loading="loading">Enviar</el-button>
                    </el-input>
                </div>
            </div>
                

            <!-- <span slot="footer" class="dialog-footer row"> -->
            <span slot="footer" class="dialog-footer">
                <!-- <div class="col-md-6">   
                    <el-input v-model="form.customer_email">
                        <el-button slot="append" icon="el-icon-message"   @click="clickSendEmail" :loading="loading">Enviar</el-button>
                    </el-input>
                </div> -->
                <!-- <div class="col-md-6">  -->
                    <el-button @click="clickClose">Cerrar</el-button>  
                <!-- </div> -->

            </span>
        </el-dialog>

     
    </div>
</template>

<script>


    export default {

        props: ['showDialog', 'contractNewId', 'showClose'],
        data() {
            return {
                titleDialog: null, 
                resource: 'contracts', 
                form: {},  
                loading: false,

            }
        },
        created() {
            this.initForm()
        },
        methods: {
            initForm() {  
                this.form = {
                    id: null,
                    external_id: null, 
                    customer_email: null, 
                    customer_id:null
                }
            },     
            create() {
                this.$http.get(`/${this.resource}/record/${this.contractNewId}`)
                    .then(response => {
                        this.form = response.data.data
                        this.titleDialog = `Contrato registrado: ${this.form.identifier}`
                    })
            },             
            clickClose() {
                this.$emit('update:showDialog', false)
                this.initForm() 
            },
            clickToPrint(format){
                window.open(`/${this.resource}/print/${this.form.external_id}/${format}`, '_blank');
            } , 
            clickDownload(format){
                window.open(`/${this.resource}/download/${this.form.external_id}/${format}`, '_blank');
            } ,
            
            clickSendEmail() {
                this.loading = true
                this.$http.post(`/${this.resource}/email`, {
                    customer_id: this.form.customer_id,
                    customer_email: this.form.customer_email,
                    id: this.form.id
                })
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success('El correo fue enviado satisfactoriamente')
                        } else {
                            this.$message.error('Error al enviar el correo')
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data.errors
                        } else {
                            this.$message.error(error.response.data.message)
                        }
                    })
                    .then(() => {
                        this.loading = false
                    })
            },
        }
    }
</script>
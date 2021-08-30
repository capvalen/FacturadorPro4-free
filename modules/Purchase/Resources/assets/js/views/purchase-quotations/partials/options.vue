<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @open="create" width="30%" :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false" append-to-body>
        <div class="row">
            
            <div class="col-lg-12 col-md-12 col-sm-12 text-center font-weight-bold" >
                <el-alert    title="Documento enviado a proveedores"    type="success"    show-icon>  </el-alert>
            </div>
            
            <div class="col-lg-12 col-md-12 col-sm-12 text-center font-weight-bold mt-4">
                <p>Imprimir A4</p>
                <button type="button" class="btn btn-lg btn-info waves-effect waves-light" @click="clickPrint('a4')">
                    <i class="fa fa-file-alt"></i>
                </button>
            </div> 
        </div>   
        <span slot="footer" class="dialog-footer">
            <template v-if="showClose">
                <el-button @click="clickClose">Cerrar</el-button>
            </template>
            <template v-else>
                <el-button class="list" @click="clickFinalize">Ir al listado</el-button>
                <el-button type="primary" @click="clickNewDocument">{{button_text}}</el-button>
            </template>
        </span>
    </el-dialog>
</template>

<script>
    export default {
        props: ['showDialog', 'recordId', 'showClose','isUpdate'],
        data() {
            return {
                titleDialog: null,
                loading: false,
                resource: 'purchase-quotations',
                errors: {},
                form: {},
                company: {},
                locked_emission:{},
                button_text:'Nuevo documento'
            }
        },
        async created() {
            this.initForm() 
        },
        methods: {
            initForm() {
                this.errors = {};
                this.form = {
                    external_id: null,
                    identifier: null,
                    external_id: null,
                    date_of_issue: null,
                    id: null
                }; 
            },
            async create() {
                await this.$http.get(`/${this.resource}/record/${this.recordId}`).then(response => {
                    this.form = response.data.data;
                    this.titleDialog = 'Documento: '+this.form.identifier;
                });
                this.button_text = this.isUpdate ? 'Continuar':'Nuevo documento'
            },
            clickPrint(format){
                window.open(`/${this.resource}/print/${this.form.external_id}/${format}`, '_blank');
            },
            clickDownloadImage() {
                window.open(`${this.form.image_detraction}`, '_blank');
            },
            clickDownload(format) {
                window.open(`${this.form.download_pdf}/${format}`, '_blank');
            },
            clickSendEmail() {
                this.loading = true
                this.$http.post(`/${this.resource}/email`, {
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
            clickFinalize() {
                location.href = `/${this.resource}`
            },
            clickNewDocument() {
                this.clickClose()
            },
            clickClose() {
                this.$emit('update:showDialog', false)
                this.initForm()
            },
        }
    }
</script>

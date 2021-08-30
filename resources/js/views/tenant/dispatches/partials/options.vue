<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @open="create" width="30%" :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false" append-to-body>
     
        <div class="row mb-4" v-if="form.response_message">
            <div class="col-md-12">
                <el-alert
                    :title="form.response_message"
                    :type="form.response_type"
                    show-icon>
                </el-alert>
            </div>
        </div>

        <div class="row">

            <template v-if="form.has_cdr">
                <div class="col-lg-6 col-md-6 col-sm-6 text-center font-weight-bold mt-3">
                    <button type="button" class="btn btn-lg btn-info waves-effect waves-light" @click="clickDownload()">
                        <i class="fa fa-file-alt"></i>
                    </button>
                    <p>Descargar A4</p>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 text-center font-weight-bold mt-3">
                    <button type="button" class="btn btn-lg btn-info waves-effect waves-light" @click="clickDownloadCdr()">
                        <i class="fa fa-file-download"></i>
                    </button>
                    <p>Descargar CDR</p>
                </div>  
            </template>
            <template v-else>
                <div class="col-lg-12 col-md-12 col-sm-12 text-center font-weight-bold mt-3">
                    <button type="button" class="btn btn-lg btn-info waves-effect waves-light" @click="clickDownload()">
                        <i class="fa fa-file-alt"></i>
                    </button>
                    <p>Descargar A4</p>
                </div>
            </template>

        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <el-input v-model="form.customer_email">
                    <el-button slot="append" icon="el-icon-message" @click="clickSendEmail" :loading="loading">Enviar</el-button>
                </el-input>
                <small class="form-control-feedback" v-if="errors.customer_email" v-text="errors.customer_email[0]"></small>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <el-input v-model="form.customer_telephone">
                    <template slot="prepend">+51</template>
                        <el-button slot="append" @click="clickSendWhatsapp" >Enviar
                            <el-tooltip class="item" effect="dark"  content="Es necesario tener aperturado Whatsapp web" placement="top-start">
                                <i class="fab fa-whatsapp" ></i>
                            </el-tooltip>
                        </el-button>
                </el-input>
                <small class="form-control-feedback" v-if="errors.customer_telephone" v-text="errors.customer_telephone[0]"></small>
            </div>
        </div>
        <span slot="footer" class="dialog-footer">
            <template v-if="showClose">
                <el-button @click="clickClose">Cerrar</el-button>
            </template>
            <template v-else>
                <el-button class="list" @click="clickFinalize">Ir al listado</el-button>
                <el-button type="primary" v-if="!isUpdate" @click="clickNewDocument">{{text_button}}</el-button>
            </template>
        </span>
    </el-dialog>
</template>

<script>
    export default {
        props: ['showDialog', 'recordId', 'showClose', 'isUpdate'],
        data() {
            return {
                titleDialog: null,
                loading: false,
                resource: 'dispatches',
                errors: {},
                form: {},
                company: {},
                locked_emission:{},
                text_button: null,
            }
        },
        async created() {
            this.initForm()

            this.text_button = 'Nueva guía'
        },
        methods: {
            clickDownload() {
                window.open(this.form.download_external_pdf, '_blank');
            },
            clickSendWhatsapp() {
                
                if(!this.form.customer_telephone){
                    return this.$message.error('El número es obligatorio')
                }

                window.open(`https://wa.me/51${this.form.customer_telephone}?text=${this.form.message_text}`, '_blank');
            
            },
            initForm() {
                this.errors = {};
                this.form = {
                    customer_email: null,
                    download_external_pdf: null,
                    external_id: null,
                    number: null,
                    id: null,
                    response_message:null,
                    response_type:null,
                    customer_telephone:null,
                    message_text:null,
                    download_cdr: null,
                    state_type_id: '05',
                    has_cdr: true,
                }

                this.locked_emission = {
                    success: true,
                    message: null
                }
                this.company = {
                    soap_type_id: null,
                }
            },
            clickDownloadCdr() {
                window.open(this.form.download_cdr, '_blank');
            }, 
            async create() {
                await this.$http.get(`/${this.resource}/record/${this.recordId}`).then(response => {
                    this.form = response.data.data;
                    this.titleDialog = 'Guía: ' + this.form.number;
                });

            },
            clickPrint(format){
                window.open(`/${this.resource}/print/${this.form.external_id}/${format}`, '_blank');
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

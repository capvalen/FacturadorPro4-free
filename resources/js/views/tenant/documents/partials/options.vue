<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @open="create" width="30%" :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false" append-to-body>
        <div v-loading="loading">
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

            <div class="col-lg-12 col-md-12 col-sm-12 text-center font-weight-bold" v-if="!locked_emission.success">
                <el-alert    :title="locked_emission.message"    type="warning"    show-icon>  </el-alert>
            </div>
        </div>
        <div class="row">

            <div class="col text-center font-weight-bold mt-3">
                <button type="button" class="btn btn-lg btn-info waves-effect waves-light" @click="clickPrint('a4')">
                    <i class="fa fa-file-alt"></i>
                </button>
                <p>Imprimir A4</p>
            </div>
             <div class="col text-center font-weight-bold mt-3">

                <button type="button" class="btn btn-lg btn-info waves-effect waves-light" @click="clickPrint('ticket')">
                    <i class="fa fa-receipt"></i>
                </button>
                 <p>Imprimir Ticket 80MM</p>
            </div>

             <div class="col text-center font-weight-bold mt-3">

                <button type="button" class="btn btn-lg btn-info waves-effect waves-light" @click="clickPrint('ticket_50')">
                    <i class="fa fa-receipt"></i>
                </button>
                <p>Imprimir Ticket 50MM</p>
            </div>

            <div class="col text-center font-weight-bold mt-3" v-if="configuration.ticket_58">

                <button type="button" class="btn btn-lg btn-info waves-effect waves-light" @click="clickPrint('ticket_58')">
                    <i class="fa fa-receipt"></i>
                </button>
                <p>Imprimir Ticket 58MM</p>
            </div>

            <div class="col text-center font-weight-bold mt-3">

                <button type="button" class="btn btn-lg btn-info waves-effect waves-light" @click="clickPrint('a5')">
                    <i class="fa fa-receipt"></i>
                </button>
                <p>Imprimir A5</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 text-center font-weight-bold mt-3" v-if="form.image_detraction">
                <a :href="`${this.form.image_detraction}`" download class="text-center font-weight-bold text-dark">Descargar constancia de pago - detracción</a>
            </div>
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
        <div class="row mt-4" v-if="company.soap_type_id == '02' && form.group_id == '01'">
            <div class="col-md-12 text-center">
                <button type="button" class="btn waves-effect waves-light btn-outline-primary"
                        @click.prevent="clickConsultCdr(form.id)">Consultar CDR</button>
            </div>
        </div>
        </div>
        <span slot="footer" class="dialog-footer">
            <template v-if="showClose">
                <el-button @click="clickClose">Cerrar</el-button>
            </template>
            <template v-else>
                <el-button class="list" @click="clickFinalize">Ir al listado</el-button>
                <el-button v-if="!isUpdate" type="primary" @click="clickNewDocument">Nuevo comprobante</el-button>
            </template>
        </span>
    </el-dialog>
</template>

<script>
    export default {
        props: ['showDialog', 'recordId', 'showClose','isContingency','generatDispatch','dispatchId', 'isUpdate','configuration'],
        data() {
            return {
                titleDialog: null,
                loading: false,
                resource: 'documents',
                errors: {},
                form: {},
                company: {},
                locked_emission:{}
            }
        },
        async created() {
            this.initForm()
        },
        methods: {
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
                    download_pdf: null,
                    external_id: null,
                    number: null,
                    image_detraction: null,
                    id: null,
                    response_message:null,
                    response_type:null,
                    customer_telephone:null,
                    message_text:null,
                    group_id:null,
                };
                this.locked_emission = {
                    success: true,
                    message: null
                }
                this.company = {
                    soap_type_id: null,
                }
            },
            async create() {

                await this.getCompany()
                await this.getRecord()

                this.loading = true;
                await this.$http.get(`/${this.resource}/locked_emission`).then(response => {
                    this.locked_emission = response.data
                }).finally(() => this.loading = false);
            },
            async getCompany(){
                this.loading = true;
                await this.$http.get(`/companies/record`)
                    .then(response => {
                        if (response.data !== '') {
                            this.company = response.data.data
                        }
                    }).finally(() => this.loading = false);
            },
            async getRecord(){
                this.loading = true;
                await this.$http.get(`/${this.resource}/record/${this.recordId}`).then(response => {
                    this.form = response.data.data;
                    this.titleDialog = 'Comprobante: '+this.form.number;
                    if(this.generatDispatch) window.open(`/dispatches/create/${this.form.id}/i/${this.dispatchId}`)
                }).finally(() => this.loading = false);
            },
            clickPrint(format){
                window.open(`/print/document/${this.form.external_id}/${format}`, '_blank');
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
            clickConsultCdr(document_id) {
                this.$http.get(`/${this.resource}/consult_cdr/${document_id}`)
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success(response.data.message)
                            this.getRecord()
                            this.$eventHub.$emit('reloadData')
                        } else {
                            this.$message.error(response.data.message)
                        }
                    })
                    .catch(error => {
                        this.$message.error(error.response.data.message)
                    })
            },
            clickFinalize() {
                location.href = (this.isContingency) ? `/contingencies` : `/${this.resource}`
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

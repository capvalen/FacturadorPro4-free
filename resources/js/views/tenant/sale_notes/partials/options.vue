<template>
    <div>
        <el-dialog :title="titleDialog" :visible="showDialog" @open="create"
                :close-on-click-modal="false"
                :close-on-press-escape="false"
                width="800px"
                :show-close="false">

            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 container-tabs">
                    <el-tabs v-model="activeName">
                        <el-tab-pane label="Imprimir A4" name="first">
                            <embed :src="form.print_a4" type="application/pdf" width="100%" height="400px"/>
                        </el-tab-pane>
                        <el-tab-pane label="Imprimir A5" name="second">
                            <embed :src="form.print_a5" type="application/pdf" width="100%" height="400px"/>
                        </el-tab-pane>
                        <el-tab-pane label="Imprimir Ticket 58MM" name="third" v-if="configuration.ticket_58">
                            <embed :src="form.print_ticket_58" type="application/pdf" width="100%" height="400px"/>
                        </el-tab-pane>
                        <el-tab-pane label="Imprimir Ticket" name="fourth">
                            <embed :src="form.print_ticket" type="application/pdf" width="100%" height="400px"/>
                        </el-tab-pane>
                    </el-tabs>
                </div>
                <div class="col-12 container-btns text-center">
                    <br><br>
                    <a :href="`https://docs.google.com/viewer?url=${form.print_a4}?format=pdf`" class="btn mx-3 btn-primary btn-lg" target="_BLANK">
                        <i class="far fa-file-pdf"></i>
                        <br>
                        <span>PDF A4</span>
                    </a>
                    <a :href="`https://docs.google.com/viewer?url=${form.print_a5}?format=pdf`" class="btn btn-primary mx-3 btn-lg" target="_BLANK">
                        <i class="far fa-file-pdf"></i>
                        <br>
                        <span>PDF A5</span>
                    </a>
                    <a :href="`https://docs.google.com/viewer?url=${form.print_ticket_58}?format=pdf`" class="btn mx-3 btn-primary btn-lg" target="_BLANK">
                        <i class="far fa-file-pdf"></i>
                        <br>
                        <span>PDF TICKET 58MM</span>
                    </a>
                    <a :href="`https://docs.google.com/viewer?url=${form.print_ticket}?format=pdf`" class="btn mx-3 btn-primary btn-lg" target="_BLANK">
                        <i class="far fa-file-pdf"></i>
                        <br>
                        <span>PDF TICKET</span>
                    </a>
                </div>
            </div>
            <span slot="footer" class="dialog-footer row">
                <div class="col-md-6">
                    <el-input v-model="form.customer_email">
                        <el-button slot="append" icon="el-icon-message"   @click="clickSendEmail" :loading="loading">Enviar</el-button>
                    </el-input>
                </div>
                <div class="col-md-6">
                <template v-if="showClose">
                    <el-button @click="clickClose">Cerrar</el-button>
                </template>
                <template v-else>
                    <el-button @click="clickFinalize">Ir al listado</el-button>
                    <el-button type="primary" @click="clickNewSaleNote">Nueva nota de venta</el-button>
                </template>
                </div>
            </span>
        </el-dialog>

    </div>
</template>

<script>
export default {
    props: ['showDialog', 'recordId', 'showClose','configuration'],
    data() {
        return {
            serviceUrl:"https://ej2services.syncfusion.com/production/web-services/api/pdfviewer",
            titleDialog: null,
            loading: false,
            resource: 'sale-notes',
            resource_documents: 'documents',
            errors: {},
            form: {},
            document:{},
            document_types: [],
            all_series: [],
            series: [],
            loading_submit:false,
            showDialogOptions: false,
            documentNewId: null,
            activeName: 'first',
        }
    },
    created() {
        this.initForm()
    },
    methods: {
        initForm() {
            this.errors = {}
            this.form = {
                id: null,
                external_id: null,
                identifier: null,
                date_of_issue:null,
                print_ticket: null,
                print_ticket_58: null,
                print_a4: null,
                print_a5: null,
                series:null,
                number:null,
            }
        },
        create() {
            this.$http.get(`/${this.resource}/record/${this.recordId}`)
                .then(response => {
                    this.form = response.data.data
                    this.titleDialog = `Nota de venta registrada:  ${this.form.serie}-${this.form.number}`
                })
        },
        clickFinalize() {
            location.href = `/${this.resource}`
        },
        clickNewSaleNote() {
            this.clickClose()
        },
        clickClose() {
            this.$emit('update:showDialog', false)
            this.initForm()
        },
        clickDownload(){
            window.open(`/downloads/saleNote/sale_note/${this.form.external_id}`, '_blank');
        },
        clickToPrint(format){
            window.open(`/${this.resource}/print/${this.form.id}/${format}`, '_blank');
        },
        clickSendEmail() {
            this.loading=true
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
                    this.loading=false

                })
        },
    }
}
</script>

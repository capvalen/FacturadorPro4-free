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

        <div class="row" v-if="form.state_type_id == '05'">
            <div class="col-lg-12 col-md-12 col-sm-12 text-center font-weight-bold mt-3">
                <button type="button" class="btn btn-lg btn-info waves-effect waves-light" @click="clickDownload()">
                    <i class="fa fa-file-download"></i>
                </button>
                <p>Descargar CDR</p>
            </div>  
        </div>   
        <span slot="footer" class="dialog-footer">
            <template v-if="showClose">
                <el-button @click="clickClose">Cerrar</el-button>
            </template>
            <template v-else>
                <el-button class="list" @click="clickFinalize">Ir al listado</el-button>
                <el-button type="primary" @click="clickNewDocument">Nuevo comprobante</el-button>
            </template>
        </span>
    </el-dialog>
</template>

<script>
    export default {
        props: ['showDialog', 'recordId', 'showClose'],
        data() {
            return {
                titleDialog: null,
                loading: false,
                resource: 'retentions',
                errors: {},
                form: {},
            }
        },
        async created() {
            this.initForm() 
        },
        methods: {
            clickDownload() {
                window.open(this.form.download_cdr, '_blank');
            }, 
            initForm() {
                this.errors = {};
                this.form = {
                    id: null,
                    number_full: null,
                    date_of_issue: null,
                    download_cdr: null,
                    response_message:null,
                    response_type:null,
                    state_type_id: '05',
                }
            },
            async create() {
                await this.$http.get(`/${this.resource}/record/${this.recordId}`).then(response => {
                    this.form = response.data.data;
                    this.titleDialog = 'Retención: '+this.form.number_full;
                });
            },  
            clickClose() {
                this.$emit('update:showDialog', false)
                this.initForm()
            },
            clickFinalize() {
                location.href = `/${this.resource}`
            },
            clickNewDocument() {
                this.clickClose()
            },
        }
    }
</script>

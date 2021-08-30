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
            <div class="col-lg-12 col-md-12 col-sm-12 text-center font-weight-bold mt-3">
                <button type="button" class="btn btn-lg btn-info waves-effect waves-light" @click="clickDownload()">
                    <i class="fa fa-file-download"></i>
                </button>
                <p>Descargar CDR</p>
            </div>  
        </div>   
        <span slot="footer" class="dialog-footer">
            <el-button @click="clickClose">Cerrar</el-button>
        </span>
    </el-dialog>
</template>

<script>
    export default {
        props: ['showDialog', 'recordId'],
        data() {
            return {
                titleDialog: null,
                loading: false,
                resource: 'summaries',
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
                    identifier: null,
                    date_of_issue: null,
                    download_cdr: null,
                    response_message:null,
                    response_type:null,
                }
            },
            async create() {
                await this.$http.get(`/${this.resource}/record/${this.recordId}`).then(response => {
                    this.form = response.data.data;
                    this.titleDialog = 'Resumen: '+this.form.identifier;
                });
            },  
            clickClose() {
                this.$emit('update:showDialog', false)
                this.initForm()
            },
        }
    }
</script>

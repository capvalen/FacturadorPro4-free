<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @open="create" width="30%" :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false" append-to-body>
            
          
        <div class="row mt-3">
            <div class="col-md-12">
                <el-input v-model="form.email">
                    <el-button slot="append" icon="el-icon-message" @click="clickSendEmail" :loading="loading">Enviar</el-button>
                </el-input>
                <small class="form-control-feedback" v-if="errors.email" v-text="errors.email[0]"></small>
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
                titleDialog: 'Enviar correo',
                loading: false,
                resource: 'cash',
                errors: {},
                form: {},
            }
        },
        async created() {
            this.initForm()
        },
        methods: {
            initForm() {
                this.errors = {};
                this.form = {
                    email: null,
                    cash_id: null, 
                }; 
            },
            async create() {
                  this.form.cash_id = this.recordId
            },   
            clickSendEmail() {

                if(!this.form.email){
                    return this.$message.error('Ingrese un correo')
                }

                this.loading = true
                this.$http.post(`/${this.resource}/email`, this.form)
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
            clickClose() {
                this.$emit('update:showDialog', false)
                this.initForm()
            },
        }
    }
</script>

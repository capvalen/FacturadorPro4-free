<template>
    <div class="card">
        <div class="card-header bg-info">
            <h3 class="my-0">Datos de la Empresa</h3>
        </div>
        <div class="card-body">
            <form autocomplete="off" @submit.prevent="submit">
                <div class="form-body">
                    <div class="row">
                        <div class="col-md-12 mt-2">
                            <h4 class="border-bottom">Entorno del sistema</h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" :class="{'has-danger': errors.soap_send_id}">
                                <label class="control-label">SOAP Envio</label>
                                <el-select  v-model="form.soap_send_id" >
                                    <el-option v-for="(option, index) in soap_sends" :key="index" :value="option.value" :label="option.text"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.soap_send_id" v-text="errors.soap_send_id[0]"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" :class="{'has-danger': errors.soap_type_id}">
                                <label class="control-label">SOAP Tipo</label>
                                <el-select  v-model="form.soap_type_id">
                                    <el-option v-for="option in soap_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>

                                <el-checkbox
                                       v-if="form.soap_send_id == '02' && form.soap_type_id == '01'"
                                       v-model="toggle"
                                       label="Ingresar Usuario">
                                </el-checkbox>
                                <small class="form-control-feedback" v-if="errors.soap_type_id" v-text="errors.soap_type_id[0]"></small>
                            </div>
                        </div>
                    </div>
                    <template v-if="form.soap_type_id == '02' || toggle == true ">
                        <div class="row" >
                            <div class="col-md-12 mt-2">
                                <h4 class="border-bottom">Usuario Secundario Sunat</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" :class="{'has-danger': errors.soap_username}">
                                    <label class="control-label">SOAP Usuario <span class="text-danger">*</span></label>
                                    <el-input v-model="form.soap_username"></el-input>
                                    <div class="sub-title text-muted"><small>RUC + Usuario. Ejemplo: 01234567890ELUSUARIO</small></div>
                                    <small class="form-control-feedback" v-if="errors.soap_username" v-text="errors.soap_username[0]"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" :class="{'has-danger': errors.soap_password}">
                                    <label class="control-label">SOAP Password <span class="text-danger">*</span></label>
                                    <el-input v-model="form.soap_password"></el-input>
                                    <small class="form-control-feedback" v-if="errors.soap_password" v-text="errors.soap_password[0]"></small>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div class="row" v-if="form.soap_send_id == '02'">
                        <div class="col-md-12">
                            <div class="form-group" :class="{'has-danger': errors.soap_url}">
                                <label class="control-label">SOAP Url</label>
                                <el-input v-model="form.soap_url"></el-input>
                                <small class="form-control-feedback" v-if="errors.soap_url" v-text="errors.soap_url[0]"></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-actions text-right pt-2">
                    <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>

    export default {
        data() {
            return {
                loading_submit: false,
                headers: headers_token,
                resource: 'companies',
                errors: {},
                form: {},
                soap_sends: [],
                soap_types: [],
                toggle: false, //Creando el objeto a retornar con v-model
                soap_sends: [ { value: '01', text: 'Sunat' }, { value: '02', text: 'Ose' }],
                soap_types: [{id: "01", description: "Demo"}, {id: "02", description: "Producción"}],
            }
        },
        async created() {
            await this.initForm()
            /*await this.$http.get(`/${this.resource}/tables`)
                .then(response => {
                    this.soap_sends = response.data.soap_sends
                    this.soap_types = response.data.soap_types
                })*/
            await this.$http.get(`/${this.resource}/record`)
                .then(response => {
                    if (response.data !== '') {
                        this.form = response.data.data
                    }
                })
        },
        methods: {
            initForm() {
                this.errors = {}
                this.form = {
                    id: null,
                    certificate: '',
                    soap_send_id: '01',
                    soap_type_id: '01',
                    soap_username: null,
                    soap_password: null,
                    soap_url: null,
                }
            },
            submit() {
                this.loading_submit = true
                this.$http.post(`/${this.resource}`, this.form)
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success(response.data.message)
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

        }
    }
</script>

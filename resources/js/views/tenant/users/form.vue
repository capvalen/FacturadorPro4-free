<template>
    <div class="card">
        <div class="card-header bg-info">
            <h3 class="my-0">Datos del Usuario <small>Acceso al sistema</small></h3>
        </div>
        <div class="card-body">
            <form autocomplete="off" @submit.prevent="submit">
                <div class="form-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" :class="{'has-danger': errors.name}">
                                <label class="control-label">Nombre</label>
                                <el-input v-model="form.name"></el-input>
                                <small class="form-control-feedback" v-if="errors.name" v-text="errors.name[0]"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" :class="{'has-danger': errors.email}">
                                <label class="control-label">Correo Electrónico</label>
                                <el-input v-model="form.email" :disabled="true"></el-input>
                                <small class="form-control-feedback" v-if="errors.email" v-text="errors.email[0]"></small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group" :class="{'has-danger': errors.api_token}">
                                <label class="control-label">Api Token</label>
                                <el-input v-model="form.api_token" readonly></el-input>
                                <small class="form-control-feedback" v-if="errors.api_token" v-text="errors.api_token[0]"></small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" :class="{'has-danger': errors.password}">
                                <label class="control-label">Contraseña</label>
                                <el-input v-model="form.password"></el-input>
                                <small class="form-control-feedback" v-if="errors.password" v-text="errors.password[0]"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" :class="{'has-danger': errors.password_confirmation}">
                                <label class="control-label">Confirmar Contraseña</label>
                                <el-input v-model="form.password_confirmation"></el-input>
                                <small class="form-control-feedback" v-if="errors.password_confirmation" v-text="errors.password_confirmation[0]"></small>
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
                headers: null,
                resource: 'users',
                errors: {},
                form: {},
            }
        },
        created() {
            this.initForm()
            this.$http.get(`/${this.resource}/record/1`)
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
                    name: null,
                    email: null,
                    api_token: null,
                    password: null,
                    password_confirmation: null
                }
            },
            submit() {
                this.loading_submit = true
                this.$http.post(`/${this.resource}`, this.form)
                    .then(response => {
                        if (response.data.success) {
                            this.form.password = null
                            this.form.password_confirmation = null
                            this.$message.success(response.data.message)
                        } else {
                            this.$message.error(response.data.message)
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data.errors
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

<template>
    <div class="card">
        <div class="card-header bg-info">
            <h3 class="my-0">Modo offline</h3>
        </div>
        <div class="card-body">
            <form autocomplete="off" @submit.prevent="submit">
                <div class="form-body">
                    <div class="row">
                        
                        <div class="col-md-12">
                            <label class="control-label">Activar modo offline</label>
                            <div class="form-group" :class="{'has-danger': errors.is_client}">
                                <el-switch v-model="form.is_client" active-text="Si" inactive-text="No"></el-switch>
                                <small class="form-control-feedback" v-if="errors.is_client" v-text="errors.is_client[0]"></small>
                            </div>
                        </div> 
                </div>
                <div class="row mt-4" v-if="form.is_client">
                        
                    <div class="col-md-6"> 
                        <label class="control-label">URL Servidor</label>
                        <div class="form-group" :class="{'has-danger': errors.url_server}">
                            <el-input v-model="form.url_server" ></el-input>                                
                            <small class="form-control-feedback" v-if="errors.url_server" v-text="errors.url_server[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-6"> 
                        <label class="control-label">Token Servidor</label>
                        <div class="form-group" :class="{'has-danger': errors.token_server}">
                            <el-input v-model="form.token_server" ></el-input>                                
                            <small class="form-control-feedback" v-if="errors.token_server" v-text="errors.token_server[0]"></small>
                        </div>
                    </div> 
                </div>
                <div class="form-actions text-right pt-2" >
                    <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
                </div>
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
                resource: 'offline-configurations',
                errors: {},
                form: {}
            }
        },
        async created() {
            await this.initForm();
            
            await this.$http.get(`/${this.resource}/record`) .then(response => {
                if (response.data !== '') this.form = response.data.data;
            });
        },
        methods: {
            initForm() {
                this.errors = {};
                
                this.form = {
                    is_client: false,
                    token_server: null,
                    url_server: null, 
                };
            },
            submit() {
                this.loading_submit = true;
                
                this.$http.post(`/${this.resource}`, this.form).then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                    }
                    else {
                        this.$message.error(response.data.message);
                    }
                }).catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data;
                    }
                    else {
                        console.log(error);
                    }
                }).then(() => {
                    this.loading_submit = false;
                });
            }
        }
    }
</script>

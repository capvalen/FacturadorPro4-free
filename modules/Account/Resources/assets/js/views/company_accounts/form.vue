<template>
    <div class="card">
        <div class="card-header bg-info">
            <h3 class="my-0">Cuentas contables (Ventas)</h3>
        </div>
        <div class="card-body">
            <form autocomplete="off" @submit.prevent="submit">
                <div class="form-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="control-label">Total Soles</label>
                            <div class="form-group" :class="{'has-danger': errors.total_pen}">
                                <el-input v-model="form.total_pen" ></el-input>
                                <small class="form-control-feedback" v-if="errors.total_pen" v-text="errors.total_pen[0]"></small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="control-label">IGV Soles</label>
                            <div class="form-group" :class="{'has-danger': errors.igv_pen}">
                                <el-input v-model="form.igv_pen" ></el-input>
                                <small class="form-control-feedback" v-if="errors.igv_pen" v-text="errors.igv_pen[0]"></small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="control-label">Subtotal Soles</label>
                            <div class="form-group" :class="{'has-danger': errors.subtotal_pen}">
                                <el-input v-model="form.subtotal_pen" ></el-input>
                                <small class="form-control-feedback" v-if="errors.subtotal_pen" v-text="errors.subtotal_pen[0]"></small>
                            </div>
                        </div>
                        <div class="col-md-4 mt-4">
                            <label class="control-label">Total Dólares</label>
                            <div class="form-group" :class="{'has-danger': errors.total_usd}">
                                <el-input v-model="form.total_usd" ></el-input>
                                <small class="form-control-feedback" v-if="errors.total_usd" v-text="errors.total_usd[0]"></small>
                            </div>
                        </div>
                        <div class="col-md-4 mt-4">
                            <label class="control-label">IGV Dólares</label>
                            <div class="form-group" :class="{'has-danger': errors.igv_usd}">
                                <el-input v-model="form.igv_usd" ></el-input>
                                <small class="form-control-feedback" v-if="errors.igv_usd" v-text="errors.igv_usd[0]"></small>
                            </div>
                        </div>
                        <div class="col-md-4 mt-4">
                            <label class="control-label">Subtotal Dólares</label>
                            <div class="form-group" :class="{'has-danger': errors.subtotal_usd}">
                                <el-input v-model="form.subtotal_usd" ></el-input>
                                <small class="form-control-feedback" v-if="errors.subtotal_usd" v-text="errors.subtotal_usd[0]"></small>
                            </div>
                        </div>
                </div>
                <div class="form-actions text-right pt-2">
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
                resource: 'company_accounts',
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
                    send_auto: true,
                    stock: true,
                    cron: true,
                    id: null,
                    subtotal_account:null
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
                }).then(() => {
                    this.loading_submit = false;
                });
            }
        }
    }
</script>

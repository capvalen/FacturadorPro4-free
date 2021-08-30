<template>
    <div class="card">
        <div class="card-header bg-info">
            <h3 class="my-0">
                Buscar comprobante electrónico
            </h3>
        </div>
        <div class="card-body">
            <form autocomplete="off" @submit.prevent="submit">
                <div class="form-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group" :class="{'has-danger': errors.document_type_id}">
                                <label class="control-label mt-2">Tipo Documento<span class="text-danger"> *</span></label>
                                <el-select v-model="form.document_type_id">
                                    <el-option v-for="option in document_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.document_type_id" v-text="errors.document_type_id[0]"></small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group" :class="{'has-danger': errors.date_of_issue}">
                                <label class="control-label mt-2">Fecha de emisión<span class="text-danger"> *</span></label>
                                <el-date-picker v-model="form.date_of_issue" type="date" value-format="yyyy-MM-dd" :clearable="false"></el-date-picker>
                                <small class="form-control-feedback" v-if="errors.date_of_issue" v-text="errors.date_of_issue[0]"></small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" :class="{'has-danger': errors.series}">
                                <label class="control-label mt-2">Serie<span class="text-danger"> *</span></label>
                                <el-input v-model="form.series" :maxlength="4"></el-input>
                                <small class="form-control-feedback" v-if="errors.series" v-text="errors.series[0]"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" :class="{'has-danger': errors.number}">
                                <label class="control-label mt-2">Número<span class="text-danger"> *</span></label>
                                <el-input v-model="form.number"></el-input>
                                <small class="form-control-feedback" v-if="errors.number" v-text="errors.number[0]"></small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" :class="{'has-danger': errors.customer_number}">
                                <label class="control-label mt-2">Número Cliente (RUC/DNI/CE)<span class="text-danger"> *</span></label>
                                <el-input v-model="form.customer_number" :maxlength="11"></el-input>
                                <small class="form-control-feedback" v-if="errors.customer_number" v-text="errors.customer_number[0]"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" :class="{'has-danger': errors.total}">
                                <label class="control-label mt-2">Monto total<span class="text-danger"> *</span></label>
                                <el-input v-model="form.total"></el-input>
                                <small class="form-control-feedback" v-if="errors.total" v-text="errors.total[0]"></small>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="form-actions text-right pt-2 mt-2">
                    <el-button type="primary" native-type="submit" :loading="loading_submit">Buscar</el-button>
                </div>
            </form>
            <div class="table-responsive" v-if="record">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Número</th>
                        <th class="text-right">Total</th>
                        <th class="text-right">Descargas</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>{{ record.customer }}</td>
                        <td>{{ record.number }}</td>
                        <td class="text-right">{{ record.total }}</td>
                        <td class="text-right">
                            <a :href="record.download_xml">XML</a>
                            <a :href="record.download_pdf">PDF</a>
                            <!--<button type="button" class="btn waves-effect waves-light btn-xs btn-info" @click.prevent="clickCreate(row.id)">Editar</button>-->
                            <!--<button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickDelete(row.id)">Eliminar</button>-->
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                loading_submit: false,
                resource: 'search',
                errors: {},
                form: {},
                record: null,
                document_types: []
            }
        },
        created() {
            this.initForm()
            this.$http.get(`/${this.resource}/tables`)
                .then(response => {
                    this.document_types = response.data.document_types
                })
        },
        methods: {
            initForm() {
                this.errors = {}
                this.form = {
                    id: null,
                    document_type_id: '01',
                    customer_number: null,
                    series: null,
                    number: null,
                    total: null,
                    date_of_issue: moment().format('YYYY-MM-DD'),
                }
            },
            submit() {
                this.loading_submit = true
                this.record = null
                this.$http.post(`/${this.resource}`, this.form)
                    .then(response => {
                        if (response.data.success) {
                            this.record = response.data.data
                        } else {
                            this.$message.error(response.data.message)
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data
                        } else {
                            this.$message.error(error.response.data.message)
                        }
                    })
                    .then(() => {
                        this.loading_submit = false
                    })
            },
        }
    }
</script>

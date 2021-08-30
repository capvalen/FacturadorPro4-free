<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @close="close" @open="create">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group" :class="{'has-danger': errors.date_of_reference}">
                            <label class="control-label">Fecha de emisión de comprobantes</label>
                            <el-date-picker v-model="form.date_of_reference" type="date" :clearable="false" value-format="yyyy-MM-dd" @change="changeDateOfReference"></el-date-picker>
                            <small class="form-control-feedback" v-if="errors.date_of_reference" v-text="errors.date_of_reference[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end justify-content-end pt-2">
                        <div class="form-group">
                            <button type="button" class="btn waves-effect waves-light btn-info" @click.prevent="clickSearchDocuments" dusk="search-documents">Buscar comprobantes</button>
                        </div>
                    </div>
                </div>
                <div class="row" v-if="form.documents.length > 0">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Número</th>
                                    <th class="text-center">Moneda</th>
                                    <th class="text-right">T.Exportación</th>
                                    <th class="text-right">T.Gratuita</th>
                                    <th class="text-right">T.Inafecta</th>
                                    <th class="text-right">T.Exonerado</th>
                                    <th class="text-right">T.Gravado</th>
                                    <th class="text-right">T.Igv</th>
                                    <th class="text-right">Total</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(row, index) in form.documents" :key="index">
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ row.number }}<br/>
                                        <small v-text="row.document_type_description"></small><br/>
                                        <small v-if="row.affected_document" v-text="row.affected_document"></small>
                                    </td>
                                    <td class="text-center">{{ row.currency_type_id }}</td>
                                    <td class="text-right">{{ row.total_exportation }}</td>
                                    <td class="text-right">{{ row.total_free }}</td>
                                    <td class="text-right">{{ row.total_unaffected }}</td>
                                    <td class="text-right">{{ row.total_exonerated }}</td>
                                    <td class="text-right">{{ row.total_taxed }}</td>
                                    <td class="text-right">{{ row.total_igv }}</td>
                                    <td class="text-right">{{ row.total }}</td>
                                    <td class="text-right">
                                        <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickRemoveDocument(index)">x</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions text-right pt-2">
                <el-button @click.prevent="close()">Cancelar</el-button>
                <el-button type="primary" native-type="submit" :loading="loading_submit" v-if="form.documents.length > 0" dusk="save-summary">Guardar</el-button>
            </div>
        </form>
    </el-dialog>
</template>
<script>

    export default {
        props: ['showDialog'],
        data () {
            return {
                loading_submit: false,
                loading_search: false,
                titleDialog: null,
                resource: 'summaries',
                errors: {},
                form: {},
            }
        },
        created() {
            this.initForm()
        },
        methods: {
            initForm() {
                this.loading_submit = false,
                this.loading_search = false,
                this.errors = {}
                this.form = {
                    id: null,
                    summary_status_type_id: '1',
                    date_of_issue: null,
                    date_of_reference: moment().format('YYYY-MM-DD'),
                    documents: [],
                }
            },
            create() {
                this.titleDialog = 'Registrar Resumen'
                this.changeDateOfReference()
            },
            clickSearchDocuments() {
                this.loading_search = true
                this.$http.post(`/${this.resource}/documents`, {
                    'date_of_reference': this.form.date_of_reference
                })
                    .then(response => {

                        if(response.data.success){

                            this.form.documents = response.data.data
                        
                        }else{

                            this.$message.error(response.data.message)

                        }

                    })
                    .catch(error => {
                        this.$message.error(error.response.data.message)
                    })
                    .then(() => {
                        this.loading_search = false
                    })
            },
            changeDateOfReference() {
                this.form.documents = []
            },
            clickRemoveDocument(index) {
                this.form.documents.splice(index, 1)
            },
            submit() {
                this.loading_submit = true
                this.$http.post(`${this.resource}`, this.form)
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success(response.data.message)
                            this.$eventHub.$emit('reloadData')
                            this.close()
                        } else {
                            this.$message.error(response.data.message)
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
                        this.loading_submit = false
                    })
            },
            close() {
                this.$emit('update:showDialog', false)
                this.initForm()
            },
        }
    }
</script>

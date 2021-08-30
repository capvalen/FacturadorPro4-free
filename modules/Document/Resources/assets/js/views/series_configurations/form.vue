<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @close="close" @open="create">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.document_type_id}">
                            <label class="control-label">Tipo comprobante</label>
                            <el-select v-model="form.document_type_id" filterable @change="changeDocumentType">
                                <el-option v-for="option in document_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.document_type_id" v-text="errors.document_type_id[0]"></small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.series_id}">
                            <label class="control-label">Serie</label>
                            <el-select v-model="form.series_id" filterable @change="changeSeries">
                                <el-option v-for="option in series" :key="option.id" :value="option.id" :label="option.number"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.series_id" v-text="errors.series_id[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.number}">
                            <label class="control-label">Número (Correlativo a iniciar)</label>
                            <el-input v-model="form.number"  ></el-input>
                            <small class="form-control-feedback" v-if="errors.number" v-text="errors.number[0]"></small>
                        </div>
                    </div>

                </div>
            </div>
            <div class="form-actions text-right mt-4">
                <el-button @click.prevent="close()">Cancelar</el-button>
                <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
            </div>
        </form>
    </el-dialog>
</template>

<script>

    export default {
        props: ['showDialog', 'recordId'],
        data() {
            return {
                loading_submit: false,
                titleDialog: null,
                resource: 'series-configurations',
                errors: {},
                form: {},
                series: [],
                all_series: [],
                document_types: [],

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
                    series_id: null,
                    document_type_id:null,
                    series: null,
                    number: null,
                }
            },
            async changeSeries(){
                let serie = await _.find(this.series,{'id':this.form.series_id})
                this.form.series = serie.number
                // console.log(this.form.series)
            },
            async changeDocumentType() {
                await this.filterSeries();
            },
            async filterSeries() {
                this.form.series_id = null
                this.series = await _.filter(this.all_series, {'document_type_id': this.form.document_type_id});
            },

            async create() {
                this.titleDialog = 'Registrar configuración'

                await this.$http.get(`/${this.resource}/tables`)
                    .then(response => {
                        this.all_series = response.data.series;
                        this.document_types = response.data.document_types;
                    })
            },
            submit() {
                this.loading_submit = true
                this.$http.post(`/${this.resource}`, this.form)
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
                            this.errors = error.response.data
                        } else {
                            console.log(error)
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

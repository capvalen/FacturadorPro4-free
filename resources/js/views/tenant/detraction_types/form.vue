<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @close="close" @open="create">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    
                    <div class="col-md-4">
                        <div class="form-group" :class="{'has-danger': errors.operation_type_id}">
                            <label class="control-label">Tipo operación</label>
                            <el-select v-model="form.operation_type_id" filterable>
                                <el-option v-for="option in operation_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.operation_type_id" v-text="errors.operation_type_id[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" :class="{'has-danger': errors.id}">
                            <label class="control-label">Código</label>
                            <el-input v-model="form.id" :readonly="recordId !== null"></el-input>
                            <small class="form-control-feedback" v-if="errors.id" v-text="errors.id[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" :class="{'has-danger': errors.description}">
                            <label class="control-label">Descripción</label>
                            <el-input v-model="form.description"></el-input>
                            <small class="form-control-feedback" v-if="errors.description" v-text="errors.description[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" :class="{'has-danger': errors.percentage}">
                            <label class="control-label">Porcentaje</label>
                            <el-input v-model="form.percentage"></el-input>
                            <small class="form-control-feedback" v-if="errors.percentage" v-text="errors.percentage[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" :class="{'has-danger': errors.active}">
                            <label class="control-label">Activo</label>
                            <el-switch v-model="form.active" active-text="Si" inactive-text="No" class="d-block"></el-switch>
                            <small class="form-control-feedback" v-if="errors.active" v-text="errors.active[0]"></small>
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
                resource: 'detraction_types',
                errors: {},
                form: {},
                options: [],
                operation_types: [],
            }
        },
        async created() {
            await this.initForm()
            await this.$http.get(`/${this.resource}/tables`)
                .then(response => {
                    this.operation_types = response.data.operation_types
                    this.form.operation_type_id = this.operation_types.length>0 ? this.operation_types[0].id:null
                })
        },
        methods: {
            initForm() {
                this.errors = {}
                this.form = {
                    id: null,
                    description: null,
                    percentage: 0,
                    operation_type_id: null,
                    active: true
                }
            },
            create() {
                this.titleDialog = (this.recordId)? 'Editar tipo detracción':'Nuevo tipo detracción'
                if (this.recordId) {
                    this.$http.get(`/${this.resource}/record/${this.recordId}`)
                        .then(response => {
                            this.form = response.data.data
                        })
                }
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

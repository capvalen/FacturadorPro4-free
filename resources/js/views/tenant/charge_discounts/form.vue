<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @close="close" @open="create">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.charge_discount_type_id}">
                            <label class="control-label">Tipo de Cargo</label>
                            <el-select v-model="form.charge_discount_type_id">
                                <el-option v-for="option in charge_discount_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.charge_discount_type_id" v-text="errors.charge_discount_type_id[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.base}">
                            <label class="control-label">Afecta la base imponible del IGV</label>
                            <el-switch
                                    v-model="form.base"
                                    active-text="Si"
                                    inactive-text="No">
                            </el-switch>
                            <small class="form-control-feedback" v-if="errors.base" v-text="errors.base[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.level}">
                            <label class="control-label">Nivel</label>
                            <el-select v-model="form.level">
                                <el-option key="global" value="global" label="Global"></el-option>
                                <el-option key="item" value="item" label="Item"></el-option>
                                <el-option key="both" value="both" label="Ambos"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.level" v-text="errors.level[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-8">
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
        props: ['showDialog', 'type', 'recordId'],
        data() {
            return {
                loading_submit: false,
                titleDialog: null,
                resource: 'charge_discounts',
                errors: {},
                form: {},
                charge_discount_types: [],
            }
        },
        created() {
            this.initForm()
            this.$http.get(`/${this.resource}/tables/${this.type}`)
                .then(response => {
                    this.charge_discount_types = response.data.charge_discount_types
                })
        },
        methods: {
            initForm() {
                this.errors = {}
                this.form = {
                    id: null,
                    charge_discount_type_id: null,
                    type: this.type,
                    base: false,
                    level: 'both',
                    description: null,
                    percentage: null,
                }
            },
            create() {
                if (this.type === 'charge') {
                    this.titleDialog = (this.recordId)? 'Editar Cargo':'Nuevo Cargo'
                } else {
                    this.titleDialog = (this.recordId)? 'Editar Descuento':'Nuevo Descuento'
                }
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
                            this.errors = error.response.data.errors
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

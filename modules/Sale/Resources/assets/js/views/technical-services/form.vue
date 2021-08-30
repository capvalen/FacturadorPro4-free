nant<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @close="close" @open="create" append-to-body>
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-lg-12 pb-2">
                        <div class="form-group" :class="{'has-danger': errors.customer_id}">
                            <label class="control-label font-weight-bold text-info">
                                Cliente
                                <a href="#" @click.prevent="showDialogNewPerson = true">[+ Nuevo]</a>
                            </label>
                            <el-select v-model="form.customer_id" filterable remote class="border-left rounded-left border-info" popper-class="el-select-customers"
                                dusk="customer_id"
                                placeholder="Escriba el nombre o número de documento del cliente"
                                :remote-method="searchRemoteCustomers"
                                :loading="loading_search">

                                <el-option v-for="option in customers" :key="option.id" :value="option.id" :label="option.description"></el-option>

                            </el-select>
                            <small class="form-control-feedback" v-if="errors.customer_id" v-text="errors.customer_id[0]"></small>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group" :class="{'has-danger': errors.description}">
                            <label class="control-label">Descripción </label>
                            <el-input type="textarea" v-model="form.description" ></el-input>
                            <small class="form-control-feedback" v-if="errors.description" v-text="errors.description[0]"></small>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group" :class="{'has-danger': errors.state}">
                            <label class="control-label">Estado </label>
                            <el-input type="textarea" v-model="form.state" ></el-input>
                            <small class="form-control-feedback" v-if="errors.state" v-text="errors.state[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group" :class="{'has-danger': errors.reason}">
                            <label class="control-label">Motivo de ingreso</label>
                            <el-input type="textarea" v-model="form.reason" ></el-input>
                            <small class="form-control-feedback" v-if="errors.reason" v-text="errors.reason[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.cellphone}">
                            <label class="control-label">Celular </label>
                            <el-input v-model="form.cellphone" ></el-input>
                            <small class="form-control-feedback" v-if="errors.cellphone" v-text="errors.cellphone[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.serial_number}">
                            <label class="control-label">Número de serie </label>
                            <el-input v-model="form.serial_number" ></el-input>
                            <small class="form-control-feedback" v-if="errors.serial_number" v-text="errors.serial_number[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.cost}">
                            <label class="control-label">Costo </label>
                            <el-input v-model="form.cost" ></el-input>
                            <small class="form-control-feedback" v-if="errors.cost" v-text="errors.cost[0]"></small>
                        </div>
                    </div>
                    <!-- <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.prepayment}">
                            <label class="control-label">Pago adelantado </label>
                            <el-input v-model="form.prepayment" ></el-input>
                            <small class="form-control-feedback" v-if="errors.prepayment" v-text="errors.prepayment[0]"></small>
                        </div>
                    </div> -->
                    <div class="col-md-12" v-if="recordId">
                        <div class="form-group" :class="{'has-danger': errors.activities}">
                            <label class="control-label">Actividades realizadas (Equipo internado)</label>
                            <el-input type="textarea" v-model="form.activities" ></el-input>
                            <small class="form-control-feedback" v-if="errors.activities" v-text="errors.activities[0]"></small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.brand}">
                            <label class="control-label">Marca </label>
                            <el-input v-model="form.brand" ></el-input>
                            <small class="form-control-feedback" v-if="errors.brand" v-text="errors.brand[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.equipment}">
                            <label class="control-label">Equipo </label>
                            <el-input v-model="form.equipment" ></el-input>
                            <small class="form-control-feedback" v-if="errors.equipment" v-text="errors.equipment[0]"></small>
                        </div>
                    </div>
                </div>
                <div class="row" style="padding:2%;">
                    <div class="col-md-3"><el-checkbox v-model="form.repair">Reparación</el-checkbox></div>
                    <div class="col-md-3"><el-checkbox v-model="form.warranty">Garantía</el-checkbox></div>
                    <div class="col-md-3"><el-checkbox v-model="form.maintenance">Mantenimiento</el-checkbox></div>
                    <div class="col-md-3"><el-checkbox v-model="form.diagnosis">Diagnostico</el-checkbox></div>
                </div>
                <div class="row">
                    <div class="col-md-12">

                        <div class="form-group">
                            <label class="control-label">
                                    Notas
                                <a href="#" @click.prevent="clickAddNote" class="text-center font-weight-bold text-info">[+ Agregar]</a>
                            </label>

                            <table style="width: 100%">
                                <tr v-for="(guide,index) in form.important_note" :key="index">
                                    <td>
                                        <el-input v-model="guide.description"></el-input>
                                    </td>
                                    <td align="center">
                                        <button  type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickRemoveGuide(index)">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                        <!-- <a href="#" @click.prevent="clickRemoveGuide" style="color:red">Remover</a> -->
                                    </td>
                                </tr>
                            </table>

                        </div>

                    </div>


                </div>

            </div>
            <div class="form-actions text-right mt-4">
                <el-button @click.prevent="close()">Cancelar</el-button>
                <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
            </div>
        </form>


        <person-form :showDialog.sync="showDialogNewPerson"
                       type="customers"
                       :external="true"
                       :document_type_id = form.document_type_id></person-form>
    </el-dialog>
</template>

<script>
    import PersonForm from '@views/persons/form.vue'

    export default {
        props: ['showDialog', 'recordId'],
        components: {PersonForm},
        data() {
            return {
                showDialogNewPerson: false,
                loading_submit: false,
                all_customers: [],
                customers: [],
                titleDialog: null,
                resource: 'technical-services',
                loading_search:false,
                errors: {},
                form: {},
            }
        },
        async created() {
            await this.initForm()
            await this.$http.get(`/${this.resource}/tables`)
                .then(response => {
                    this.all_customers = response.data.customers
                    this.allCustomers()
                })

            this.$eventHub.$on('reloadDataPersons', (customer_id) => {
                this.reloadDataCustomers(customer_id)
            })
        },
        methods: {
            clickAddNote()
            {
                if(this.form.important_note.length == 2)
                {
                    return
                }

                 this.form.important_note.push({
                    description: null,
                })
            },
             clickRemoveGuide(index) {
                this.form.important_note.splice(index, 1)
            },
            searchRemoteCustomers(input) {

                if (input.length > 0) {
                    this.loading_search = true
                    let parameters = `input=${input}`

                    this.$http.get(`/${this.resource}/search/customers?${parameters}`)
                            .then(response => {
                                this.customers = response.data.customers
                                this.loading_search = false
                                if(this.customers.length == 0){this.allCustomers()}
                            })
                } else {
                    this.allCustomers()
                }

            },
            allCustomers() {
                this.customers = this.all_customers
            },
            reloadDataCustomers(customer_id) {
                this.$http.get(`/${this.resource}/search/customer/${customer_id}`).then((response) => {
                    this.customers = response.data.customers
                    this.form.customer_id = customer_id
                })
            },
            initForm() {
                this.errors = {}
                this.form = {
                    id: null,
                    customer_id: null,
                    cellphone: null,
                    date_of_issue: moment().format('YYYY-MM-DD'),
                    time_of_issue: moment().format('HH:mm:ss'),
                    description: null,
                    state: null,
                    reason: null,
                    serial_number: null,
                    activities: null,
                    cost: 0,
                    prepayment: 0,
                    equipment: null,
                    brand:null,
                    repair:false,
                    warranty:false,
                    maintenance:false,
                    diagnosis:false,
                    important_note:[]


                }
            },
            create() {

                this.titleDialog = (this.recordId)? 'Editar servicio técnico':'Nuevo servicio técnico'

                if (this.recordId) {
                    this.$http.get(`/${this.resource}/record/${this.recordId}`)
                        .then(response => {
                            this.form = response.data.data
                            this.reloadDataCustomers(this.form.customer_id)
                        })
                }
            },
            submit() {

                if(parseFloat(this.form.prepayment) > parseFloat(this.form.cost)){
                    return this.$message.error('Pago adelantado no puede ser mayor al costo')
                }

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

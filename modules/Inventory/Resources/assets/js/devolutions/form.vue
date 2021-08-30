<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <!-- <div class="card-header bg-info">
            <h3 class="my-0">Nuevo Comprobante</h3>
        </div> -->
        <div class="tab-content" v-if="loading_form">
            <div class="invoice">
                <header class="clearfix">
                    <div class="row">
                        <div class="col-sm-2 text-center mt-3 mb-0">
                            <logo url="/" :path_logo="(company.logo != null) ? `/storage/uploads/logos/${company.logo}` : ''" ></logo>
                        </div>
                        <div class="col-sm-10 text-left mt-3 mb-0">
                            <address class="ib mr-2" >
                                <span class="font-weight-bold d-block">Documento de Devolución</span>
                                <span class="font-weight-bold d-block">DV-XXX</span>
                                <span class="font-weight-bold">{{company.name}}</span>
                                <br>
                                <div v-if="establishment.address != '-'">{{ establishment.address }}, </div> {{ establishment.district.description }}, {{ establishment.province.description }}, {{ establishment.department.description }} - {{ establishment.country.description }}
                                <br>
                                {{establishment.email}} - <span v-if="establishment.telephone != '-'">{{establishment.telephone}}</span>
                            </address>
                        </div>
                    </div>
                </header>
                <form autocomplete="off" @submit.prevent="submit">
                    <div class="form-body">
                        <div class="row mt-1">
                             <div class="col-lg-4 pb-2">
                                <div class="form-group" :class="{'has-danger': errors.devolution_reason_id}">
                                    <label class="control-label font-weight-bold text-info">
                                        Motivo
                                    </label>
                                    <el-select v-model="form.devolution_reason_id" filterable  class="border-left rounded-left border-info" popper-class="el-select-customers">
                                        <el-option v-for="option in devolution_reasons" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                    </el-select>
                                    <small class="form-control-feedback" v-if="errors.devolution_reason_id" v-text="errors.devolution_reason_id[0]"></small>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group" :class="{'has-danger': errors.date_of_issue}">
                                    <label class="control-label">Fec. Emisión</label>
                                    <el-date-picker v-model="form.date_of_issue" type="date" value-format="yyyy-MM-dd" :clearable="false" ></el-date-picker>
                                    <small class="form-control-feedback" v-if="errors.date_of_issue" v-text="errors.date_of_issue[0]"></small>
                                </div>
                            </div>   
                            <div class="col-lg-6">
                                <div class="form-group" :class="{'has-danger': errors.observation}">
                                    <label class="control-label">Observación
                                    </label>
                                    <el-input  type="textarea"  :rows="3" v-model="form.observation"></el-input>
                                    <small class="form-control-feedback" v-if="errors.observation" v-text="errors.observation[0]"></small>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th class="font-weight-bold">Descripción</th>
                                                <th class="text-center font-weight-bold">Unidad</th>
                                                <th class="text-right font-weight-bold">Cantidad</th>
                                                <th class="text-right font-weight-bold">Lote</th>
                                                <th class="text-right font-weight-bold">F. Vencimiento</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody v-if="form.items.length > 0">
                                            <tr v-for="(row, index) in form.items" :key="index">
                                                <td>{{index + 1}}</td>
                                                <td>{{row.item.description}} </td>
                                                <td class="text-center">{{row.item.unit_type_id}}</td>
                                                <td class="text-right">{{row.quantity}}</td>
                                                <td class="text-right">{{ (row.item.lot_selected) ? row.item.lot_selected.code : ''}}</td>
                                                <td class="text-right">{{(row.item.lot_selected) ? row.item.lot_selected.date_of_due : '' }}</td>
                                                <td class="text-right">
                                                    <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickRemoveItem(index)">x</button>
                                                </td>
                                            </tr>
                                            <tr><td colspan="7"></td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-6 d-flex align-items-end">
                                <div class="form-group">
                                    <button type="button" class="btn waves-effect waves-light btn-primary" @click.prevent="showDialogAddItem = true">+ Agregar Producto</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions text-right mt-4">
                        <el-button @click.prevent="close()">Cancelar</el-button>
                        <el-button class="submit" type="primary" native-type="submit" :loading="loading_submit" v-if="form.items.length > 0">Generar</el-button>
                    </div>
                </form>
            </div>
        </div>

        <devolution-form-item :showDialog.sync="showDialogAddItem"
                           @add="addRow"></devolution-form-item>
 
    </div>
</template>

<script>
    import DevolutionFormItem from './partials/item.vue'
    import Logo from '@views/companies/logo.vue'

    export default {
        props:['typeUser'],
        components: {DevolutionFormItem, Logo},
        data() {
            return {
                resource: 'devolutions',
                showDialogAddItem: false,
                showDialogOptions: false,
                loading_submit: false,
                loading_form: false,
                errors: {},
                form: {},
                devolution_reasons: [],
                company: null,
                establishments: [],
                establishment: null,
            }
        },
        async created() {
            await this.initForm()
            await this.$http.get(`/${this.resource}/tables`)
                .then(response => {
                    this.devolution_reasons = response.data.devolution_reasons
                    this.establishments = response.data.establishments
                    this.company = response.data.company
                    this.form.establishment_id = (this.establishments.length > 0)?this.establishments[0].id:null

                    this.changeEstablishment()
                })

            this.loading_form = true
        },
        methods: {
            initForm() {

                this.errors = {}
                this.form = {
                    prefix:'DV',
                    establishment_id: null,
                    date_of_issue: moment().format('YYYY-MM-DD'),
                    time_of_issue: moment().format('HH:mm:ss'),
                    items: [],
                    observation: null,
                    devolution_reason_id: null,
                }

            },
            resetForm() {
                this.initForm()
                this.form.establishment_id = (this.establishments.length > 0)?this.establishments[0].id:null
                this.changeEstablishment()
            },
            changeEstablishment() {
                this.establishment = _.find(this.establishments, {'id': this.form.establishment_id})
            },
            addRow(row) {
                this.form.items.push(JSON.parse(JSON.stringify(row)));
            },
            clickRemoveItem(index) {
                this.form.items.splice(index, 1)
                this.calculateTotal()
            },
            async submit() {

                this.loading_submit = true

                await this.$http.post(`/${this.resource}`, this.form).then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        this.$eventHub.$emit('reloadDataItems')
                        this.resetForm()
                    }
                    else {
                        this.$message.error(response.data.message);
                    }
                }).catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data;
                    }
                    else {
                        this.$message.error(error.response.data.message);
                    }
                }).then(() => {
                    this.loading_submit = false;
                });
            },
            close() {
                location.href = `/${this.resource}`
            },
        }
    }
</script>

<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <div class="card-header bg-info">
            <h3 class="my-0">{{title}}</h3>
        </div>
        <div class="card-body">
            <form autocomplete="off" @submit.prevent="submit">
                <div class="form-body">
                    <div class="row">
                        <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.establishment_id}">
                                <label class="control-label font-weight-bold">Establecimiento<span class="text-danger"> *</span></label>
                                <el-select v-model="form.establishment_id" @change="changeEstablishment">
                                    <el-option v-for="option in establishments" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.establishment_id" v-text="errors.establishment_id[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.date_of_issue}">
                                <label class="control-label font-weight-bold">Fecha de emisión<span class="text-danger"> *</span></label>
                                <el-date-picker v-model="form.date_of_issue" type="date" value-format="yyyy-MM-dd" :clearable="false"></el-date-picker>
                                <small class="form-control-feedback" v-if="errors.date_of_issue" v-text="errors.date_of_issue[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.date_of_shipping}">
                                <label class="control-label font-weight-bold">Fecha de traslado<span class="text-danger"> *</span></label>
                                <el-date-picker v-model="form.date_of_shipping" type="date" value-format="yyyy-MM-dd" :clearable="false"></el-date-picker>
                                <small class="form-control-feedback" v-if="errors.date_of_shipping" v-text="errors.date_of_shipping[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group" :class="{'has-danger': errors.customer_id}">
                                <label class="control-label font-weight-bold">
                                    Cliente<span class="text-danger"> *</span>
                                    <a href="#" @click.prevent="showDialogNewPerson = true">[+ Nuevo]</a>
                                </label>
                                <el-select v-model="form.customer_id" filterable>
                                    <el-option v-for="option in customers" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.customer_id" v-text="errors.customer_id[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.transport_mode_type_id}">
                                <label class="control-label font-weight-bold">Modo de translado<span class="text-danger"> *</span></label>
                                <el-select v-model="form.transport_mode_type_id">
                                    <el-option v-for="option in transportModeTypes" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.transport_mode_type_id" v-text="errors.transport_mode_type_id[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group" :class="{'has-danger': errors.transfer_reason_type_id}">
                                <label class="control-label font-weight-bold">Motivo de translado<span class="text-danger"> *</span></label>
                                <el-select v-model="form.transfer_reason_type_id">
                                    <el-option v-for="option in transferReasonTypes" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.transfer_reason_type_id" v-text="errors.transfer_reason_type_id[0]"></small>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group" :class="{'has-danger': errors.transfer_reason_description}">
                                <label class="control-label font-weight-bold">Descripción de motivo de traslado<span class="text-danger"> *</span></label>
                                <el-input type="textarea" :rows="3" placeholder="Descripción de motivo de traslado..." v-model="form.transfer_reason_description" maxlength="100"></el-input>
                                <small class="form-control-feedback" v-if="errors.transfer_reason_description" v-text="errors.transfer_reason_description[0]"></small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.unit_type_id}">
                                <label class="control-label font-weight-bold">Unidad de medida<span class="text-danger"> *</span></label>
                                <el-select v-model="form.unit_type_id">
                                    <el-option v-for="option in unitTypes" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.unit_type_id" v-text="errors.unit_type_id[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.total_weight}">
                                <label class="control-label font-weight-bold">Peso total<span class="text-danger"> *</span></label>
                                <el-input-number v-model="form.total_weight" :precision="2" :step="1" :min="0" :max="9999999999"></el-input-number>
                                <small class="form-control-feedback" v-if="errors.total_weight" v-text="errors.total_weight[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.packages_number}">
                                <label class="control-label font-weight-bold">Número de paquetes<span class="text-danger"> *</span></label>
                                <el-input-number v-model="form.packages_number" :precision="0" :step="1" :min="0" :max="9999999999"></el-input-number>
                                <small class="form-control-feedback" v-if="errors.packages_number" v-text="errors.packages_number[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group" :class="{'has-danger': errors.observations}">
                                <label class="control-label font-weight-bold">Observaciones<span class="text-danger"> *</span></label>
                                <el-input type="textarea" :rows="3" placeholder="Observaciones..." v-model="form.observations" maxlength="250"></el-input>
                                <small class="form-control-feedback" v-if="errors.observations" v-text="errors.observations[0]"></small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                    </div>
                    <div class="row">
                    </div>
                    <hr>
                    <h4>Datos envío</h4>
                    <h6>Dirección partida</h6>
                    <div class="row">
                        <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.origin}">
                                <label class="control-label font-weight-bold">País<span class="text-danger"> *</span></label>
                                <el-select v-model="form.origin.country_id" filterable>
                                    <el-option v-for="option in countries" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.origin" v-text="errors.origin.country_id[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group" :class="{'has-danger': errors.origin}">
                                <label class="control-label font-weight-bold">Ubigeo<span class="text-danger"> *</span></label>
                                <el-cascader :options="locations" v-model="form.origin.location_id" filterable></el-cascader>

                                <small class="form-control-feedback" v-if="errors.origin" v-text="errors.origin.location_id[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group" :class="{'has-danger': errors['origin.address']}">
                                <label class="control-label font-weight-bold">Dirección<span class="text-danger"> *</span></label>
                                <el-input v-model="form.origin.address" :maxlength="100" placeholder="Dirección..."></el-input>
                                <small class="form-control-feedback" v-if="errors['origin.address']" v-text="errors['origin.address'][0]"></small>
                            </div>
                        </div>
                    </div>
                    <h6>Dirección llegada</h6>
                    <div class="row">
                        <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.delivery}">
                                <label class="control-label font-weight-bold">País<span class="text-danger"> *</span></label>
                                <el-select v-model="form.delivery.country_id" filterable>
                                    <el-option v-for="option in countries" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.delivery" v-text="errors.delivery.country_id[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group" :class="{'has-danger': errors.delivery}">
                                <label class="control-label font-weight-bold">Ubigeo<span class="text-danger"> *</span></label>
                                <el-cascader :options="locations" v-model="form.delivery.location_id" filterable></el-cascader>

                                <small class="form-control-feedback" v-if="errors.delivery" v-text="errors.delivery.location_id[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group" :class="{'has-danger': errors['delivery.address']}">
                                <label class="control-label font-weight-bold">Dirección<span class="text-danger"> *</span></label>
                                <el-input v-model="form.delivery.address" :maxlength="100" placeholder="Dirección..."></el-input>
                                <small class="form-control-feedback" v-if="errors['delivery.address']" v-text="errors['delivery.address'][0]"></small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h4></h4>
                    <div class="row">

                        <div class="col-lg-6">
                            <div class="form-group" :class="{'has-danger': errors.dispatcher_id}">
                                <label class="control-label font-weight-bold">
                                    Transportista<span class="text-danger"> *</span>
                                    <a href="#" @click.prevent="showDialogNewDispatcher = true">[+ Nuevo]</a>
                                </label>
                                <el-select v-model="form.dispatcher_id" filterable>
                                    <el-option v-for="option in dispatchers" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.dispatcher_id" v-text="errors.dispatcher_id[0]"></small>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group" :class="{'has-danger': errors.driver_id}">
                                <label class="control-label font-weight-bold">
                                    Conductor<span class="text-danger"> *</span>
                                    <a href="#" @click.prevent="showDialogNewDriver = true">[+ Nuevo]</a>

                                </label>
                                <el-select v-model="form.driver_id" filterable>
                                    <el-option v-for="option in drivers" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.driver_id" v-text="errors.driver_id[0]"></small>
                            </div>
                        </div>
                    </div>
                    <h4></h4>
                    <div class="row">
                        <div class="col-lg-3">

                            <div class="form-group" :class="{'has-danger': errors['license_plates.license_plate_1']}">
                                <label class="control-label font-weight-bold">N° placa del vehiculo<span class="text-danger"> *</span></label>
                                <el-input v-model="form.license_plates.license_plate_1" @keyup.native="keyUpLicensePlate1" :maxlength="8"></el-input>
                                <small class="form-control-feedback" v-if="errors['license_plates.license_plate_1']" v-text="errors['license_plates.license_plate_1'][0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group" :class="{'has-danger': errors['license_plates.register_number_1']}">
                                <label class="control-label font-weight-bold">N° registro<span class="text-danger"> *</span></label>
                                <el-input v-model="form.license_plates.register_number_1" ></el-input>
                                <small class="form-control-feedback" v-if="errors['license_plates.register_number_1']" v-text="errors['license_plates.register_number_1'][0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group" :class="{'has-danger': errors['license_plates.license_plate_2']}">
                                <label class="control-label font-weight-bold">N° placa semirremolque<span class="text-danger"> *</span></label>
                                <el-input v-model="form.license_plates.license_plate_2" @keyup.native="keyUpLicensePlate2" :maxlength="8"></el-input>
                                <small class="form-control-feedback" v-if="errors['license_plates.license_plate_2']" v-text="errors['license_plates.license_plate_2'][0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group" :class="{'has-danger': errors['license_plates.register_number_2']}">
                                <label class="control-label font-weight-bold">N° registro<span class="text-danger"> *</span></label>
                                <el-input v-model="form.license_plates.register_number_2" ></el-input>
                                <small class="form-control-feedback" v-if="errors['license_plates.register_number_2']" v-text="errors['license_plates.register_number_2'][0]"></small>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th class="font-weight-bold">Unidad</th>
                                    <th class="font-weight-bold">Descripción</th>
                                    <th class="text-right font-weight-bold">Cantidad</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody v-if="form.items.length > 0">
                                <tr v-for="(row, index) in form.items" :key="index">
                                    <td>{{index + 1}}</td>
                                    <td>{{ row.unit_type_id ? row.unit_type_id : row.item.unit_type_id }}</td>
                                    <td>{{row.description ? row.description : row.item.description}}</td>
                                    <td class="text-right">{{row.quantity}}</td>
                                    <td class="text-right">
                                        <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickRemoveItem(index)">x</button>
                                        <button data-toggle="tooltip" data-placement="top" title="Editar" type="button" class="btn waves-effect waves-light btn-xs btn-primary"
                                            @click.prevent="clickEditItem(row)"><i class="fas fa-file-signature"></i></button>

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <button type="button" class="btn waves-effect waves-light btn-primary" @click.prevent="clickShowDialogFormItem">+ Agregar Producto</button>
                    </div>
                </div>
                <div class="form-actions text-right mt-4">
                    <el-button @click.prevent="close()">Cancelar</el-button>
                    <el-button type="primary" native-type="submit" :loading="loading_submit" v-if="(form.items.length > 0)">Generar</el-button>
                </div>
            </form>
        </div>

        <person-form :showDialog.sync="showDialogNewPerson" type="customers" :external="true"></person-form>

        <driver-form :showDialog.sync="showDialogNewDriver" :external="true"></driver-form>

        <dispatcher-form :showDialog.sync="showDialogNewDispatcher" :external="true"></dispatcher-form>

        <items :recordItem.sync="recordItem" :dialogVisible.sync="showDialogAddItems" @addItem="addItem"></items>


        <order-form-options :showDialog.sync="showDialogOptions"
                            :recordId="recordId"
                            :isUpdate="(id) ? true:false"></order-form-options>
    </div>
</template>

<script>
    import PersonForm from '@views/persons/form.vue';
    import DispatcherForm from '../dispatchers/form.vue';
    import DriverForm from '../drivers/form.vue';
    import OrderFormOptions from './partials/options.vue'
    import Items from './items.vue';

    export default {
        props: ['id'],
        components: {DriverForm, PersonForm, Items, OrderFormOptions, DispatcherForm},
        data() {
            return {
                showDialogNewDriver: false,
                showDialogNewDispatcher: false,
                showDialogNewPerson: false,
                identityDocumentTypes: [],
                showDialogOptions: false,
                showDialogAddItems: false,
                transferReasonTypes: [],
                transportModeTypes: [],
                resource: 'order-forms',
                establishment_id: null,
                loading_submit: false,
                provincesDelivery: [],
                districtsDelivery: [],
                provincesOrigin: [],
                districtsOrigin: [],
                establishments: [],
                districtsAll: [],
                provincesAll: [],
                departments: [],
                countries: [],
                seriesAll: [],
                unitTypes: [],
                customers: [],
                code: null,
                locations: [],
                series: [],
                dispatchers: [],
                drivers: [],
                errors: {
                    errors: {}
                },
                form: {},
                title: null,
                recordId:null,
                recordItem:null
            }
        },
        async created() {
            // this.clean();


            await this.initForm()

            await this.$http.post(`/${this.resource}/tables`).then(response => {
                this.identityDocumentTypes = response.data.identityDocumentTypes;
                this.transferReasonTypes = response.data.transferReasonTypes;
                this.transportModeTypes = response.data.transportModeTypes;
                this.establishments = response.data.establishments;
                this.departments = response.data.departments;
                this.provincesAll = response.data.provinces;
                this.districtsAll = response.data.districts;
                this.unitTypes = response.data.unitTypes;
                this.customers = response.data.customers;
                this.countries = response.data.countries;
                this.locations = response.data.locations;
                this.seriesAll = response.data.series;
                this.drivers = response.data.drivers;
                this.dispatchers = response.data.dispatchers;
            });

            await this.isUpdate()

            this.title = (this.id) ? 'Editar orden de pedido':'Nueva orden de pedido'

            this.events()
        },
        methods: {
            async keyUpLicensePlate1(e){

                if(this.form.license_plates.license_plate_1.length == 3 && e.keyCode !== 8){
                    this.form.license_plates.license_plate_1 = await this.form.license_plates.license_plate_1.concat('-')
                }

            },
            async keyUpLicensePlate2(e){

                if(this.form.license_plates.license_plate_2.length == 3 && e.keyCode !== 8){
                    this.form.license_plates.license_plate_2 = await this.form.license_plates.license_plate_2.concat('-')
                }

            },
            events(){

                this.$eventHub.$on('reloadDataDrivers', (driver_id) => {
                    this.reloadDataDrivers(driver_id)
                })

                this.$eventHub.$on('reloadDataDispatchers', (dispatcher_id) => {
                    this.reloadDataDispatchers(dispatcher_id)
                })

            },
            reloadDataDispatchers(dispatcher_id) {
                this.$http.get(`/${this.resource}/table/dispatchers`).then((response) => {
                    this.dispatchers = response.data
                    this.form.dispatcher_id = dispatcher_id
                })
            },
            reloadDataDrivers(driver_id) {
                this.$http.get(`/${this.resource}/table/drivers`).then((response) => {
                    this.drivers = response.data
                    this.form.driver_id = driver_id
                })
            },
            isUpdate(){

                if(this.id){

                    this.$http.get(`/${this.resource}/record/${this.id}` )
                    .then(response => {

                        let order_form = response.data.data.order_form
                        // console.log(order_form)
                        this.form = order_form

                    })
                }

            },
            initForm() {
                this.errors = {}
                this.form = {
                    id: null,
                    external_id: null,
                    establishment_id: null,
                    document_type_id: '09',
                    number: '#',
                    date_of_issue: moment().format('YYYY-MM-DD'),
                    time_of_issue: moment().format('HH:mm:ss'),
                    date_of_shipping: moment().format('YYYY-MM-DD'),
                    customer_id: null,
                    observations: '',
                    transport_mode_type_id: null,
                    transfer_reason_type_id: null,
                    transfer_reason_description: null,
                    transshipment_indicator: false,
                    port_code: null,
                    unit_type_id: null,
                    total_weight: 0,
                    packages_number: null,
                    container_number: null,
                    delivery: {
                        country_id: 'PE',
                        location_id: [],
                        address: null,
                    },
                    origin: {
                        country_id: 'PE',
                        location_id: [],
                        address: null,
                    },
                    items: [],
                    dispatcher_id: null,
                    driver_id: null,
                    license_plates: {
                        license_plate_1: null,
                        license_plate_2: null,
                        register_number_1: null,
                        register_number_2: null,
                    },


                }
            },
            changeEstablishment() {
                this.code = this.form.establishment_id;
                this.establishment_id = this.form.establishment_id;
            },
            filterProvince(origin = true) {
                if (origin) {
                    this.provincesOrigin = _.filter(this.provincesAll, {
                        'department_id': this.form.origin.department_id
                    });

                    this.$set(this.form.origin, 'province_id', null);
                    this.$set(this.form.origin, 'location_id', null);

                    return;
                }

                this.provincesDelivery = _.filter(this.provincesAll, {
                    'department_id': this.form.delivery.department_id
                });

                this.$set(this.form.delivery, 'province_id', null);
                this.$set(this.form.delivery, 'location_id', null);
            },
            filterDistrict(origin = true) {
                if (origin) {
                    this.districtsOrigin = _.filter(this.districtsAll, {
                        'province_id': this.form.origin.province_id
                    });

                    this.$set(this.form.origin, 'location_id', null);

                    return;
                }

                this.districtsDelivery = _.filter(this.districtsAll, {
                    'province_id': this.form.delivery.province_id
                });

                this.$set(this.form.delivery, 'location_id', null);
            },
            addItem(form) {

                let exist = this.form.items.find((item) => item.id == form.item.id);

                if (exist) {
                    exist.quantity += form.quantity;
                    return;
                }

                this.form.items.push({
                    'description': form.item.description,
                    'internal_id': form.item.internal_id,
                    'quantity': form.quantity,
                    'item_id': form.item.id,
                    'unit_type_id': form.item.unit_type_id,
                    'id': form.item.id,
                });
            },
            clickRemoveItem(index) {
                this.form.items.splice(index, 1);
            },
            clickEditItem(item)
            {
                this.recordItem = item
                this.showDialogAddItems = true
            },
            submit() {

                // console.log(this.form)
                if(this.form.origin.location_id.length != 3 || this.form.delivery.location_id.length != 3)
                    return this.$message.error('El campo ubigeo es obligatorio')

                this.loading_submit = true;

                this.form.url =  window.location.origin

                this.$http.post(`/${this.resource}`, this.form).then(response => {
                        if (response.data.success) {
                            this.initForm();
                            this.recordId = response.data.data.id
                            this.showDialogOptions = true
                        }
                        else {
                            this.$message.error(response.data.message);
                        }
                    }).catch(error => {
                        this.loading_submit = false;

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
                location.href = '/order-forms';
            },
            clickShowDialogFormItem()
            {
                this.recordItem = null
                this.showDialogAddItems = true
            }
        }
    }
</script>

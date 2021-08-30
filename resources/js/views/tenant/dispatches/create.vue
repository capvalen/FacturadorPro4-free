<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <div class="card-header bg-info">
            <h3 class="my-0">Nueva Guía de Remisión</h3>
        </div>
        <div class="card-body">
            <form autocomplete="off" @submit.prevent="submit">
                <div class="form-body">
                    <div class="row">
                        <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.establishment}">
                                <label class="control-label">Establecimiento<span class="text-danger"> *</span></label>
                                <el-select v-model="form.establishment_id" @change="changeEstablishment">
                                    <el-option v-for="option in establishments" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.establishment" v-text="errors.establishment[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.series}">
                                <label class="control-label">Serie<span class="text-danger"> *</span></label>
                                <el-select v-model="form.series_id">
                                    <el-option v-for="option in series" :key="option.id" :value="option.id" :label="option.number"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.series_id" v-text="errors.series_id[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.date_of_issue}">
                                <label class="control-label">Fecha de emisión<span class="text-danger"> *</span></label>
                                <el-date-picker v-model="form.date_of_issue" type="date" value-format="yyyy-MM-dd" :clearable="false"></el-date-picker>
                                <small class="form-control-feedback" v-if="errors.date_of_issue" v-text="errors.date_of_issue[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.date_of_shipping}">
                                <label class="control-label">Fecha de traslado<span class="text-danger"> *</span></label>
                                <el-date-picker v-model="form.date_of_shipping" type="date" value-format="yyyy-MM-dd" :clearable="false"></el-date-picker>
                                <small class="form-control-feedback" v-if="errors.date_of_shipping" v-text="errors.date_of_shipping[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group" :class="{'has-danger': errors.customer_id}">
                                <label class="control-label">
                                    Cliente<span class="text-danger"> *</span>
                                    <a href="#" @click.prevent="showDialogNewPerson = true">[+ Nuevo]</a>
                                </label>
                                <el-select v-model="form.customer_id" filterable @change="changeCustomer">
                                    <el-option v-for="option in customers" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.customer_id" v-text="errors.customer_id[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.transport_mode_type_id}">
                                <label class="control-label">Modo de translado<span class="text-danger"> *</span></label>
                                <el-select v-model="form.transport_mode_type_id">
                                    <el-option v-for="option in transportModeTypes" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.transport_mode_type_id" v-text="errors.transport_mode_type_id[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group" :class="{'has-danger': errors.transfer_reason_type_id}">
                                <label class="control-label">Motivo de translado<span class="text-danger"> *</span></label>
                                <el-select v-model="form.transfer_reason_type_id">
                                    <el-option v-for="option in transferReasonTypes" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.transfer_reason_type_id" v-text="errors.transfer_reason_type_id[0]"></small>
                            </div>
                        </div>
                        <!-- <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.port_code}">
                                <label class="control-label">Codigo del Puerto</label>
                                <el-input v-model="form.port_code" maxlength="3"></el-input>
                                <small class="form-control-feedback" v-if="errors.port_code" v-text="errors.port_code[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.transshipment_indicator}">
                                <label class="control-label">Transbordo</label>
                                <div class="form-group">
                                    <el-radio v-model="form.transshipment_indicator" label="1">Si</el-radio>
                                    <el-radio v-model="form.transshipment_indicator" label="0">No</el-radio>
                                </div>
                                <small class="form-control-feedback" v-if="errors.transshipment_indicator" v-text="errors.transshipment_indicator[0]"></small>
                            </div>
                        </div> -->

                        <div class="col-lg-6">
                            <div class="form-group" :class="{'has-danger': errors.transfer_reason_description}">
                                <label class="control-label">Descripción de motivo de traslado</label>
                                <el-input type="textarea" :rows="3" placeholder="Descripción de motivo de traslado..." v-model="form.transfer_reason_description" maxlength="100"></el-input>
                                <small class="form-control-feedback" v-if="errors.transfer_reason_description" v-text="errors.transfer_reason_description[0]"></small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.unit_type_id}">
                                <label class="control-label">Unidad de medida<span class="text-danger"> *</span></label>
                                <el-select v-model="form.unit_type_id">
                                    <el-option v-for="option in unitTypes" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.unit_type_id" v-text="errors.unit_type_id[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.total_weight}">
                                <label class="control-label">Peso total<span class="text-danger"> *</span></label>
                                <el-input-number v-model="form.total_weight" :precision="2" :step="1" :min="0" :max="9999999999"></el-input-number>
                                <small class="form-control-feedback" v-if="errors.total_weight" v-text="errors.total_weight[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.packages_number}">
                                <label class="control-label">Número de paquetes<span class="text-danger"> *</span></label>
                                <el-input-number v-model="form.packages_number" :precision="0" :step="1" :min="0" :max="9999999999"></el-input-number>
                                <small class="form-control-feedback" v-if="errors.packages_number" v-text="errors.packages_number[0]"></small>
                            </div>
                        </div>
                        <!-- <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.container_number}">
                                <label class="control-label">Número de contenedor</label>
                                <el-input-number v-model="form.container_number" :precision="0" :step="1" :min="0" :max="9999999999"></el-input-number>
                                <small class="form-control-feedback" v-if="errors.container_number" v-text="errors.container_number[0]"></small>
                            </div>
                        </div> -->
                        <div class="col-lg-6">
                            <div class="form-group" :class="{'has-danger': errors.observations}">
                                <label class="control-label">Observaciones</label>
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
                                <label class="control-label">País<span class="text-danger"> *</span></label>
                                <el-select v-model="form.origin.country_id" filterable>
                                    <el-option v-for="option in countries" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.origin" v-text="errors.origin.country_id[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group" :class="{'has-danger': errors.origin}">
                                <label class="control-label">Ubigeo<span class="text-danger"> *</span></label>
                                <el-cascader :options="locations" v-model="form.origin.location_id" filterable></el-cascader>
                                <!--<el-select v-model="form.delivery.department_id" filterable @change="filterProvince(false)">-->
                                <!--<el-option v-for="option in departments" :key="option.id" :value="option.id" :label="option.description"></el-option>-->
                                <!--</el-select>-->
                                <small class="form-control-feedback" v-if="errors.origin" v-text="errors.origin.location_id[0]"></small>
                            </div>
                        </div>
                        <!-- <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.origin}">
                                <label class="control-label">Provincia</label>
                                <el-select v-model="form.origin.province_id" filterable @change="filterDistrict">
                                    <el-option v-for="option in provincesOrigin" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.origin" v-text="errors.origin.province_id[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.origin}">
                                <label class="control-label">Distrito</label>
                                <el-select v-model="form.origin.location_id" filterable>
                                    <el-option v-for="option in districtsOrigin" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.origin" v-text="errors.origin.location_id[0]"></small>
                            </div>
                        </div> -->
                        <div class="col-lg-6">
                            <div class="form-group" :class="{'has-danger': errors['origin.address']}">
                                <label class="control-label">Dirección<span class="text-danger"> *</span></label>
                                <el-input v-model="form.origin.address" :maxlength="100" placeholder="Dirección..."></el-input>
                                <small class="form-control-feedback" v-if="errors['origin.address']" v-text="errors['origin.address'][0]"></small>
                            </div>
                        </div>
                    </div>
                    <h6>Dirección llegada</h6>
                    <div class="row">
                        <div class="col-lg-2">
                            <div class="form-group" :class="{'has-danger': errors.delivery}">
                                <label class="control-label">País<span class="text-danger"> *</span></label>
                                <el-select v-model="form.delivery.country_id" filterable>
                                    <el-option v-for="option in countries" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.delivery" v-text="errors.delivery.country_id[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group" :class="{'has-danger': errors.delivery}">
                                <label class="control-label">Ubigeo<span class="text-danger"> *</span></label>
                                <el-cascader :options="locations" v-model="form.delivery.location_id" filterable></el-cascader>
                                <!--<el-select v-model="form.delivery.department_id" filterable @change="filterProvince(false)">-->
                                    <!--<el-option v-for="option in departments" :key="option.id" :value="option.id" :label="option.description"></el-option>-->
                                <!--</el-select>-->
                                <small class="form-control-feedback" v-if="errors.delivery" v-text="errors.delivery.location_id[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group" :class="{'has-danger': errors['delivery.address']}">
                                <label class="control-label">Dirección<span class="text-danger"> *</span></label>
                                <el-select placeholder="Dirección..." @change="onChangeAddress" v-model="form.delivery.address_id">
                                    <el-option v-for="(ad, i) in customerAddresses" :key="i" :label="ad.address" :value="ad.address"></el-option>
                                </el-select>
                                <!-- <el-input v-model="form.delivery.address" :maxlength="100" placeholder="Dirección..."></el-input> -->
                                <small class="form-control-feedback" v-if="errors['delivery.address']" v-text="errors['delivery.address'][0]"></small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h4>Datos transportista</h4>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group" :class="{'has-danger': errors['dispatcher.identity_document_type_id']}">
                                <label class="control-label">Tipo Doc. Identidad<span class="text-danger"> *</span></label>
                                <el-select v-model="form.dispatcher.identity_document_type_id" filterable>
                                    <el-option v-for="option in identityDocumentTypes" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors['dispatcher.identity_document_type_id']" v-text="errors['dispatcher.identity_document_type_id'][0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group" :class="{'has-danger': errors['dispatcher.number']}">
                                <label class="control-label">Número<span class="text-danger"> *</span></label>
                                <el-input v-model="form.dispatcher.number" :maxlength="11" placeholder="Número..."></el-input>
                                <small class="form-control-feedback" v-if="errors['dispatcher.number']" v-text="errors['dispatcher.number'][0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group" :class="{'has-danger': errors['dispatcher.name']}">
                                <label class="control-label">Nombre y/o razón social<span class="text-danger"> *</span></label>
                                <el-input v-model="form.dispatcher.name" :maxlength="100" placeholder="Nombre y/o razón social..."></el-input>
                                <small class="form-control-feedback" v-if="errors['dispatcher.name']" v-text="errors['dispatcher.name'][0]"></small>
                            </div>
                        </div>
                    </div>
                    <h4>Datos conductor</h4>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group" :class="{'has-danger': errors['driver.identity_document_type_id']}">
                                <label class="control-label">Tipo Doc. Identidad<span class="text-danger"> *</span></label>
                                <el-select v-model="form.driver.identity_document_type_id" filterable>
                                    <el-option v-for="option in identityDocumentTypes" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors['driver.identity_document_type_id']" v-text="errors['driver.identity_document_type_id'][0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group" :class="{'has-danger': errors['driver.number']}">
                                <label class="control-label">Número<span class="text-danger"> *</span></label>
                                <el-input v-model="form.driver.number" :maxlength="11" placeholder="Número..."></el-input>
                                <small class="form-control-feedback" v-if="errors['driver.number']" v-text="errors['driver.number'][0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group" :class="{'has-danger': errors.license_plate}">
                                <label class="control-label">Numero de placa del vehiculo<span class="text-danger"> *</span></label>
                                <el-input v-model="form.license_plate" :maxlength="8" placeholder="Numero de placa del vehiculo..."></el-input>
                                <small class="form-control-feedback" v-if="errors.license_plate" v-text="errors.license_plate[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group" >
                                <label class="control-label">Licencia del conductor</label>
                                <el-input v-model="form.driver.license" ></el-input>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group" >
                                <label class="control-label">N° placa semirremolque</label>
                                <el-input v-model="form.secondary_license_plates.semitrailer" ></el-input>
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
                                <tr v-for="(row, index) in form.items">
                                    <td>{{index + 1}}</td>
                                    <td>{{ row.unit_type_id }}</td>
                                    <td>{{row.description}}</td>
                                    <td class="text-right">{{row.quantity}}</td>
                                    <td class="text-right">
                                        <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickRemoveItem(index)">x</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <button type="button" class="btn waves-effect waves-light btn-primary" @click.prevent="showDialogAddItems = true">+ Agregar Producto</button>
                    </div>
                </div>
                <div class="form-actions text-right mt-4">
                    <el-button @click.prevent="close()">Cancelar</el-button>
                    <el-button type="primary" native-type="submit" :loading="loading_submit" v-if="(form.items.length > 0)">Generar</el-button>
                </div>
            </form>
        </div>

        <person-form :showDialog.sync="showDialogNewPerson" type="customers" :external="true"></person-form>

        <items :dialogVisible.sync="showDialogAddItems" @addItem="addItem"></items>

        <dispatch-options :showDialog.sync="showDialogOptions"
                            :recordId="recordId"
                            :showClose="false"
                            :isUpdate="(order_form_id) ? true:false"></dispatch-options>
    </div>
</template>

<script>
    import PersonForm from '../persons/form.vue';
    import Items from './items.vue';
    import DispatchOptions from './partials/options.vue'

    export default {
        props: ['order_form_id'],
        components: {PersonForm, Items, DispatchOptions, },
        data() {
            return {
                showDialogOptions: false,
                showDialogNewPerson: false,
                identityDocumentTypes: [],
                showDialogAddItems: false,
                transferReasonTypes: [],
                transportModeTypes: [],
                resource: 'dispatches',
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
                errors: {
                    errors: {}
                },
                form: {},
                recordId:null,
                company: {},
                customerAddresses: [],
            }
        },
        async created() {
            // this.clean();
            await this.initForm()

            const itemsFromSummary = localStorage.getItem('items');
            const payload = {}
            if (itemsFromSummary) {
                const items = JSON.parse(itemsFromSummary);
                payload.itemIds = items.map(i => i.id);
            }
            await this.$http.post(`/${this.resource}/tables`, payload).then(response => {
                this.company = response.data.company;
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
                if (itemsFromSummary) {
                    this.onLoadItemsFromSummary(response.data.itemsFromSummary, JSON.parse(itemsFromSummary));
                }
            });

            await this.setDefaultCustomer()

            await this.createFromOrderForm()
        },
        methods: {
            onChangeAddress() {
                const address = this.customerAddresses.find(ad => ad.address == this.form.delivery.address_id);

                this.form.delivery.address = address.address;
                if (address.country_id) {
                   this.form.delivery.country_id = address.country_id;
                }

                if (address.department_id && address.province_id && address.district_id) {
                    this.form.delivery.location_id = [address.department_id, address.province_id, address.district_id];
                }
            },
            changeCustomer() {
                this.customerAddresses = [];
                const customer = this.customers.find(i => i.id === this.form.customer_id);
                this.customerAddresses = customer.addresses;
                if (customer.address) {
                    this.customerAddresses.unshift({
                        id: null,
                        address: customer.address,
                        country_id: customer.country_id,
                        department_id: customer.department_id,
                        province_id: customer.province_id,
                        district_id: customer.district_id,
                    })
                }
            },
            onLoadItemsFromSummary(items, itemsFromStorage) {
                items.map(it => {
                    const itemWithQuantity = itemsFromStorage.find(i => i.id == it.id);
                    if (itemWithQuantity) {
                        this.addItem({
                            item: it,
                            quantity: itemWithQuantity.quantity
                        });
                    }
                });
                localStorage.removeItem('items');
            },
            setDefaultCustomer(){

                let customer = _.find(this.customers, {number: this.company.number})

                if(customer){
                    this.form.customer_id = customer.id
                }

            },
            createFromOrderForm(){

                if(this.order_form_id){

                    this.$http.get(`/order-forms/record/${this.order_form_id}` )
                        .then(response => {

                            let order_form = response.data.data.order_form
                            // this.form = order_form

                            this.form.establishment_id = order_form.establishment_id
                            this.form.establishment = order_form.establishment
                            this.form.date_of_issue = order_form.date_of_issue
                            this.form.customer_id = order_form.customer_id
                            this.form.customer = order_form.customer
                            this.form.observations = order_form.observations
                            this.form.transport_mode_type_id = order_form.transport_mode_type_id
                            this.form.transfer_reason_type_id = order_form.transfer_reason_type_id
                            this.form.transfer_reason_description = order_form.transfer_reason_description
                            this.form.date_of_shipping = order_form.date_of_shipping
                            this.form.transshipment_indicator = order_form.transshipment_indicator
                            this.form.port_code = order_form.port_code
                            this.form.unit_type_id = order_form.unit_type_id
                            this.form.total_weight = order_form.total_weight
                            this.form.packages_number = order_form.packages_number
                            this.form.container_number = order_form.container_number
                            this.form.origin = order_form.origin
                            this.form.delivery = order_form.delivery

                            //aqui
                            this.form.dispatcher = {
                                name: order_form.dispatcher.name,
                                number: order_form.dispatcher.number,
                                identity_document_type_id: order_form.dispatcher.identity_document_type_id,
                            }
                            this.form.driver = {
                                number: order_form.driver.number,
                                identity_document_type_id: order_form.driver.identity_document_type_id,
                            }

                            this.form.license_plate = order_form.license_plates.license_plate_1
                            this.form.reference_order_form_id = order_form.id
                            this.form.items = order_form.items

                            this.form.items.forEach(element => {
                                element.description = element.item.description
                                element.unit_type_id = element.item.unit_type_id
                            });

                            this.changeEstablishment()

                        })

                }
            },
            initForm() {
                this.errors = {}
                this.form = {
                    establishment_id: null,
                    document_type_id: '09',
                    series_id: null,
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
                    dispatcher: {
                        identity_document_type_id: null
                    },
                    driver: {
                        identity_document_type_id: null,
                        license: null,
                    },
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
                    // optional: {
                    //     invoice_number: this.document.series+'-'+this.document.number
                    // },
                    items: [],
                    reference_order_form_id: null,
                    license_plate:null,
                    secondary_license_plates: {
                        semitrailer: null
                    }

                }
            },
            changeEstablishment() {
                this.series = _.filter(this.seriesAll, {
                    'establishment_id': this.form.establishment_id,
                    'document_type_id': this.form.document_type_id
                });

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

                let attributes = null

                if(form.item.attributes){
                    attributes = form.item.attributes
                    this.incrementValueAttr(form)
                }

                if (exist) {
                    exist.quantity += form.quantity;
                    return;
                }
                let lot_group = null;
                if (form.item.IdLoteSelected) {
                    lot_group = form.item.lots_group.find(l => l.id == form.item.IdLoteSelected);
                }
                this.form.items.push({
                    attributes: attributes,
                    description: form.item.description,
                    internal_id: form.item.internal_id,
                    quantity: form.quantity,
                    item_id: form.item.id,
                    unit_type_id: form.item.unit_type_id,
                    id: form.item.id,
                    IdLoteSelected: form.item.IdLoteSelected || '',
                    lot_group: lot_group || null,
                });
            },
            decrementValueAttr(form){

                this.form.packages_number -= parseFloat(form.quantity)

                let total_weight = 0

                if(form.attributes){

                    form.attributes.forEach(attr => {
                        if(attr.attribute_type_id === '5032'){
                            total_weight -= parseFloat(attr.value) * parseFloat(form.quantity)
                        }
                    });
                }

                this.form.total_weight += total_weight
            },
            incrementValueAttr(form){

                this.form.packages_number += parseFloat(form.quantity)

                let total_weight = 0

                if(form.item.attributes){

                    form.item.attributes.forEach(attr => {
                        if(attr.attribute_type_id === '5032'){
                            total_weight += parseFloat(attr.value) * parseFloat(form.quantity)
                        }
                    });
                }

                this.form.total_weight += total_weight
            },
            clickRemoveItem(index) {
                this.decrementValueAttr(this.form.items[index])
                this.form.items.splice(index, 1);
            },
            async submit() {

                const validateQuantity = await  this.verifyQuantityItems()
                if(!validateQuantity.validate)
                {
                    return this.$message.error('Los productos no pueden tener cantidad 0.')
                }

                if(this.form.origin.location_id.length != 3 || this.form.delivery.location_id.length != 3)
                    return this.$message.error('El campo ubigeo es obligatorio')

                this.loading_submit = true;

                this.$http.post(`/${this.resource}`, this.form).then(response => {
                        if (response.data.success) {
                            this.initForm();

                            // this.$message.success(response.data.message)
                            this.recordId = response.data.data.id
                            this.showDialogOptions = true

                            // if(this.order_form_id){
                            //     this.close()
                            // }
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
            clean() {
                this.form = {
                    time_of_issue: moment().format('HH:mm:ss'),
                    dispatcher: {
                        identity_document_type_id: null
                    },
                    driver: {
                        identity_document_type_id: null
                    },
                    document_type_id: '09',
                    delivery: {
                        country_id: 'PE'
                    },
                    origin: {
                        country_id: 'PE'
                    },
                    number: '#',
                    items: [],
                    total_weight: null,
                    packages_number: null,
                    container_number: null
                }
            },
            close() {
                location.href = '/dispatches';
            },
            verifyQuantityItems()
            {
                let validate = true
                this.form.items.forEach( (element) => {

                    if(parseInt(element.quantity) < 1) validate= false

                })

                return { validate }
            }
        }
    }
</script>

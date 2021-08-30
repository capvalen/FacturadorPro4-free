<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @close="close">
        <Keypress
            key-event="keyup"
            :key-code="112"
            @success="handleFn112"
        />
        <form autocomplete="off" @submit.prevent="clickAddItem">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-12">
                        <h2>
                            <el-switch
                                v-model="search_item_by_barcode"
                                active-text="Buscar con escaner de código de barras"
                                @change="changeSearchItemBarcode"
                            ></el-switch>
                        </h2>
                    </div>

                    <div class="col-md-6">
                        <div :class="{'has-danger': errors.item_id}" class="form-group">
                            <label class="control-label">
                                Producto/Servicio
                                <a href="#" @click.prevent="showDialogNewItem = true">[+ Nuevo]</a>
                            </label>
                            <el-select v-show="!search_item_by_barcode"
                                       v-model="form.item_id"
                                       :remote-method="searchRemoteItems"
                                       filterable
                                       placeholder="Buscar"
                                       remote
                                       @change="changeItem"
                                       :loading="loading_search"
                            >
                                <el-option v-for="option in items" :key="option.id" :value="option.id" :label="option.full_description"></el-option>
                            </el-select>
                            <el-input v-show="search_item_by_barcode"
                                      placeholder="Buscar"
                                      @change="searchBarCode"
                                      v-model="form.barcode" :loading="loading_search"
                            ></el-input>
                            <small v-if="errors.item_id" class="form-control-feedback"
                                   v-text="errors.item_id[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.affectation_igv_type_id}">
                            <label class="control-label">Afectación Igv</label>
                            <el-select v-model="form.affectation_igv_type_id" :disabled="!change_affectation_igv_type_id" filterable>
                                <el-option v-for="option in affectation_igv_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                            </el-select>
                            <el-checkbox v-model="change_affectation_igv_type_id">Editar</el-checkbox>
                            <small class="form-control-feedback" v-if="errors.affectation_igv_type_id" v-text="errors.affectation_igv_type_id[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.quantity}">
                            <label class="control-label">Cantidad</label>
                            <el-input-number v-model="form.quantity" :min="0.01"></el-input-number>
                            <small class="form-control-feedback" v-if="errors.quantity" v-text="errors.quantity[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.unit_price}">
                            <label class="control-label">Precio Unitario</label>
                            <el-input v-model="form.unit_price" class="input-with-select"
                                      v-if="form.item.currency_type_id !== undefined"
                            >
                              <el-select v-model="form.item.currency_type_id" slot="prepend" class="el-select-currency">
                                <el-option label="S/" value="PEN"></el-option>
                                <el-option label="$" value="USD"></el-option>
                              </el-select>
                            </el-input>
                            <small class="form-control-feedback" v-if="errors.unit_price" v-text="errors.unit_price[0]"></small>
                        </div>
                        <el-checkbox v-model="form.update_purchase_price">Actualizar precio de compra</el-checkbox>
                        <el-checkbox v-model="form.update_price">Editar precio de venta</el-checkbox>
                    </div>
                    <div class="col-md-2" v-if="form.update_price">
                        <div class="form-group" :class="{'has-danger': errors.unit_price}">
                            <label class="control-label">Precio de venta</label>
                            <el-input v-model="sale_unit_price"></el-input>
                            <small class="form-control-feedback" v-if="errors.sale_unit_price" v-text="errors.sale_unit_price[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" :class="{'has-danger': errors.warehouse_id}">
                            <label class="control-label">Almacén de destino</label>
                            <el-select v-model="form.warehouse_id"   filterable  >
                                <el-option v-for="option in warehouses" :key="option.id" :value="option.id" :label="option.description"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.warehouse_id" v-text="errors.warehouse_id[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-6 mt-2" v-if="form.item_id">
                        <div class="form-group" :class="{'has-danger': errors.lot_code}" v-if="form.item.lots_enabled">
                            <label class="control-label">
                                Código lote
                            </label>
                            <el-input v-model="lot_code" >
                                <!--<el-button slot="append" icon="el-icon-edit-outline"  @click.prevent="clickLotcode"></el-button> -->
                            </el-input>
                            <small class="form-control-feedback" v-if="errors.lot_code" v-text="errors.lot_code[0]"></small>
                        </div>
                    </div>
                    <div style="padding-top: 1%;" class="col-md-3" v-show="form.item_id">
                        <div class="form-group" :class="{'has-danger': errors.date_of_due}" v-if="form.item.lots_enabled">
                            <label class="control-label">Fec. Vencimiento</label>
                            <el-date-picker v-model="form.date_of_due" type="date" value-format="yyyy-MM-dd" :clearable="true"></el-date-picker>
                            <small class="form-control-feedback" v-if="errors.date_of_due" v-text="errors.date_of_due[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-3" v-show="form.item_id">  <br>
                        <div class="form-group" :class="{'has-danger': errors.lot_code}" v-if="form.item.series_enabled">
                            <label class="control-label">
                                <!-- <el-checkbox v-model="enabled_lots"  @change="changeEnabledPercentageOfProfit">Código lote</el-checkbox> -->
                                Ingrese series
                            </label>

                            <el-button style="margin-top:2%;" type="primary" icon="el-icon-edit-outline"  @click.prevent="clickLotcode"> </el-button>

                            <small class="form-control-feedback" v-if="errors.lot_code" v-text="errors.lot_code[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-12"
                         v-if="form.item_unit_types !== undefined && form.item_unit_types.length > 0"
                    >
                        <div style="margin:3px" class="table-responsive">
                            <h5 class="separator-title">
                                Listado de Precios
                                <el-tooltip class="item" effect="dark" content="Aplica para realizar compra/venta en presentacion de diferentes precios y/o cantidades" placement="top">
                                    <i class="fa fa-info-circle"></i>
                                </el-tooltip>
                            </h5>
                            <table class="table">
                            <thead>
                            <tr>
                                <th class="text-center">Unidad</th>
                                <th class="text-center">Descripción</th>
                                <th class="text-center">Factor</th>

                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(row, index) in form.item_unit_types" :key="index">
                                    <td class="text-center">{{row.unit_type_id}}</td>
                                    <td class="text-center">{{row.description}}</td>
                                    <td class="text-center">{{row.quantity_unit}}</td>
                                    <td class="series-table-actions text-right">
                                       <button type="button" class="btn waves-effect waves-light btn-xs btn-success" @click.prevent="selectedPrice(row)">
                                            <i class="el-icon-check"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        </div>

                    </div>
                    <div class="col-md-12 mt-3">
                        <section class="card mb-2 card-transparent card-collapsed" id="card-section">
                                <header class="card-header hoverable bg-light border-top rounded-0 py-1" data-card-toggle style="cursor: pointer;" id="card-click">
                                    <div class="card-actions" style="margin-top: -12px;">
                                        <a href="#" class="card-action card-action-toggle text-info" data-card-toggle=""></a>
                                    </div>

                                    <p class="pl-1">Información adicional atributos UBL 2.1</p>
                                </header>
                                <div class="card-body px-0 pt-2" style="display: none;">
                                    <div class="col-md-12 px-0" v-if="discount_types.length > 0">
                                        <label class="control-label">
                                            Descuentos
                                            <a href="#" @click.prevent="clickAddDiscount">[+ Agregar]</a>
                                        </label>
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Descripción</th>
                                                <th>Porcentaje</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="(row, index) in form.discounts">
                                                <td>
                                                    <el-select v-model="row.discount_type_id" @change="changeDiscountType(index)">
                                                        <el-option v-for="option in discount_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                                    </el-select>
                                                </td>
                                                <td>
                                                    <el-input v-model="row.description"></el-input>
                                                </td>
                                                <td>
                                                    <el-input v-model="row.percentage"></el-input>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger" @click.prevent="clickRemoveDiscount(index)">x</button>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-12 px-0" v-if="charge_types.length > 0">
                                        <label class="control-label">
                                            Cargos
                                            <a href="#" @click.prevent="clickAddCharge">[+ Agregar]</a>
                                        </label>
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Descripción</th>
                                                <th>Porcentaje</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="(row, index) in form.charges">
                                                <td>
                                                    <el-select v-model="row.charge_type_id" @change="changeChargeType(index)">
                                                        <el-option v-for="option in charge_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                                    </el-select>
                                                </td>
                                                <td>
                                                    <el-input v-model="row.description"></el-input>
                                                </td>
                                                <td>
                                                    <el-input v-model="row.percentage"></el-input>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger" @click.prevent="clickRemoveCharge(index)">x</button>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-12 px-0" v-if="attribute_types.length > 0">
                                        <label class="control-label">
                                            Atributos
                                            <a href="#" @click.prevent="clickAddAttribute">[+ Agregar]</a>
                                        </label>
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Descripción</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="(row, index) in form.attributes">
                                                <td>
                                                    <el-select v-model="row.attribute_type_id" filterable @change="changeAttributeType(index)">
                                                        <el-option v-for="option in attribute_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                                    </el-select>
                                                </td>
                                                <td>
                                                    <el-input v-model="row.value"></el-input>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger" @click.prevent="clickRemoveAttribute(index)">x</button>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </section>
                    </div>
                </div>
            </div>
            <div class="form-actions text-right pt-2">
                <el-button @click.prevent="close()">Cerrar</el-button>
                <el-button type="primary" native-type="submit" v-if="form.item_id" >Agregar</el-button>
            </div>
        </form>
        <item-form :showDialog.sync="showDialogNewItem"
                   :external="true"></item-form>

        <lots-form
            :showDialog.sync="showDialogLots"
            :stock="form.quantity"
            :lots="lots"
            @addRowLot="addRowLot">
        </lots-form>

    </el-dialog>
</template>
<style>
.el-select-dropdown {
  max-width: 80% !important;
  margin-right: 5% !important;
}
.el-select-currency {
  width: 59px;
}
.input-with-select {
  background-color: #fff;
}
</style>
<script>

    import itemForm from '../../items/form.vue'
    import {calculateRowItem} from '../../../../helpers/functions'
    import LotsForm from '@components/incomeLots.vue'
    import Keypress from "vue-keypress";

    export default {
        props: ['showDialog', 'currencyTypeIdActive', 'exchangeRateSale'],
        components: {itemForm, LotsForm, Keypress},
        data() {
            return {
                search_item_by_barcode:false,
                sale_unit_price: 0,
                loading_search:false,
                titleDialog: 'Agregar Producto o Servicio',
                showDialogLots:false,
                resource: 'purchases',
                showDialogNewItem: false,
                errors: {},
                form: {},
                items: [],
                all_items: [],
                warehouses: [],
                lots: [],
                affectation_igv_types: [],
                system_isc_types: [],
                discount_types: [],
                charge_types: [],
                attribute_types: [],
                use_price: 1,
                lot_code: null,
                change_affectation_igv_type_id: false
            }
        },
        created() {
            this.initForm()
            this.$http.get(`/${this.resource}/item/tables`).then(response => {
                this.all_items = response.data.items
                this.affectation_igv_types = response.data.affectation_igv_types
                this.system_isc_types = response.data.system_isc_types
                this.discount_types = response.data.discount_types
                this.charge_types = response.data.charge_types
                this.attribute_types = response.data.attribute_types
                this.warehouses = response.data.warehouses
                this.initFilterItems()
            })

            this.$eventHub.$on('reloadDataItems', (item_id) => {
                this.reloadDataItems(item_id)
            })
        },
        methods: {
            handleFn112(response) {
                this.search_item_by_barcode = !this.search_item_by_barcode;
            },
            searchBarCode(input) {
                this.loading_search = true
                let parameters = `barcode=${input}`
                this.$http.get(`/${this.resource}/search-items/?${parameters}`)
                    .then(response => {
                        let items = response.data.items
                        this.items = items
                        this.loading_search = false
                        if (items === undefined || items.length == 0) {
                            this.initFilterItems()
                        } else if (this.items.length > 2) {
                            // varios items
                        } else {
                            if (items.length == 1) {
                                this.form.item_id = items[0].id
                                this.items = response.data.items
                                this.form.item = items[0]
                                this.form.item_id = items[0].id
                                this.changeItemAlt();
                            }
                        }
                    })
            },
            async searchRemoteItems(input) {

                if (input.length > 2) {

                    this.loading_search = true
                    let parameters = `input=${input}`

                    await this.$http.get(`/${this.resource}/search-items/?${parameters}`)
                        .then(response => {
                            this.items = response.data.items
                            this.loading_search = false

                            if (this.items.length == 0) {
                                this.initFilterItems()
                            }
                        })
                } else {
                    await this.initFilterItems()
                }

            },
            initFilterItems() {
                this.items = this.all_items
            },
            addRowLot(lots){
                this.lots = lots
            },
            clickLotcode(){
                this.showDialogLots = true
            },
            filterItems(){
                this.items = this.items.filter(item => item.warehouses.length >0)
                // this.items = this.items.filter(item => (item.warehouses!== undefined && item.warehouses.length >0))
            },
            initForm() {
                this.errors = {}
                this.form = {
                    item_id: null,
                    barcode: null,
                    warehouse_id: 1,
                    warehouse_description: null,
                    item: {},
                    affectation_igv_type_id: null,
                    affectation_igv_type: {},
                    has_isc: false,
                    system_isc_type_id: null,
                    percentage_isc: 0,
                    suggested_price: 0,
                    quantity: 1,
                    unit_price: 0,
                    charges: [],
                    discounts: [],
                    attributes: [],
                    item_unit_types: [],
                    lot_code:null,
                    date_of_due: null,
                    purchase_has_igv: null,
                    update_price: false,
                    update_purchase_price: true,
                }

                this.item_unit_type = {};
                this.lots = []
                this.lot_code = null
            },
            clickAddDiscount() {
                this.form.discounts.push({
                    discount_type_id: null,
                    discount_type: null,
                    description: null,
                    percentage: 0,
                    factor: 0,
                    amount: 0,
                    base: 0
                })
            },
            clickRemoveDiscount(index) {
                this.form.discounts.splice(index, 1)
            },
            changeDiscountType(index) {
                let discount_type_id = this.form.discounts[index].discount_type_id
                this.form.discounts[index].discount_type = _.find(this.discount_types, {id: discount_type_id})
            },
            clickAddCharge() {
                this.form.charges.push({
                    charge_type_id: null,
                    charge_type: null,
                    description: null,
                    percentage: 0,
                    factor: 0,
                    amount: 0,
                    base: 0
                })
            },
            clickRemoveCharge(index) {
                this.form.charges.splice(index, 1)
            },
            changeChargeType(index) {
                let charge_type_id = this.form.charges[index].charge_type_id
                this.form.charges[index].charge_type = _.find(this.charge_types, {id: charge_type_id})
            },
            clickAddAttribute() {
                this.form.attributes.push({
                    attribute_type_id: null,
                    description: null,
                    value: null,
                    start_date: null,
                    end_date: null,
                    duration: null,
                })
            },
            clickRemoveAttribute(index) {
                this.form.attributes.splice(index, 1)
            },
            changeAttributeType(index) {
                let attribute_type_id = this.form.attributes[index].attribute_type_id
                let attribute_type = _.find(this.attribute_types, {id: attribute_type_id})
                this.form.attributes[index].description = attribute_type.description
            },
            close() {
                this.initForm()
                this.$emit('update:showDialog', false)
            },
            selectedPrice(row) {
                this.form.item_unit_type_id = row.id
                this.item_unit_type = row
                this.form.item.unit_type_id = row.unit_type_id
            },
            changeItem() {
                const item = {..._.find(this.items, {'id': this.form.item_id})};
                this.form.item = item;
                const saleUnitPrice = item.sale_unit_price;
                this.sale_unit_price = parseFloat(saleUnitPrice).toFixed(2);
                this.form.unit_price = this.form.item.purchase_unit_price
                this.form.affectation_igv_type_id = this.form.item.purchase_affectation_igv_type_id
                this.form.item_unit_types = _.find(this.items, {'id': this.form.item_id}).item_unit_types
                this.form.purchase_has_igv = this.form.item.purchase_has_igv;

            },

            changeItemAlt() {
                let item = _.find(this.items, {'id': this.form.item_id});
                this.form.item = item;
                let saleUnitPrice = item.sale_unit_price;
                this.sale_unit_price = parseFloat(saleUnitPrice).toFixed(2);
                this.form.unit_price = this.form.item.purchase_unit_price
                this.form.affectation_igv_type_id = this.form.item.purchase_affectation_igv_type_id
                this.form.item_unit_types = item.item_unit_types
                this.form.purchase_has_igv = this.form.item.purchase_has_igv;
                this.search_item_by_barcode = 0;
            },
            async clickAddItem() {
                if(this.form.item.lots_enabled){
                    if(!this.lot_code)
                        return this.$message.error('Código de lote es requerido');

                    if(!this.form.date_of_due)
                        return this.$message.error('Fecha de vencimiento es requerido si lotes esta habilitado.');
                }

                if(this.form.item.series_enabled) {
                    if(this.lots.length > this.form.quantity)
                        return this.$message.error('La cantidad de series registradas es superior al stock');

                    if(this.lots.length != this.form.quantity)
                        return this.$message.error('La cantidad de series registradas son diferentes al stock');
                }

                let affectation_igv_types_exonerated_unaffected = ['20','21','30','31','32','33','34','35','36','37']

                let unit_price = this.form.unit_price

                if(!affectation_igv_types_exonerated_unaffected.includes(this.form.affectation_igv_type_id)) {

                    unit_price = (this.form.purchase_has_igv)?this.form.unit_price:this.form.unit_price*1.18;

                }

                let date_of_due = this.form.date_of_due

                this.form.item.unit_price = unit_price
                this.form.item.presentation = this.item_unit_type;
                this.form.affectation_igv_type = _.find(this.affectation_igv_types, {'id': this.form.affectation_igv_type_id})
                this.row = await calculateRowItem(this.form, this.currencyTypeIdActive, this.exchangeRateSale)
                this.row.lot_code = await this.lot_code
                this.row.lots = await this.lots
                this.row.update_price = this.form.update_price
                this.row.update_purchase_price = this.form.update_purchase_price
                this.row.sale_unit_price = this.sale_unit_price

                this.row = this.changeWarehouse(this.row)

                this.row.date_of_due = date_of_due

                this.initForm()

                this.$emit('add', this.row)
            },
            changeWarehouse(row){
                let warehouse = _.find(this.warehouses,{'id':this.form.warehouse_id})
                row.warehouse_id = warehouse.id
                row.warehouse_description = warehouse.description
                return row
            },
            reloadDataItems(item_id) {

                if(!item_id){

                    this.$http.get(`/${this.resource}/table/items`).then((response) => {
                        this.items = response.data
                        this.form.item_id = item_id
                    })

                }else{

                    this.$http.get(`/${this.resource}/search/item/${item_id}`).then((response) => {

                        this.items = response.data.items
                        this.form.item_id = item_id
                        this.changeItem()

                    })
                }

            },
            enabledSearchItemsBarcode() {
                if (this.search_item_by_barcode) {
                    if (this.items.length == 1) {
                        this.clickAddItem(this.items[0], 0);
                        this.filterItems();
                    }

                    this.cleanInput();
                }
            },
            changeSearchItemBarcode() {
                this.cleanInput();
                if(!this.search_item_by_barcode){
                    this.initFilterItems()
                }
            },
            cleanInput() {
                this.input_item = null;
            },
        }
    }

</script>

<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @open="create" @close="close">
        <form autocomplete="off" @submit.prevent="clickAddItem">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group" id="custom-select" :class="{'has-danger': errors.item_id}">
                            <label class="control-label">
                                Producto/Servicio
                                <a href="#" v-if="typeUser != 'seller'" @click.prevent="showDialogNewItem = true">[+ Nuevo]</a>
                            </label>

                            <el-input id="custom-input">
                                <el-select v-model="form.item_id" @change="changeItem" 
                                    filterable
                                    placeholder="Buscar"
                                    remote
                                    :remote-method="searchRemoteItems"
                                    :loading="loading_search"
                                    popper-class="el-select-items"
                                    slot="prepend"
                                    id="select-width"
                                >
                                    <el-tooltip v-for="option in items"  :key="option.id" placement="top">
                                        <div slot="content">
                                            Marca: {{option.brand}} <br>
                                            Categoria: {{option.category}} <br>
                                            Stock: {{option.stock}} <br>
                                            Precio: {{option.currency_type_symbol}} {{option.sale_unit_price}} <br>
                                            Cod. Lote: {{option.lot_code}} <br>
                                            Fec. Venc: {{option.date_of_due}} <br>
                                        </div>
                                        <el-option :value="option.id" :label="option.full_description"></el-option>
                                    </el-tooltip>
                                </el-select>
                                <el-tooltip slot="append" class="item" effect="dark" content="Ver Stock del Producto" placement="bottom" >
                                    <el-button   @click.prevent="clickWarehouseDetail()"><i class="fa fa-search"></i></el-button>
                                </el-tooltip>
                            </el-input>

                            <small class="form-control-feedback" v-if="errors.item_id" v-text="errors.item_id[0]"></small>
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
                            <el-input v-model="form.unit_price">
                                <template slot="prepend" v-if="form.item.currency_type_symbol">{{ form.item.currency_type_symbol }}</template>
                            </el-input>
                            <small class="form-control-feedback" v-if="errors.unit_price" v-text="errors.unit_price[0]"></small>
                        </div>
                    </div>
                    <div style="padding-top: 1%;" class="col-md-2 col-sm-2" v-if="form.item_id && form.item.lots_enabled && form.lots_group.length > 0">
                        <a href="#"  class="text-center font-weight-bold text-info" @click.prevent="clickLotGroup">[&#10004; Seleccionar lote]</a>
                    </div>

                    <div style="padding-top: 1%;" class="col-md-3 col-sm-3" v-if="form.item_id && form.item.series_enabled">
                        <!-- <el-button type="primary" native-type="submit" icon="el-icon-check">Elegir serie</el-button> -->
                        <a href="#"  class="text-center font-weight-bold text-info" @click.prevent="clickSelectLots">[&#10004; Seleccionar series]</a>
                    </div>

                    <!--<div class="col-md-6 mt-4" v-if="form.item_id && form.item.series_enabled">
                        <a href="#"  class="text-center font-weight-bold text-info" @click.prevent="clickSelectLots">[&#10004; Seleccionar series]</a>
                    </div> -->

                    <div class="col-md-12"  v-if="form.item_unit_types.length > 0">
                        <div style="margin:3px" class="table-responsive">
                            <h3>Lista de Precios</h3>
                            <table class="table">
                            <thead>
                            <tr>
                                <th class="text-center">Unidad</th>
                                <th class="text-center">Descripción</th>
                                <th class="text-center">Factor</th>
                                <th class="text-center">Precio 1</th>
                                <th class="text-center">Precio 2</th>
                                <th class="text-center">Precio 3</th>
                                <th class="text-center">Precio Default</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(row, index) in form.item_unit_types">

                                    <td class="text-center">{{row.unit_type_id}}</td>
                                    <td class="text-center">{{row.description}}</td>
                                    <td class="text-center">{{row.quantity_unit}}</td>
                                    <td class="text-center">{{row.price1}}</td>
                                    <td class="text-center">{{row.price2}}</td>
                                    <td class="text-center">{{row.price3}}</td>
                                    <td class="text-center">Precio {{row.price_default}}</td>
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
                <el-button type="primary" native-type="submit">Agregar</el-button>
            </div>
        </form>
        <item-form  :showDialog.sync="showDialogNewItem"
                   :external="true"></item-form>


        <select-lots-form
            :showDialog.sync="showDialogSelectLots"
            :lots="lots"
            :itemId="form.item_id"
            @addRowSelectLot="addRowSelectLot">
        </select-lots-form>

         <lots-group
            :quantity="form.quantity"
            :showDialog.sync="showDialogLots"
            :lots_group="form.lots_group"
            @addRowLotGroup="addRowLotGroup">
        </lots-group>

        <warehouses-detail
                :showDialog.sync="showWarehousesDetail"
                :warehouses="warehousesDetail">
            </warehouses-detail>

    </el-dialog>
</template>
<style>
.el-select-dropdown {
    max-width: 80% !important;
    margin-right: 5% !important;
}
</style>
<script>

    import itemForm from '../../items/form.vue'
    import {calculateRowItem} from '../../../../helpers/functions'
    import SelectLotsForm from '../../documents/partials/lots.vue'
    import LotsGroup from './lots_group.vue'
    import WarehousesDetail from '../../documents/partials/select_warehouses.vue'


    export default {
        props: ['showDialog', 'currencyTypeIdActive', 'exchangeRateSale', 'typeUser'],
        components: {itemForm, SelectLotsForm, LotsGroup, WarehousesDetail},
        data() {
            return {
                showWarehousesDetail: false,
                warehousesDetail:[],
                loading_search:false,
                titleDialog: 'Agregar Producto o Servicio',
                resource: 'sale-notes',
                showDialogNewItem: false,
                showDialogSelectLots: false,
                errors: {},
                form: {},
                all_items: [],
                items: [],
                affectation_igv_types: [],
                system_isc_types: [],
                discount_types: [],
                charge_types: [],
                attribute_types: [],
                use_price: 1,
                change_affectation_igv_type_id: false,
                lots:[],
                showDialogLots: false
            }
        },
        created() {
            this.initForm()
            this.getTables()
            this.$eventHub.$on('reloadDataItems', (item_id) => {
                this.reloadDataItems(item_id)
            })
            this.events()
        },
        methods: {
            events(){
                
                this.$eventHub.$on('selectWarehouseId', (warehouse_id) => {
                    // console.log(warehouse_id)
                    this.form.warehouse_id = warehouse_id
                })
            },
            clickWarehouseDetail(){

                if(!this.form.item_id){
                    return this.$message.error('Seleccione un item');
                }

                let item = _.find(this.items, {'id': this.form.item_id});

                this.warehousesDetail = item.warehouses
                this.showWarehousesDetail = true
            },
            addRowSelectLot(lots){
                this.lots = lots
            },
            async clickSelectLots(){
                this.showDialogSelectLots = true
            },
            getTables(){

                this.$http.get(`/${this.resource}/item/tables`).then(response => {
                    this.all_items = response.data.items
                    // this.items = response.data.items
                    this.affectation_igv_types = response.data.affectation_igv_types
                    this.system_isc_types = response.data.system_isc_types
                    this.discount_types = response.data.discount_types
                    this.charge_types = response.data.charge_types
                    this.attribute_types = response.data.attribute_types
                    // this.filterItems()
                    this.filterItems()

                })
            },
            selectedPrice(row)
            {
                // debugger
                let valor = 0
                switch(row.price_default)
                {
                    case 1:
                        valor = row.price1
                        break
                    case 2:
                         valor = row.price2
                        break
                    case 3:
                         valor = row.price3
                        break

                }

                this.item_unit_type = row
                this.form.unit_price = valor
                this.form.item.unit_type_id = row.unit_type_id
                this.form.item_unit_type_id = row.id
                this.getTables()

            },
            async searchRemoteItems(input) {

                if (input.length > 2) {

                    this.loading_search = true
                    let parameters = `input=${input}`

                    await this.$http.get(`/${this.resource}/search-items/?${parameters}`)
                            .then(response => {
                                // console.log(response)
                                this.items = response.data.items
                                this.loading_search = false

                                if(this.items.length == 0){
                                    this.filterItems()
                                }
                            })
                } else {
                    await this.filterItems()
                }

            },
            filterItems() {
                this.items = this.all_items
            },
            initForm() {
                this.errors = {}
                this.form = {
                    item_id: null,
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
                    has_igv: null,
                    is_set: false,
                    item_unit_types: [],
                    series_enabled: false,
                    IdLoteSelected: null,
                    warehouse_id:null,
                    lots_group: []
                }
                this.item_unit_type = {};
                this.lots = []
            },
            // initializeFields() {
            //     this.form.affectation_igv_type_id = this.affectation_igv_types[0].id
            // },
            create() {
            //     this.initializeFields()
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
            async changeItem() {

                this.form.item = await _.find(this.items, {'id': this.form.item_id})
                this.lots = this.form.item.lots
                // console.log(this.form.item.lots)

                this.form.item_unit_types = _.find(this.items, {'id': this.form.item_id}).item_unit_types
                this.form.unit_price = this.form.item.sale_unit_price

                this.form.has_igv = this.form.item.has_igv;
                this.form.affectation_igv_type_id = this.form.item.sale_affectation_igv_type_id
                this.form.quantity = 1;
                this.form.lots_group = this.form.item.lots_group

            },
            async clickAddItem() {


                if(this.form.item.lots_enabled){
                    if(!this.form.IdLoteSelected)
                        return this.$message.error('Debe seleccionar un lote.');
                }
                
                let affectation_igv_types_exonerated_unaffected = ['20','21','30','31','32','33','34','35','36','37']

                let unit_price = this.form.unit_price

                if(!affectation_igv_types_exonerated_unaffected.includes(this.form.affectation_igv_type_id)) {

                    unit_price = (this.form.has_igv)?this.form.unit_price:this.form.unit_price*1.18;

                }


                // this.form.item.unit_price = this.form.unit_price
                this.form.unit_price = unit_price;
                this.form.item.unit_price = unit_price;

                this.form.affectation_igv_type = _.find(this.affectation_igv_types, {'id': this.form.affectation_igv_type_id})
                this.form.item.presentation = this.item_unit_type;

                let IdLoteSelected = this.form.IdLoteSelected


                this.row = await calculateRowItem(this.form, this.currencyTypeIdActive, this.exchangeRateSale)

                let select_lots = await _.filter(this.row.item.lots, {'has_sale':true})
                let un_select_lots = await _.filter(this.row.item.lots, {'has_sale':false})

                if(this.form.item.series_enabled){
                    if(select_lots.length != this.form.quantity)
                        return this.$message.error('La cantidad de series seleccionadas son diferentes a la cantidad a vender');
                }

                this.row.item.lots = un_select_lots
                this.row.lots = select_lots

                this.row.IdLoteSelected = IdLoteSelected

                // console.log(un_select_lots)
                // console.log(this.row.lots)

                this.initForm()
                // this.initializeFields()
                this.$emit('add', this.row)
            },
            reloadDataItems(item_id) {

                if(!item_id){

                    this.$http.get(`/${this.resource}/table/items`).then((response) => {
                        this.items = response.data
                        this.form.item_id = item_id
                        // if(item_id) this.changeItem()
                        // this.filterItems()
                    })

                }else{

                    this.$http.get(`/${this.resource}/search/item/${item_id}`).then((response) => {

                        this.items = response.data.items
                        this.form.item_id = item_id
                        this.changeItem()

                    })
                }

            },
            addRowLotGroup(id)
            {
                this.form.IdLoteSelected =  id
            },
             clickLotGroup()
            {
                this.showDialogLots = true
            },
        }
    }

</script>

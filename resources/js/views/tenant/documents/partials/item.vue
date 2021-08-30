<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @open="create" @close="close" top="7vh" :close-on-click-modal="false">
        <form autocomplete="off" @submit.prevent="clickAddItem">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-7 col-lg-7 col-xl-7 col-sm-7">
                        <div class="form-group" id="custom-select" :class="{'has-danger': errors.item_id}">
                            <label class="control-label">
                                Producto/Servicio
                                <a v-if="can_add_new_product"
                                   href="#" @click.prevent="showDialogNewItem = true">
                                    [+ Nuevo]
                                </a>
                            </label>

                            <template v-if="!search_item_by_barcode" id="select-append">
                                <el-input id="custom-input">
                                    <el-select :disabled="recordItem != null"
                                            v-model="form.item_id"
                                            @change="changeItem"
                                            filterable
                                            remote
                                            placeholder="Buscar"
                                            popper-class="el-select-items"
                                            @visible-change="focusTotalItem"
                                            slot="prepend"
                                            id="select-width"
                                            :remote-method="searchRemoteItems"
                                            :loading="loading_search">

                                        <el-tooltip v-for="option in items"  :key="option.id" placement="top">

                                            <div slot="content">
                                                Almacen: {{option.warehouse_description}} <br>
                                                Marca: {{option.brand}} <br>
                                                Categoria: {{option.category}} <br>
                                                Stock: {{option.stock}} <br>
                                                Precio: {{option.currency_type_symbol}} {{option.sale_unit_price}} <br>
                                            </div>

                                            <el-option  :value="option.id" :label="option.full_description"></el-option>

                                        </el-tooltip>

                                    </el-select>
                                    <el-tooltip slot="append" class="item" effect="dark" content="Ver Stock del Producto" placement="bottom" :disabled="recordItem != null">
                                        <el-button :disabled="isEditItemNote"  @click.prevent="clickWarehouseDetail()"><i class="fa fa-search"></i></el-button>
                                    </el-tooltip>
                                </el-input>
                            </template>
                            <template v-else>
                                <el-input id="custom-input">
                                    <el-select :disabled="recordItem != null" v-model="form.item_id"
                                        ref="selectBarcode"
                                        @change="changeItem"
                                        filterable
                                        placeholder="Buscar"
                                        popper-class="el-select-items"
                                        slot="prepend"
                                        id="select-width"
                                        value-key="id"
                                        remote
                                        :remote-method="searchRemoteItems"
                                        :loading="loading_search"
                                    >
                                        <el-option v-for="option in items"  :key="option.id" :value="option.id" :label="option.full_description"></el-option>
                                    </el-select>
                                    <el-tooltip slot="append" class="item" effect="dark" content="Ver Stock del Producto" placement="bottom" :disabled="recordItem != null">
                                        <el-button :disabled="isEditItemNote"  @click.prevent="clickWarehouseDetail()"><i class="fa fa-search"></i></el-button>
                                    </el-tooltip>
                                </el-input>
                            </template>

                            <template v-if="!is_client">
                                <el-checkbox  v-model="search_item_by_barcode" :disabled="recordItem != null" >Buscar por código de barras</el-checkbox><br>
                            </template>
                            <el-checkbox v-model="form.has_plastic_bag_taxes" :disabled="isEditItemNote" >Impuesto a la Bolsa Plástica</el-checkbox>
                            <small class="form-control-feedback" v-if="errors.item_id" v-text="errors.item_id[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group" :class="{'has-danger': errors.affectation_igv_type_id}">
                            <label class="control-label">Afectación Igv</label>
                            <el-select v-model="form.affectation_igv_type_id" :disabled="!change_affectation_igv_type_id" filterable>
                                <el-option v-for="option in affectation_igv_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                            </el-select>
                            <el-checkbox :disabled="recordItem != null" v-model="change_affectation_igv_type_id">Editar</el-checkbox>
                            <small class="form-control-feedback" v-if="errors.affectation_igv_type_id" v-text="errors.affectation_igv_type_id[0]"></small>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group" :class="{'has-danger': errors.quantity}">

                            <label class="control-label">Cantidad</label>
                            <el-input v-model="form.quantity" :disabled="form.item.calculate_quantity" @blur="validateQuantity" @input.native="changeValidateQuantity" ref="inputQuantity">
                                <el-button style="padding-right: 5px ;padding-left: 12px" slot="prepend" icon="el-icon-minus" @click="clickDecrease" :disabled="form.quantity < 0.01 || form.item.calculate_quantity"></el-button>
                                <el-button style="padding-right: 5px ;padding-left: 12px" slot="append" icon="el-icon-plus" @click="clickIncrease"  :disabled="form.item.calculate_quantity"></el-button>
                            </el-input>
                            <small class="form-control-feedback" v-if="errors.quantity" v-text="errors.quantity[0]"></small>

                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group" :class="{'has-danger': errors.unit_price_value}">
                            <label class="control-label">Precio Unitario</label>
                            <el-input v-model="form.unit_price_value" @input="calculateQuantity" :readonly="!edit_unit_price">
                                <template slot="prepend" v-if="form.item.currency_type_symbol">{{ form.item.currency_type_symbol }}</template>
                            </el-input>
                            <small class="form-control-feedback" v-if="errors.unit_price_value" v-text="errors.unit_price[0]"></small>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <label class="control-label">Total</label>
                            <el-input v-model="readonly_total" @input="calculateTotal" readonly></el-input>
                        </div>
                    </div>

                    <div style="padding-top: 1%;" class="col-md-2 col-sm-2" v-if="form.item_id && form.item.lots_enabled && form.lots_group.length > 0">
                        <a href="#"  class="text-center font-weight-bold text-info" @click.prevent="clickLotGroup">[&#10004; Seleccionar lote]</a>
                    </div>

                    <div style="padding-top: 1%;" class="col-md-3 col-sm-3" v-if="form.item_id && form.item.series_enabled">
                        <a href="#"  class="text-center font-weight-bold text-info" @click.prevent="clickSelectLots">[&#10004; Seleccionar series]</a>
                    </div>


                    <div class="col-md-3 col-sm-6" v-show="form.item.calculate_quantity">
                        <div class="form-group"  :class="{'has-danger': errors.total_item}">
                            <label class="control-label">Total venta producto</label>
                            <el-input v-model="total_item" @input="calculateQuantity" :min="0.01" ref="total_item">
                                <template slot="prepend" v-if="form.item.currency_type_symbol">{{ form.item.currency_type_symbol }}</template>
                            </el-input>
                            <small class="form-control-feedback" v-if="errors.total_item" v-text="errors.total_item[0]"></small>
                        </div>
                    </div>
                    <div v-if="configuration.edit_name_product" class="col-md-12 col-sm-12 mt-2">
                        <div class="form-group">
                            <label class="control-label">Nombre producto en PDF</label>
                            <vue-ckeditor type="classic" v-model="form.name_product_pdf" :editors="editors"></vue-ckeditor>
                        </div>
                    </div>
                    <template v-if="!is_client">

                        <div class="col-md-12"  v-if="form.item_unit_types.length > 0">
                            <div style="margin:3px" class="table-responsive">
                                <h5 class="separator-title">
                                    Lista de Precios
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
                                    <th class="text-center">Precio 1</th>
                                    <th class="text-center">Precio 2</th>
                                    <th class="text-center">Precio 3</th>
                                    <th class="text-center">Precio Default</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(row, index) in form.item_unit_types" :key="index">
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

                        <div class="col-md-12 mt-2">
                            <el-collapse  v-model="activePanel">
                                <el-collapse-item :disabled="recordItem != null" title="+ Agregar Descuentos/Cargos/Atributos especiales" name="1">
                                    <div v-if="discount_types.length > 0">
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
                                                <tr v-for="(row, index) in form.discounts" :key="index">
                                                    <td>
                                                        <el-select v-model="row.discount_type_id" @change="changeDiscountType(index)">
                                                            <el-option v-for="option in discount_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                                        </el-select>
                                                    </td>
                                                    <td>
                                                        <el-input v-model="row.description"></el-input>
                                                    </td>
                                                    <td>
                                                        <el-checkbox v-model="row.is_amount">Ingresar monto fijo</el-checkbox><br>
                                                        <el-input v-model="row.percentage"></el-input>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger" @click.prevent="clickRemoveDiscount(index)">x</button>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div v-if="charge_types.length > 0">
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
                                                <tr v-for="(row, index) in form.charges" :key="index">
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
                                        <div v-if="attribute_types.length > 0">
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
                                                <tr v-for="(row, index) in form.attributes" :key="index">
                                                    <td>
                                                        <el-select v-model="row.attribute_type_id" filterable @change="changeAttributeType(index)">
                                                            <el-option v-for="option in attribute_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                                        </el-select>
                                                    </td>
                                                    <td>
                                                        <el-input v-model="row.value" @input="inputAttribute(index)"></el-input>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger" @click.prevent="clickRemoveAttribute(index)">x</button>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                </el-collapse-item>
                            </el-collapse>
                        </div>
                    </template>
                </div>
            </div>
            <div class="form-actions text-right pt-2">
                <el-button @click.prevent="close()">Cerrar</el-button>
                <el-button class="add" type="primary" native-type="submit" v-if="form.item_id">{{titleAction}}</el-button>
            </div>
        </form>
        <item-form :showDialog.sync="showDialogNewItem"
                   :external="true"></item-form>


        <warehouses-detail
                :showDialog.sync="showWarehousesDetail"
                :isUpdateWarehouseId="isUpdateWarehouseId"
                :warehouses="warehousesDetail">
            </warehouses-detail>

        <lots-group
            :quantity="form.quantity"
            :showDialog.sync="showDialogLots"
            :lots_group="form.lots_group"
            @addRowLotGroup="addRowLotGroup">
        </lots-group>

        <select-lots-form
            :showDialog.sync="showDialogSelectLots"
            :lots="lots"
            :itemId="form.item_id"
            :documentItemId="form.document_item_id"
            @addRowSelectLot="addRowSelectLot">
        </select-lots-form>


    </el-dialog>
</template>
<style>
.el-select-dropdown {
    max-width: 80% !important;
    margin-right: 5% !important;
}
</style>

<script>

    import ItemForm from '../../items/form.vue'
    import LotsGroup from './lots_group.vue'

    import {calculateRowItem} from '../../../../helpers/functions'
    import WarehousesDetail from './select_warehouses.vue'
    import SelectLotsForm from './lots.vue'

    import ClassicEditor from '@ckeditor/ckeditor5-build-classic'
    import VueCkeditor from 'vue-ckeditor5'

    export default {
        props: [
            'recordItem',
            'showDialog',
            'operationTypeId',
            'currencyTypeIdActive',
            'exchangeRateSale',
            'typeUser',
            'isEditItemNote',
            'configuration',
            'documentTypeId',
            'noteCreditOrDebitTypeId'
        ],
        components: {ItemForm, WarehousesDetail, LotsGroup, SelectLotsForm, 'vue-ckeditor': VueCkeditor.component},
        data() {
            return {
                can_add_new_product: false,
                loading_search:false,
                titleAction: '',
                is_client:false,
                titleDialog: '',
                resource: 'documents',
                showDialogNewItem: false,
                has_list_prices: false,
                errors: {},
                form: {},
                all_items: [],
                items: [],
                operation_types: [],
                all_affectation_igv_types: [],
                affectation_igv_types: [],
                system_isc_types: [],
                discount_types: [],
                charge_types: [],
                attribute_types: [],
                use_price: 1,
                change_affectation_igv_type_id: false,
                activePanel: 0,
                total_item: 0,
                item_unit_types: [],
                showWarehousesDetail: false,
                warehousesDetail:[],
                showListStock:false,
                search_item_by_barcode:false,
                isUpdateWarehouseId:null,
                showDialogLots: false,
                showDialogSelectLots: false,
                lots:[],
                editors: {
                  classic: ClassicEditor
                },
                value1: 'hello',
                readonly_total: 0
                //item_unit_type: {}
            }
        },
        created() {
            this.initForm()
            this.$http.get(`/${this.resource}/item/tables`).then(response => {
                this.all_items = response.data.items
                this.operation_types = response.data.operation_types
                this.all_affectation_igv_types = response.data.affectation_igv_types
                this.system_isc_types = response.data.system_isc_types
                this.discount_types = response.data.discount_types
                this.charge_types = response.data.charge_types
                this.attribute_types = response.data.attribute_types
                this.is_client = response.data.is_client;
                this.filterItems()

            })

            this.$eventHub.$on('reloadDataItems', (item_id) => {
                this.reloadDataItems(item_id)
            })

            this.$eventHub.$on('selectWarehouseId', (warehouse_id) => {
                this.form.warehouse_id = warehouse_id
            })
            this.canCreateProduct();
        },
        computed: {
            edit_unit_price() {
                if(this.typeUser === 'admin') {
                    return true
                }
                if(this.typeUser === 'seller') {
                    return this.configuration.allow_edit_unit_price_to_seller;
                }
                return false;
            }
        },
        methods: {
            canCreateProduct(){
                if(this.typeUser === 'admin') {
                    this.can_add_new_product =  true
                }else if(this.typeUser === 'seller') {
                    if (this.configuration !== undefined && this.configuration.seller_can_create_product !== undefined) {
                        this.can_add_new_product = this.configuration.seller_can_create_product;
                    }
                }
                return this.can_add_new_product;
            },
            validateQuantity(){

                if(!this.form.quantity){
                    this.setMinQuantity()
                }

                if (isNaN(Number(this.form.quantity))) {
                    this.setMinQuantity()
                }

                if (typeof parseFloat(this.form.quantity) !== 'number'){
                    this.setMinQuantity()
                }

                if(this.form.quantity <= this.getMinQuantity()){
                    this.setMinQuantity()
                }

                this.calculateTotal()
            },
            changeValidateQuantity(event) {
                this.calculateTotal()
            },
            getMinQuantity(){
                return 0.01
            },
            setMinQuantity(){
                this.form.quantity = this.getMinQuantity()
            },
            clickDecrease(){

                this.form.quantity = parseInt(this.form.quantity-1)

                if(this.form.quantity <= this.getMinQuantity()){
                    this.setMinQuantity()
                    return
                }

                this.calculateTotal()

            },
            clickIncrease(){
                this.form.quantity = parseInt(this.form.quantity + 1)
                this.calculateTotal()
            },
            async searchRemoteItems(input) {
                if (input.length > 2) {
                    this.loading_search = true
                    const params = {
                        'input': input,
                        'search_by_barcode': this.search_item_by_barcode ? 1 : 0
                    }
                    await this.$http.get(`/${this.resource}/search-items/`, { params })
                            .then(response => {
                                this.items = response.data.items
                                this.loading_search = false
                                this.enabledSearchItemsBarcode()
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
            enabledSearchItemsBarcode(){
                if(this.search_item_by_barcode) {
                    this.$refs.selectBarcode.$data.selectedLabel = '';
                    if (this.items.length == 1) {
                        this.form.item_id = this.items[0].id;
                        this.$refs.selectBarcode.blur();
                        this.changeItem();
                    }
                }
            },
            filterMethod(query){

                let item = _.find(this.items, {'internal_id': query});

                if(item){
                    this.form.item_id = item.id
                    this.changeItem()
                }
            },
            clickWarehouseDetail(){

                if(!this.form.item_id){
                    return this.$message.error('Seleccione un item');
                }

                let item = _.find(this.items, {'id': this.form.item_id});

                this.warehousesDetail = item.warehouses
                this.showWarehousesDetail = true
            },
            // filterItems(){
            //     this.items = this.items.filter(item => item.warehouses.length >0)
            // },
            initForm() {
                this.errors = {};

                this.form = {
                   // category_id: [1],
                   // edit: false,
                    item_id: null,
                    item: {},
                    affectation_igv_type_id: null,
                    affectation_igv_type: {},
                    has_isc: false,
                    system_isc_type_id: null,
                    percentage_isc: 0,
                    suggested_price: 0,
                    quantity: 1,
                    unit_price_value: 0,
                    input_unit_price_value: 0,
                    unit_price: 0,
                    charges: [],
                    discounts: [],
                    attributes: [],
                    has_igv: null,
                    item_unit_types: [],
                    has_plastic_bag_taxes:false,
                    warehouse_id:null,
                    lots_group: [],
                    IdLoteSelected: null,
                    document_item_id: null,
                    name_product_pdf: ''
                };

                this.activePanel = 0;
                this.total_item = 0;
                this.item_unit_type = {};
                this.has_list_prices = false;
            },
            // initializeFields() {
            //     this.form.affectation_igv_type_id = this.affectation_igv_types[0].id
            // },
            async create() {

                this.titleDialog = (this.recordItem) ? ' Editar Producto o Servicio' : ' Agregar Producto o Servicio';
                this.titleAction = (this.recordItem) ? ' Editar' : ' Agregar';
                let operation_type = await _.find(this.operation_types, {id: this.operationTypeId})
                this.affectation_igv_types = await _.filter(this.all_affectation_igv_types, {exportation: operation_type.exportation})


                if (this.recordItem) {
                    await this.reloadDataItems(this.recordItem.item_id)
                    this.form.item_id = await this.recordItem.item_id
                    await this.changeItem()
                    this.form.quantity = this.recordItem.quantity
                    this.form.unit_price_value = this.recordItem.input_unit_price_value
                    this.form.has_plastic_bag_taxes = (this.recordItem.total_plastic_bag_taxes > 0) ? true : false
                    this.form.warehouse_id = this.recordItem.warehouse_id
                    this.isUpdateWarehouseId = this.recordItem.warehouse_id

                    if(this.isEditItemNote){
                        this.form.item.currency_type_id = this.currencyTypeIdActive
                        this.form.item.currency_type_symbol = (this.currencyTypeIdActive == 'PEN') ? 'S/':'$'

                        if(this.documentTypeId == '07' && this.noteCreditOrDebitTypeId == '07'){

                            this.form.document_item_id =  this.recordItem.id ? this.recordItem.id : this.recordItem.document_item_id
                            this.form.item.lots = this.recordItem.item.lots
                            await this.regularizeLots()
                            this.lots = this.form.item.lots
                        }

                    }

                    if(this.recordItem.item.name_product_pdf){
                        this.form.name_product_pdf = this.recordItem.item.name_product_pdf
                    }
                    // if(this.recordItem.name_product_pdf){
                    //     this.form.name_product_pdf = this.recordItem.name_product_pdf
                    // }


                    this.calculateQuantity()
                }else{
                    this.isUpdateWarehouseId = null
                }

            },
            async regularizeLots(){

                if(this.form.document_item_id && this.form.item.lots.length > 0){

                    await this.$http.get(`/${this.resource}/regularize-lots/${this.form.document_item_id}`).then((response) => {

                                        let all_lots = this.form.item.lots
                                        let available_lots = response.data

                                        all_lots.forEach((lot, index)  => {

                                            let exist_lot = _.find(available_lots, (it) =>{
                                                return it.id == lot.id
                                            })

                                            if(!exist_lot){
                                                this.form.item.lots.splice(index, 1)
                                            }

                                        })
                                    })
                                    .catch(error => {
                                    })
                                    .then(() => {
                                    })

                }

            },
            clickAddDiscount() {
                this.form.discounts.push({
                    discount_type_id: null,
                    discount_type: null,
                    description: null,
                    percentage: 0,
                    factor: 0,
                    amount: 0,
                    base: 0,
                    is_amount: false
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
                this.inputAttribute(index)
            },
            inputAttribute(index){

                let value = this.form.attributes[index].value
                let hotelAttributes = ['4003', '4004']

                this.form.attributes[index].start_date = (hotelAttributes.includes(this.form.attributes[index].attribute_type_id)) ? value:null

            },
            close() {
                this.initForm()
                this.$emit('update:showDialog', false)
            },
            async changeItem() {
                this.form.item = _.find(this.items, {'id': this.form.item_id});
                this.form.item_unit_types = _.find(this.items, {'id': this.form.item_id}).item_unit_types
                this.form.unit_price_value = this.form.item.sale_unit_price;
                this.lots = this.form.item.lots

                this.form.has_igv = this.form.item.has_igv;
                this.form.has_plastic_bag_taxes = this.form.item.has_plastic_bag_taxes;
                this.form.affectation_igv_type_id = this.form.item.sale_affectation_igv_type_id;
                this.form.quantity = 1;
                this.cleanTotalItem();
                this.showListStock = true


                if(this.form.item.attributes.length > 0) {
                    const contex = this
                    this.form.item.attributes.forEach((row)=>{

                        contex.form.attributes.push({
                            attribute_type_id: row.attribute_type_id,
                            description: row.description,
                            value: row.value,
                            start_date: row.start_date,
                            end_date: row.end_date,
                            duration: row.duration ,
                        })
                    })
                }
                this.form.lots_group = this.form.item.lots_group
                // if (!this.recordItem) {
                //     await this.form.item.warehouses.forEach(element => {
                //         if(element.checked){
                //             this.form.warehouse_id = element.warehouse_id
                //         }
                //     });
                // }

                //this.item_unit_types = this.form.item.item_unit_types;
                //(this.item_unit_types.length > 0) ? this.has_list_prices = true : this.has_list_prices = false;
            },
            focusTotalItem(change) {
                if(!change && this.form.item.calculate_quantity) {
                    this.$refs.total_item.$el.getElementsByTagName('input')[0].focus()
                    this.total_item = this.form.unit_price_value
                }
            },
            calculateQuantity() {
                if(this.form.item.calculate_quantity) {
                    this.form.quantity = _.round((this.total_item / this.form.unit_price_value), 4)
                }
                this.calculateTotal()
            },
            calculateTotal() {
                this.readonly_total = _.round((this.form.quantity * this.form.unit_price_value), 4)
            },
            cleanTotalItem(){
                this.total_item = null
            },
            async clickAddItem() {

                // if(this.form.quantity < this.getMinQuantity()){
                //     return this.$message.error(`La cantidad no puede ser inferior a ${this.getMinQuantity()}`);
                // }
                this.validateQuantity()

                if(this.form.item.lots_enabled){
                    if(!this.form.IdLoteSelected)
                        return this.$message.error('Debe seleccionar un lote.');
                }


                if (this.validateTotalItem().total_item) return;

                let unit_price = (this.form.has_igv)?this.form.unit_price_value:this.form.unit_price_value*1.18;

                this.form.input_unit_price_value = this.form.unit_price_value;

                this.form.unit_price = unit_price;
                this.form.item.unit_price = unit_price;
                this.form.item.presentation = this.item_unit_type;
                this.form.affectation_igv_type = _.find(this.affectation_igv_types, {'id': this.form.affectation_igv_type_id});

                let IdLoteSelected = this.form.IdLoteSelected
                let document_item_id = this.form.document_item_id
                this.row = calculateRowItem(this.form, this.currencyTypeIdActive, this.exchangeRateSale);

                this.row.item.name_product_pdf = this.row.name_product_pdf || '';
                if (this.recordItem) {
                    this.row.indexi = this.recordItem.indexi
                }

                let select_lots = await _.filter(this.row.item.lots, {'has_sale':true})
                let un_select_lots = await _.filter(this.row.item.lots, {'has_sale':false})

                if(this.form.item.series_enabled){
                    if(select_lots.length != this.form.quantity)
                        return this.$message.error('La cantidad de series seleccionadas son diferentes a la cantidad a vender');
                }

               // this.row.edit = false;
                this.initForm();
                //this.initializeFields()

                if (this.recordItem)
                {
                    this.row.indexi = this.recordItem.indexi
                }

                this.row.IdLoteSelected = IdLoteSelected
                this.row.document_item_id = document_item_id

                this.$emit('add', this.row);


                if (this.recordItem)
                {
                    this.close()
                }
            },
            validateTotalItem(){

                this.errors = {}

                if(this.form.item.calculate_quantity){
                    if(this.total_item < 0.01)
                        this.$set(this.errors, 'total_item', ['total venta item debe ser mayor a 0.01']);
                }

                return this.errors
            },
            async reloadDataItems(item_id) {

                if(!item_id){

                    await this.$http.get(`/${this.resource}/table/items`).then((response) => {
                        this.items = response.data
                        this.form.item_id = item_id
                        // if(item_id) this.changeItem()
                        // this.filterItems()
                    })

                }else{

                    await this.$http.get(`/${this.resource}/search/item/${item_id}`).then((response) => {

                        this.items = response.data.items
                        this.form.item_id = item_id
                        this.changeItem()

                    })
                }

            },
            changePresentation() {
                let price = 0;

                this.item_unit_type = _.find(this.form.item.item_unit_types, {'id': this.form.item_unit_type_id});

                switch (this.item_unit_type.price_default) {
                    case 1: price = this.item_unit_type.price1
                        break;
                    case 2: price = this.item_unit_type.price2
                        break;
                    case 3: price = this.item_unit_type.price3
                        break;
                }

                this.form.unit_price_value = price;
                this.form.item.unit_type_id = this.item_unit_type.unit_type_id;
            },
            selectedPrice(row)
            {
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
                this.form.item_unit_type_id = row.id
                this.item_unit_type = row

                this.form.unit_price_value = valor
                this.form.item.unit_type_id = row.unit_type_id
                this.calculateQuantity()
            },
            addRowLotGroup(id)
            {
                this.form.IdLoteSelected =  id
            },
            clickLotGroup()
            {
                this.showDialogLots = true
            },
            async clickSelectLots(){
                this.showDialogSelectLots = true
            },
            addRowSelectLot(lots){
                this.lots = lots
            },
        }
    }

</script>

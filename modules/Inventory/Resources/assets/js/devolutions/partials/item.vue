<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @open="create" @close="close" top="7vh" :close-on-click-modal="false">
        <form autocomplete="off" @submit.prevent="clickAddItem">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-6 col-lg-6 col-xl-6 col-sm-6">
                        <div class="form-group" id="custom-select" :class="{'has-danger': errors.item_id}">
                            <label class="control-label">
                                Producto
                            </label>

                            <el-input id="custom-input">
                                <el-select 
                                        v-model="form.item_id"
                                        @change="changeItem"
                                        filterable
                                        remote
                                        placeholder="Buscar"
                                        popper-class="el-select-items"
                                        slot="prepend"
                                        id="select-width"
                                        :remote-method="searchRemoteItems"
                                        :loading="loading_search">
                                        <el-option  v-for="option in items"  :key="option.id" :value="option.id" :label="option.full_description"></el-option>
                                </el-select> 
                            </el-input>  
                        </div>
                    </div> 

                    <div class="col-md-3 col-sm-3">
                        <div class="form-group" :class="{'has-danger': errors.quantity}">

                            <label class="control-label">Cantidad</label>
                            <el-input v-model="form.quantity" :disabled="form.item.calculate_quantity" @blur="validateQuantity" >
                                <el-button slot="prepend" icon="el-icon-minus" @click="clickDecrease" :disabled="form.quantity < 0.01 || form.item.calculate_quantity"></el-button>
                                <el-button slot="append" icon="el-icon-plus" @click="clickIncrease"  :disabled="form.item.calculate_quantity"></el-button>
                            </el-input>
                            <small class="form-control-feedback" v-if="errors.quantity" v-text="errors.quantity[0]"></small>

                        </div>
                    </div> 
 
                    <div style="padding-top: 1%;" class="col-md-3 col-sm-3 mt-3" v-if="form.item_id && form.item.lots_enabled && form.lots_group.length > 0">
                        <a href="#"  class="text-center font-weight-bold text-info" @click.prevent="clickLotGroup">[&#10004; Seleccionar lote]</a>
                    </div>

                    <div style="padding-top: 1%;" class="col-md-3 col-sm-3 mt-3" v-if="form.item_id && form.item.series_enabled">
                        <a href="#"  class="text-center font-weight-bold text-info" @click.prevent="clickSelectLots">[&#10004; Seleccionar series]</a>
                    </div>

  
                </div>
            </div>
            <div class="form-actions text-right pt-2">
                <el-button @click.prevent="close()">Cerrar</el-button>
                <el-button class="add" type="primary" native-type="submit" v-if="form.item_id">{{titleAction}}</el-button>
            </div>
        </form>

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

    import LotsGroup from './lots_group.vue'
    import SelectLotsForm from '@views/documents/partials/lots.vue'

    export default {
        props: ['showDialog'],
        components: {LotsGroup, SelectLotsForm},
        data() {
            return {
                loading_search:false,
                titleAction: '',
                titleDialog: '',
                resource: 'devolutions',
                errors: {},
                form: {},
                all_items: [],
                items: [],
                showDialogLots: false,
                showDialogSelectLots: false,
                lots:[],
            }
        },
        async created() {

            await this.initForm()
            await this.reloadDataItems()
            this.$eventHub.$on('reloadDataItems', () => {
                this.reloadDataItems()
            })
        },
        methods: {
            async reloadDataItems() {

                await this.$http.get(`/${this.resource}/item/tables`).then(response => {
                    // this.items = response.data.items
                    this.all_items = response.data.items 
                    this.filterItems()

                })

            },
            async searchRemoteItems(input) {

                if (input.length > 1) {

                    this.loading_search = true
                    let parameters = `input=${input}`

                    await this.$http.get(`/${this.resource}/search-items?${parameters}`)
                            .then(response => {

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
                this.errors = {};

                this.form = { 
                    item_id: null,
                    item: {},
                    quantity: 1,
                    lots_group: [],
                    IdLoteSelected: null,
                };

            },
            async create() {

                this.titleDialog =' Agregar Producto';
                this.titleAction = ' Agregar';
            }, 
            close() {
                this.initForm()
                this.$emit('update:showDialog', false)
            },
            async changeItem() {

                this.form.item = _.find(this.items, {'id': this.form.item_id});
                this.lots = this.form.item.lots

                this.form.quantity = 1;
                this.cleanTotalItem();

                this.form.lots_group = this.form.item.lots_group
 
            },  
            cleanTotalItem(){
                this.total_item = null
            },
            async clickAddItem() {
 
                this.validateQuantity()

                if(this.form.item.lots_enabled){
                    if(!this.form.IdLoteSelected)
                        return this.$message.error('Debe seleccionar un lote.');
                }

                let IdLoteSelected = this.form.IdLoteSelected

                let select_lots = await _.filter(this.form.item.lots, {'has_sale':true})
                let un_select_lots = await _.filter(this.form.item.lots, {'has_sale':false})

                // console.log(select_lots.length)

                if(this.form.item.series_enabled){
                    if(select_lots.length != this.form.quantity)
                        return this.$message.error('La cantidad de series seleccionadas son diferentes a la cantidad a vender');
                }

                if(IdLoteSelected){
                    this.form.item.lot_selected = await _.find(this.form.lots_group, {id:IdLoteSelected, checked: true})
                }

                this.form.IdLoteSelected = IdLoteSelected

                this.$emit('add', this.form);

                this.initForm();
 
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
                // console.log(isNaN(Number(this.form.quantity)))
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

            },
            clickIncrease(){
                this.form.quantity = parseInt(this.form.quantity + 1)
            },
        }
    }

</script>

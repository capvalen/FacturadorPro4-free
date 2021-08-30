<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @open="create" @close="close">
        <form autocomplete="off" @submit.prevent="clickAddItem">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group" :class="{'has-danger': errors.item_id}">
                            <label class="control-label">
                                Producto/Servicio
                                <a href="#" @click.prevent="showDialogNewItem = true">[+ Nuevo]</a>
                            </label>
                            <el-select v-model="form.item_id" @change="changeItem" filterable ref="select_item">
                                <el-option v-for="option in items" :key="option.id" :value="option.id" :label="option.full_description"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.item_id" v-text="errors.item_id[0]"></small>
                        </div>
                    </div> 
                    <div class="col-md-4">
                        <div class="form-group" :class="{'has-danger': errors.quantity}">
                            <label class="control-label">Cantidad</label>
                            <el-input-number v-model="form.quantity" :min="0.01" :disabled="form.item.calculate_quantity"></el-input-number>
                            <small class="form-control-feedback" v-if="errors.quantity" v-text="errors.quantity[0]"></small>
                        </div>
                    </div>     
                </div>
            </div>
            <div class="form-actions text-right mt-4">
                <el-button @click.prevent="close()">Cerrar</el-button>
                <el-button type="primary" native-type="submit" v-if="form.item_id">Agregar</el-button>
            </div>
        </form>
        <item-form :showDialog.sync="showDialogNewItem"
                   :external="true"></item-form>
    </el-dialog>
</template>
<style>
.el-select-dropdown { 
    max-width: 80% !important;
    margin-right: 5% !important;
}
</style>
<script>

    import itemForm from '../../../../../../../../resources/js/views/tenant/items/form.vue'

    export default {
        props: ['showDialog'],
        components: {itemForm},
        data() {
            return {
                titleDialog: 'Agregar Producto o Servicio',
                resource: 'purchase-quotations',
                showDialogNewItem: false,
                errors: {},
                form: {},
                items: [],
                aux_items: [], 
            }
        },
        async created() {
            await this.initForm()
            await this.getItems();

            this.$eventHub.$on('reloadDataItems', (item_id) => {
                this.reloadDataItems(item_id)
            })
        },
        methods: { 

            initForm() {
                this.errors = {};
                
                this.form = {
                    item_id: null,
                    item: {}, 
                    quantity: 1,
                    unit_type_id: null,
                    is_set: false,
                };
                
            }, 
            create(){},
            close() {
                this.initForm()
                this.$emit('update:showDialog', false)
            },
            changeItem() {
                this.form.item = _.find(this.items, {'id': this.form.item_id});
                this.form.quantity = 1;
            }, 
            clickAddItem() {
                 
                // this.form.affectation_igv_type = _.find(this.affectation_igv_types, {'id': this.form.affectation_igv_type_id});
                // this.row = calculateRowItem(this.form, this.currencyTypeIdActive, this.exchangeRateSale);
                this.$emit('add', this.form);
                this.initForm();
                this.setFocusSelectItem()
            },
            setFocusSelectItem(){
                this.$refs.select_item.$el.getElementsByTagName('input')[0].focus()
            }, 
            getItems() {
                this.$http.get(`/${this.resource}/item/tables`).then(response => {
                    this.items = response.data.items
                })
            }, 
            reloadDataItems(item_id) {
                this.$http.get(`/${this.resource}/table/items`).then((response) => {
                    this.items = response.data
                    this.form.item_id = item_id
                    this.changeItem()
                    // this.filterItems()

                })
            },
        }
    }

</script>
<template>
    <el-dialog :title="titleDialog" :visible="dialogVisible" @open="create" @close="close" top="8vh">
        <div class="form-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group" :class="{'has-danger': errors.items}">
                        <label class="control-label">
                            Producto
                            <a href="#" @click.prevent="showDialogNewItem = true">[+ Nuevo]</a>
                        </label>
                        <el-select v-model="form.item"
                                    filterable
                                    @change="onChangeItem"
                                    remote
                                    :remote-method="searchRemoteItems"
                                    :loading="loading_search">
                            <el-option v-for="option in items" :key="option.id" :value="option.id" :label="option.full_description"></el-option>
                        </el-select>
                        <small class="form-control-feedback" v-if="errors.items" v-text="errors.items[0]"></small>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group" :class="{'has-danger': errors.quantity}">
                        <label class="control-label">Cantidad</label>
                        <el-input-number v-model="form.quantity" :precision="4" :step="1" :min="0.01" :max="99999999"></el-input-number>
                        <small class="form-control-feedback" v-if="errors.quantity" v-text="errors.quantity[0]"></small>
                    </div>
                </div>
                <template v-if="item">
                    <div class="col-12 mt-2" v-if="item.lots_enabled && item.lots_group.length > 0">
                        <a href="#"  class="text-center font-weight-bold text-info" @click.prevent="clickLotGroup">[&#10004; Seleccionar lote]</a>
                    </div>
                </template>
            </div>
        </div>
        <span slot="footer" class="dialog-footer">
            <el-button @click.prevent="close">Cerrar</el-button>
            <el-button type="primary" @click="clickAddItem">Agregar</el-button>
        </span>

        <item-form :showDialog.sync="showDialogNewItem" :external="true"></item-form>

        <lots-group
            v-if="item"
            :quantity="form.quantity"
            :showDialog.sync="showDialogLots"
            :lots_group="item.lots_group"
            @addRowLotGroup="addRowLotGroup">
        </lots-group>
    </el-dialog>
</template>

<script>
    import itemForm from '../items/form.vue';
    import LotsGroup from '../documents/partials/lots_group.vue';

    export default {
        components: {itemForm, LotsGroup},
        props: ['dialogVisible'],
        data() {
            return {
                titleDialog: 'Agregar Producto',
                showDialogNewItem: false,
                resource: 'dispatches',
                errors: {},
                items: [],
                form: {},
                showDialogLots: false,
                item: null,
                loading_search:false,
            }
        },
        methods: {
            clickLotGroup() {
                this.showDialogLots = true
            },
            onChangeItem() {
                this.form.IdLoteSelected = null;
                this.item = this.items.find(it => it.id == this.form.item);
            },
            addRowLotGroup(id) {
                this.form.IdLoteSelected =  id;
            },
            create() {
                this.$http.post(`/${this.resource}/tables`).then(response => {
                    this.items = response.data.items;
                });

                this.form = {};
            },
            close() {
                this.$emit('update:dialogVisible', false);
            },
            clickAddItem() {
                this.errors = {};

                if(this.item.lots_enabled){
                    if(! this.form.IdLoteSelected)
                        return this.$message.error('Debe seleccionar un lote.');
                }

                if ((this.form.item != null) && (this.form.quantity != null)) {
                    const item = this.items.find((item) => item.id == this.form.item)
                    item.IdLoteSelected = this.form.IdLoteSelected;
                    this.$emit('addItem', {
                        item,
                        quantity: this.form.quantity,
                    });

                    this.form = {};
                    this.item = null;
                    return;
                }

                if (this.form.item == null) this.$set(this.errors, 'items', ['Seleccione el producto']);

                if (this.form.quantity == null) this.$set(this.errors, 'quantity', ['Digite la cantidad']);

                this.form.IdLoteSelected = null;
            },
            async searchRemoteItems(input) {
                if (input.length > 2) {
                    this.loading_search = true
                    const params = {
                        'input': input,
                        'search_by_barcode': this.search_item_by_barcode ? 1 : 0
                    }
                    await this.$http.get(`/documents/search-items/`, { params })
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
        }
    }
</script>

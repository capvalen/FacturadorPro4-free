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
                        <el-select :disabled="recordItem != null" v-model="form.item" filterable>
                            <el-option v-for="option in items" :key="option.id" :value="option.id" :label="option.full_description"></el-option>
                        </el-select>
                        <small class="form-control-feedback" v-if="errors.items" v-text="errors.items[0]"></small>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group" :class="{'has-danger': errors.quantity}">
                        <label class="control-label">Cantidad</label>
                        <el-input-number v-model="form.quantity" :precision="4" :step="1" :min="0" :max="99999999"></el-input-number>
                        <small class="form-control-feedback" v-if="errors.quantity" v-text="errors.quantity[0]"></small>
                    </div>
                </div>
            </div>
        </div>
        <span slot="footer" class="dialog-footer">
            <el-button @click.prevent="close">Cerrar</el-button>
            <el-button type="primary" @click="clickAddItem">Guardar</el-button>
        </span>

        <item-form :showDialog.sync="showDialogNewItem" :external="true"></item-form>
    </el-dialog>
</template>

<script>
    import itemForm from '@views/items/form.vue';

    export default {
        components: {itemForm},
        props: ['dialogVisible', 'recordItem'],
        data() {
            return {
                titleDialog: 'Agregar Producto',
                showDialogNewItem: false,
                resource: 'order-forms',
                errors: {},
                items: [],
                form: {}
            }
        },
        methods: {
            async create() {

                await  this.$http.post(`/${this.resource}/tables`).then(response => {
                    this.items = response.data.items;
                });

                if(this.recordItem)
                {
                    this.form = { quantity: this.recordItem.quantity, item: this.recordItem.item_id };
                }
                else{
                    this.form = { quantity:0 };
                }

            },
            close() {
                this.$emit('update:dialogVisible', false);
            },
            clickAddItem() {
                this.errors = {};


                if(this.recordItem)
                {
                    this.recordItem.quantity = this.form.quantity
                    this.form = {}
                    this.$emit('update:dialogVisible', false);

                }else{

                    if ((this.form.item != null) && (this.form.quantity != null)) {
                        this.$emit('addItem', {
                            item: this.items.find((item) => item.id == this.form.item),
                            quantity: this.form.quantity
                        });

                        this.form = {quantity:0};

                        return;
                    }

                    if (this.form.item == null) this.$set(this.errors, 'items', ['Seleccione el producto']);

                    if (this.form.quantity == null) this.$set(this.errors, 'quantity', ['Digite la cantidad']);
                }


            }
        }
    }
</script>

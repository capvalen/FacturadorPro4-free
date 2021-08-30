<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @open="create" @close="close"  append-to-body >
        <form autocomplete="off" @submit.prevent="clickAddItem">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-8 col-lg-8 col-xl-8 col-sm-8">
                        <div class="form-group"  :class="{'has-danger': errors.individual_item_id}">
                            <label class="control-label">
                                Producto
                            </label>
                            <el-select
                                    v-model="form.individual_item_id" @change="changeItem"
                                    filterable
                                    placeholder="Buscar" >

                                    <el-option v-for="option in individual_items" :key="option.id" :value="option.id" :label="option.full_description"></el-option>
                            </el-select>

                            <small class="form-control-feedback" v-if="errors.individual_item_id" v-text="errors.individual_item_id[0]"></small>
                        </div>
                    </div> 
                    <div class="col-md-4">
                        <div class="form-group" :class="{'has-danger': errors.quantity}">
                            <label class="control-label">Cantidad</label>
                            <el-input-number v-model="form.quantity" :min="0.01"></el-input-number>
                            <small class="form-control-feedback" v-if="errors.quantity" v-text="errors.quantity[0]"></small>
                        </div>
                    </div>  
                </div>
            </div>
            <div class="form-actions text-right mt-2">
                <el-button @click.prevent="close()">Cerrar</el-button>
                <el-button type="primary" native-type="submit" v-if="form.individual_item_id">Agregar</el-button>
            </div>
        </form>
         
    </el-dialog>
</template>
<style>
.el-select-dropdown {
    max-width: 80% !important;
    margin-right: 5% !important;
}
</style>
<script>


    export default {
        props: ['showDialog'],
        data() {
            return {
                titleDialog: 'Agregar Producto',
                resource: 'item-sets',
                errors: {},
                form: {},
                individual_items: [],
            }
        },
        created() {
            this.initForm()

            this.$http.get(`/${this.resource}/item/tables`).then(response => {

                this.individual_items = response.data.individual_items

            })
        },
        methods: {
            initForm() {

                this.errors = {}

                this.form = {
                    individual_item_id: null,
                    sale_unit_price: 0,
                    quantity: 1,
                    full_description: null
                }
 
            },
            create() {
            },
            close() {
                this.initForm()
                this.$emit('update:showDialog', false)
            },
            changeItem() { 
            
                let item = _.find(this.individual_items, {'id': this.form.individual_item_id})
                this.form.sale_unit_price = item.sale_unit_price
                this.form.full_description = item.full_description
            
            }, 
            async clickAddItem() {

                this.$emit('add', this.form);
                this.initForm();

            },
        }
    }

</script>

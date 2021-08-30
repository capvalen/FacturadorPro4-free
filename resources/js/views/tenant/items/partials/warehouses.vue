<template>
    <el-dialog :title="titleDialog" :visible="showDialog"   @close="close"   append-to-body top="7vh">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-12" v-if="warehouses">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Ubicación</th>
                                <th class="text-right">Stock</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(row, index) in warehouses" :key="index">
                                <th>{{ row.warehouse_description }}</th>
                                <th class="text-right">{{ row.stock }}</th>
                            </tr>
                            </tbody>
                        </table>

                        <template v-if="item_unit_types.length > 0">
                            <h5>Lista de Precios Creados</h5>
                            <table class="table">
                            <thead>
                                <tr>
                                <th>Unidad</th>
                                <th>Description</th>
                                <th>Factor</th>
                                <th>Precio 1</th>
                                <th>Precio 2</th>
                                <th>Precio 3</th>
                                <th>P.Defecto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(row, index) in item_unit_types" :key="index">
                                <th>{{ row.unit_type_id }}</th>
                                <th>{{ row.description }}</th>
                                <th>{{ row.quantity_unit }}</th>
                                <th>{{ row.price1 }}</th>
                                <th>{{ row.price2 }}</th>
                                <th>{{ row.price3 }}</th>
                                <th>Precio {{ row.price_default }}</th>
                                </tr>
                            </tbody>
                            </table>
                        </template>

                    </div>
                </div>
            </div>
            <div class="form-actions text-right pt-2">
                <el-button @click.prevent="close()">Cerrar</el-button>
            </div>
        </form>
    </el-dialog>
</template>

<script>


    export default {
        props:['showDialog', 'warehouses', 'item_unit_types'],
        data() {
            return {
                showImportDialog: false,
                resource: 'items',
                recordId: null,
                titleDialog: 'Stock de producto',

            }
        },
        created() {
            //console.log(this.typeUser)
        },
        methods: {
            close() {
                this.$emit('update:showDialog', false)
            },
        }
    }
</script>

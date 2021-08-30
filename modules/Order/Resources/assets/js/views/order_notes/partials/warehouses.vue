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
                            <tr v-for="row in warehouses">
                                <th>{{ row.warehouse_description }}</th>
                                <th class="text-right">{{ row.stock }}</th>
                            </tr>
                            </tbody>
                        </table>
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
        props:['showDialog', 'warehouses'],
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

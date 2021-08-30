<template> 
    <el-dialog :title="titleDialog" :visible="showDialog" @open="create"  @close="close"   append-to-body top="7vh" :close-on-click-modal="false">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-12" v-if="warehouses">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Seleccionar
                                        <el-tooltip class="item" effect="dark" content="Seleccionar almacén desde el cuál se descontará stock" placement="top">
                                            <i class="fa fa-info-circle"></i>
                                        </el-tooltip>
                                    </th>
                                    <th>Ubicación</th>
                                    <th class="text-right">Stock</th>
                                </tr>
                            </thead>
                            <tbody>    
                                <tr v-for="(row,index) in warehouses" :key="index">
                                    <th align="center">
                                        <el-checkbox   v-model="row.checked" @change="changeWarehouse(index)"></el-checkbox>
                                    </th>
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
        props:['showDialog', 'warehouses', 'isUpdateWarehouseId'],
        data() {
            return {
                showImportDialog: false,
                resource: 'items',
                recordId: null,
                titleDialog: 'Almacenes/Stock',
                my_warehouses:[],
                warehouse_id:null,

            }
        },
        created() {
            // console.log(this.typeUser)
        },
        methods: {
            async create(){

                this.warehouse_id = null
                // console.log(this.isUpdateWarehouseId)
                
                if(this.isUpdateWarehouseId){
                    await this.warehouses.forEach((it) => {
                        if(it.warehouse_id == this.isUpdateWarehouseId){
                            it.checked = true
                        }else{
                            it.checked = false
                        }
                    });
                }
            },
            async changeWarehouse(index){

                await this.warehouses.forEach((it, ind) => {
                    // console.log(ind, index)
                    if(ind != index){
                        it.checked = false
                    }
                });

                await this.selectWarehouseId()
            },
            async selectWarehouseId(){

                await this.warehouses.forEach((it, ind) => {
                    if(it.checked){
                        this.warehouse_id = it.warehouse_id
                    }
                });

            },
            async close() {

                await this.selectWarehouseId()

                if(!this.warehouse_id)
                    return this.$message.error('Debe seleccionar un almacén');

                await this.$eventHub.$emit('selectWarehouseId', this.warehouse_id) 
                await this.$emit('update:showDialog', false)
                
            },
        }
    }
</script>

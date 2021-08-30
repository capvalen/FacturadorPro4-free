<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>{{ title }}</span></li>
            </ol>
            <div v-if="typeUser == 'admin'" class="right-wrapper pull-right">
                <!--<button type="button" class="btn btn-custom btn-sm  mt-2 mr-2" @click.prevent="clickImport()"><i class="fa fa-upload"></i> Importar</button>-->
                <button type="button" class="btn btn-custom btn-sm  mt-2 mr-2" @click.prevent="clickCreate('input')"><i class="fa fa-plus-circle"></i> Ingreso</button>
                <button type="button" class="btn btn-custom btn-sm  mt-2 mr-2" @click.prevent="clickOutput()"><i class="fa fa-minus-circle"></i> Salida</button>
            </div>
        </div>
        <div class="card mb-0">
            <div class="card-header bg-info">
                <h3 class="my-0">Listado de {{ title }}</h3>
            </div>
            <div class="card-body">
                <data-table :resource="resource" ref="datatable">
                    <tr slot="heading">
                        <th>
                            <el-dropdown>
                                <span class="el-dropdown-link">
                                    <el-button>
                                        <i class="fa fa-ellipsis-v"></i>
                                    </el-button>
                                </span>
                                <el-dropdown-menu slot="dropdown">
                                    <el-dropdown-item @click.native="onChecktAll">Seleccionar todo</el-dropdown-item>
                                    <el-dropdown-item @click.native="onUnCheckAll">Deseleccionar todo</el-dropdown-item>
                                    <el-dropdown-item @click.native="onOpenModalMoveGlobal">Trasladar</el-dropdown-item>
                                </el-dropdown-menu>
                            </el-dropdown>
                        </th>
                        <th>Producto</th>
                        <th>Almacén</th>
                        <th class="text-right">Stock</th>
                        <th class="text-right">Acciones</th>
                    <tr>
                    <tr slot-scope="{ index, row }" :key="index">
                        <td>
                            <el-switch v-model="row.selected" @click="onChangeSelectedStatus(row)"></el-switch>
                        </td>
                        <!-- <td>{{ index }}</td> -->
                        <td>{{ row.item_fulldescription }}</td>
                        <td>{{ row.warehouse_description }}</td>
                        <td class="text-right">{{ row.stock }}</td>
                        <td class="text-right">
                            <button type="button" class="btn waves-effect waves-light btn-xs btn-info"
                                    @click.prevent="clickMove(row.id)">Trasladar</button>
                            <button v-if="typeUser == 'admin'" type="button" class="btn waves-effect waves-light btn-xs btn-warning"
                                    @click.prevent="clickRemove(row.id)">Remover</button>
                        </td>
                    </tr>
                </data-table>
            </div>

            <inventories-form
                            :showDialog.sync="showDialog"
                            :type="typeTransaction"
                                ></inventories-form>

            <inventories-form-output
                            :showDialog.sync="showDialogOutput"
                            ></inventories-form-output>

            <inventories-move :showDialog.sync="showDialogMove"
                              :recordId="recordId"></inventories-move>
            <inventories-remove :showDialog.sync="showDialogRemove"
                                :recordId="recordId"></inventories-remove>
            <MoveGlobal :products="selectedItems" :show.sync="showHideModalMoveGlobal"></MoveGlobal>
        </div>
    </div>
</template>

<script>

    import InventoriesForm from './form.vue'
    import InventoriesFormOutput from './form_output.vue'

    import InventoriesMove from './move.vue'
    import InventoriesRemove from './remove.vue'
    import DataTable from '../../../../../../resources/js/components/DataTable.vue'
    import MoveGlobal from './MoveGlobal.vue';

    export default {
        props: ['type', 'typeUser'],
        components: {DataTable, InventoriesForm, InventoriesMove, InventoriesRemove, InventoriesFormOutput, MoveGlobal},
        data() {
            return {
                showHideModalMoveGlobal: false,
                selectedItems: [],
                title: null,
                showDialog: false,
                showDialogMove: false,
                showDialogRemove: false,
                showDialogOutput: false,
                resource: 'inventory',
                recordId: null,
                typeTransaction:null,
            }
        },
        created() {
            this.title = 'Inventario'
        },
        methods: {
            async onOpenModalMoveGlobal() {
                const itemsSelecteds = await this.$refs.datatable.records.filter(p => p.selected);
                if (itemsSelecteds.length > 0) {
                    this.selectedItems = itemsSelecteds;
                    this.showHideModalMoveGlobal = true;
                } else {
                    this.$message({
                        message: 'Selecciona uno o más productos.',
                        type: 'warning'
                    });
                }
            },
            async onChangeSelectedStatus(row) {
                this.$refs.datatable.records = await this.$refs.datatable.records.map(r => {
                    if (r.id === row.id) {
                        r.selected = row.selected ? false : true;
                    }
                    return r;
                });
                this.$forceUpdate();
            },
            onChecktAll() {
                this.$refs.datatable.records = this.$refs.datatable.records.map(r => {
                    r.selected = true;
                    return r;
                });
            },
            onUnCheckAll() {
                this.$refs.datatable.records = this.$refs.datatable.records.map(r => {
                    r.selected = false;
                    return r;
                });
            },
            clickMove(recordId) {
                this.recordId = recordId
                this.showDialogMove = true
            },
            clickCreate(type) {
                this.recordId = null
                this.typeTransaction = type
                this.showDialog = true
            },
            clickRemove(recordId) {
                this.recordId = recordId
                this.showDialogRemove = true
            },
            clickOutput() {
                this.recordId = null
                this.showDialogOutput = true
            }

        }
    }
</script>

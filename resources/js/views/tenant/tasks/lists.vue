<template>
    <div class="card">
        <div class="card-header bg-info">
            <h3 class="my-0">Tareas programadas</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <button type="button" class="btn btn-custom btn-sm  mt-2 mr-2 mb-3" @click.prevent="clickCreate()"><i class="fa fa-plus-circle"></i> Nueva</button>
                </div>
            </div>
            <template>
                <el-table :data="tableData" style="width: 100%">
                    <el-table-column label="Clase" prop="class"></el-table-column>
                    <el-table-column label="Hora de ejecución" prop="execution_time"></el-table-column>
                    <el-table-column label="Ultima ejecución" prop="updated_at"></el-table-column>
                    <el-table-column label="Log" prop="output"></el-table-column>
                    <el-table-column fixed="right" label="Acciones" width="120">
                        <template slot-scope="scope">
                            <el-button block size="mini" type="danger" @click="handleDelete(scope.$index, scope.row)">Eliminar</el-button>
                        </template>
                    </el-table-column>
                </el-table>
            </template>
        </div>
        <tenant-tasks-form :showDialog.sync="showDialog" :tableData.sync="tableData" @refresh="refresh"></tenant-tasks-form>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                resource: 'tasks',
                showDialog: false,
                tableData: [],
            }
        },
        created() {
            this.refresh();
        },
        methods: {
            refresh() {
                this.$http.post(`/${this.resource}/tables`).then((response) => {
                    this.tableData = response.data;
                }).catch((error) => {
                    console.log(error);
                }).then(() => {});
            },
            handleDelete(index, row) {
                this.$http.delete(`/${this.resource}/${row.id}`).then((response) => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        this.tableData.splice(index, 1);
                    }
                    else {
                        this.$message.error(response.data.message);
                    }
                }).catch((error) => {
                    this.$message.error(error.response.data.message);
                }).then(() => {});
            },
            clickCreate() {
                this.showDialog = true;
            }
        }
    }
</script>

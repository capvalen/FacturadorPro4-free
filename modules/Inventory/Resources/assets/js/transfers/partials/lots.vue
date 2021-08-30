<template>
    <el-dialog :title="titleDialog" width="40%"  :visible="showDialog"  @open="create"  :close-on-click-modal="false" :close-on-press-escape="false" append-to-body :show-close="false">

        <div class="form-body">
            <div class="row" >
                <div class="col-lg-12 col-md-12 table-responsive">
                    <table width="100%" class="table">
                        <thead>
                            <tr width="100%">
                                <th class="text-center">Seleccionar</th>
                                <th >Cod. Lote</th>
                                <th>Serie</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row, index) in lots" :key="index" width="100%">
                                <!-- <td>{{index}}</td> -->
                                <td class="text-center">
                                    <el-checkbox v-model="row.has_sale"></el-checkbox>
                                </td>
                                <td>
                                    {{row.lot_code}}
                                </td>
                                <td>
                                    {{row.series}}
                                </td>
                                <td>
                                    {{row.date}}
                                </td>
                                <br>
                            </tr>
                        </tbody>
                    </table>


                </div>

            </div>
        </div>

        <div class="form-actions text-right pt-2">
            <el-button @click.prevent="close()">Cerrar</el-button>
            <!-- <el-button type="primary" @click="submit" >Guardar</el-button> -->
        </div>
    </el-dialog>
</template>

<script>
    export default {
        props: ['showDialog', 'lots'],
        data() {
            return {
                titleDialog: 'Series',
                loading: false,
                errors: {},
                form: {},

            }
        },
        async created() {

        },
        methods: {
            create(){

            },
            async submit(){

                // let val_lots = await this.validateLots()
                // if(!val_lots.success)
                //     return this.$message.error(val_lots.message);

                // await this.$emit('addRowLot', this.lots);
                // await this.$emit('update:showDialog', false)

            },
            close() {
                this.$emit('update:showDialog', false)
                this.$emit('addRowOutputLot', this.lots);
            },
            async clickCancelSubmit() {

                // this.$emit('addRowLot', []);
                // await this.$emit('update:showDialog', false)

            },
        }
    }
</script>

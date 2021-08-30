<template>
    <el-dialog :title="titleDialog" width="40%"  :visible="showDialog"  @open="create"  :close-on-click-modal="false" :close-on-press-escape="false" append-to-body :show-close="false">

        <div class="form-body">
            <div class="row" >
                <div class="col-lg-12 col-md-12 table-responsive">
                    <div class="col-lg-5 col-md-5 col-sm-12 pb-2">
                        <el-input placeholder="Buscar serie ..."
                            v-model="search"
                            style="width: 100%;"
                            prefix-icon="el-icon-search"
                            >
                        </el-input>
                    </div>
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
                            <tr v-for="(row, index) in records" :key="index" width="100%">
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
            <el-button type="primary" @click.prevent="close()">Guardar</el-button>
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
                search: '',
            }
        },
        async created() {

        },
        computed:{
            records()
            {
                return this.search ? _.filter(this.lots, x => x.series.toUpperCase().includes(this.search.toUpperCase())) : this.lots
            }
        },
        watch:{
        },
        methods: {
            create(){

            },
            async submit(){

            },
            close() {
                this.$emit('update:showDialog', false)
                //this.$emit('addRowSelectLot', this.lots_);
            },
            async clickCancelSubmit() {

            },
        }
    }
</script>

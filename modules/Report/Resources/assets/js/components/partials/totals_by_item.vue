<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @close="close" @open="create" :close-on-click-modal="false" append-to-body>
        <form autocomplete="off" @submit.prevent="submit" v-loading="loading">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-12">
                        <el-button class="submit" type="danger"  icon="el-icon-tickets" @click.prevent="clickDownload('pdf')" >Exportar PDF</el-button>
                        <el-button class="submit" type="success" @click.prevent="clickDownload('excel')"><i class="fa fa-file-excel" ></i>  Exportal Excel</el-button>
                        <el-button class="submit" type="success" @click.prevent="onGenerateGuide">Generar guía</el-button>
                    </div>
                    <div class="col-md-12 mt-2">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <!-- <th class="">#</th> -->
                                        <th  class="text-center">Cod. Interno</th>
                                        <th  class="text-left">Producto</th>
                                        <th  class="text-center">Unidad</th>
                                        <th  class="text-center">Cantidad Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(row, index) in records" :key="index">
                                        <!-- <td>{{ index+1  }}</td>  -->
                                        <td  class="text-center">{{row.item_internal_id}}</td>
                                        <td  class="text-left">{{row.item_description}}</td>
                                        <td  class="text-center">{{row.item_unit_type_id}}</td>
                                        <td  class="text-center">{{row.quantity}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>
            <div class="form-actions text-right mt-4">
                <el-button @click.prevent="close()">Cerrar</el-button>
            </div>
        </form>
    </el-dialog>
</template>

<script>


    export default {
        props: ['showDialog', 'parameters'],
        data() {
            return {
                loading: false,
                titleDialog: 'Totales por productos',
                resource: 'reports/sales-consolidated',
                records: [],
            }
        },
        methods: {
            onGenerateGuide() {
                const items = [];
                Object.keys(this.records).forEach((r) => {
                    items.push({
                        id: this.records[r].item_id,
                        quantity: this.records[r].quantity
                    })
                });
                localStorage.setItem('items', JSON.stringify(items));
                const tab = window.open('/dispatches/create', '_BLANK');
                tab.focus();
            },
            clickDownload(type) {
                window.open(`/${this.resource}/${type}-totals/?${this.parameters}`, '_blank');
            },
            close() {
                this.$emit('update:showDialog', false)
            },
            create(){
                this.getRecords()
            },
            getRecords() {

                this.loading = true
                this.$http.get(`/${this.resource}/totals-by-item?${this.parameters}`).then((response) => {
                    this.records = response.data
                })
                .then(()=>{
                    this.loading = false
                })

            },
        }
    }
</script>

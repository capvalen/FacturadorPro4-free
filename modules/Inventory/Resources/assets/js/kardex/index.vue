<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <!--<div class="card-header bg-info">
            <h3 class="my-0">Consulta kardex</h3>
        </div> -->
        <div class="card mb-0">
                <div class="card-body">
                    <data-table :resource="resource">
                        <tr slot="heading">
                            <th>#</th>
                            <th v-if="!item_id">Producto</th>
                            <th>Fecha y hora transacción</th>
                            <th>Tipo transacción</th>
                            <th>Número</th>
                            <th>NV. Asociada</th>
                            <th>Pedido</th>
                            <th>Doc. Asociado</th>
                            <th>Feha emisión</th>
                            <th>Entrada</th>
                            <th>Salida</th>
                            <th v-if="item_id">Saldo</th>

                        <tr>
                        <tr slot-scope="{ index, row }">
                            <td>{{ index }}</td>
                            <td v-if="!item_id">{{row.item_name}}</td>
                            <td>{{row.date_time}}</td>
                            <td>{{row.type_transaction}}</td>
                            <td>{{row.number}}</td>
                            <td>{{row.sale_note_asoc}}</td>
                            <td>{{row.order_note_asoc}}</td>
                            <td>{{row.doc_asoc}}</td>
                            <td>{{row.date_of_issue}}</td>
                            <td>{{row.input}}</td>
                            <td>{{row.output}}</td>
                            <td v-if="item_id">{{row.balance}}</td>
                        </tr>

                    </data-table>


                </div>
        </div>

    </div>
</template>

<script>

    import DataTable from '../../components/DataTableKardex.vue'

    export default {
        components: {DataTable},
        data() {
            return {
                resource: 'reports/kardex',
                form: {},
                item_id:null
            }
        },
        created() {
            this.$eventHub.$on('emitItemID', (item_id) => {
                // console.log(item_id)
                this.item_id = item_id
            })
        },
        methods: {


        }
    }
</script>

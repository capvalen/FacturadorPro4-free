<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <div class="card-header bg-info">
            <h3 class="my-0">Reporte general de productos</h3>
        </div>
        <div class="card mb-0">
                <div class="card-body">
                    <data-table :resource="resource">
                        <tr slot="heading">
                            <th class="">#</th>
                            <th class="">F. Emisión</th>
                            <th class="">Tipo Documento</th>
                            <th class="">Serie</th>
                            <th class="">Número</th>
                            <th class="">N° Documento</th>
                            <th class="">Cliente</th>
                            <th class="">Cod. Interno</th>
                            <th>Marca</th>
                            <th class="">Descripción</th>
                            <!-- <th class="">U. Medida</th> -->
                            <th class="">Cantidad</th>
                            <th>Series</th>
                            <th>Plataforma</th>
                            <th class="">Moneda</th>
                            <th class="">Valor unitario</th>
                            <th class="">Total</th>
                            <template v-if="type == 'sale'">
                                <th class="">Total compra</th>
                                <th class="">Ganancia</th>
                            </template>
                        <tr>
                        <tr slot-scope="{ index, row }">
                            <td>{{ index }}</td>
                            <td>{{row.date_of_issue}}</td>
                            <td>{{row.document_type_description}}</td>
                            <td>{{row.series}}</td>
                            <td>{{row.alone_number}}</td>
                            <td>{{row.customer_number}}</td>
                            <td>{{row.customer_name}}</td>
                            <td>{{row.internal_id}}</td>
                            <td>{{ row.brand }}</td>
                            <td>{{row.description}}</td>
                            <!-- <td>{{row.unit_type_id}}</td> -->
                            <td>{{row.quantity}}</td>
                            <td>
                                {{ row.lot_has_sale | filterLots }}
                            </td>
                            <td>{{row.web_platform_name}}</td>
                            <td>{{row.currency_type_id}}</td>
                            <td>{{row.unit_value}}</td>
                            <td>{{row.total}}</td>
                            <template v-if="type == 'sale'">
                                <td>{{row.total_item_purchase}}</td>
                                <td>{{row.utility_item}}</td>
                            </template>
                        </tr>

                    </data-table>
                </div>
        </div>

    </div>
</template>

<script>
    import DataTable from '../../components/DataTableGeneralItems.vue'

    export default {
        components: {DataTable},
        data() {
            return {
                resource: 'reports/general-items',
                form: {},
                type: "sale",
            }
        },
        filters:{
            filterLots(data){

                if(data && data.length > 0)
                {
                    const lots_sale = data.filter(x=> x.has_sale == true)
                    if(lots_sale)
                    {
                        return lots_sale.map(p=> p.series).join(' - ')
                    }
                    else{
                        return ''
                    }
                }
                else{
                    return ''
                }

            }
        },
        async created() {

            this.$eventHub.$on('typeTransaction', (type) => {
                this.type = type
            })

        },
        methods: {


        }
    }
</script>

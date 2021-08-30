<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <div class="card-header bg-info">
            <h3 class="my-0">Pagos</h3>
        </div>
        <div class="card mb-0">
                <div class="card-body">
                    <data-table :resource="resource">
                        <tr slot="heading">
                            <th class="">#</th>
                            <th class="">Adquiriente</th>
                            <th class="">Documento/Transacción</th>
                            <th class="">Moneda</th>
                            <th class="">Tipo</th>
                            <th class="">Destino</th>
                            <th class="">F. Pago</th>
                            <th class="">Método</th>
                            <th class="">Referencia</th>
                            <th class="">Responsable</th>
                            <th class="">Pago</th>
 
                        <tr>
                        <tr slot-scope="{ index, row }">
                            <td>{{ index }}</td>
                            <td>{{ row.person_name }}<br/><small v-text="row.person_number"></small></td>
                            <td>
                                {{row.number_full}}<br/>
                                <small v-text="row.document_type_description"></small>
                            </td>
                            <td>{{row.currency_type_id}}</td>
                            <td>{{row.instance_type_description}}</td>
                            <td>{{row.destination_description}}</td>
                            <td>{{row.date_of_payment}}</td>
                            <td>{{row.payment_method_type_description}}</td>
                            <td>{{row.reference}}</td>
                            <td>{{row.user_name}}</td> 
                            <td>{{row.total}}</td> 
                        </tr>
                    </data-table>


                </div>
        </div>

    </div>
</template>

<script>

    import DataTable from '../../components/DataTableGlobalPayment.vue'

    export default {
        components: {DataTable},
        data() {
            return {
                resource: 'finances/global-payments',
                form: {},

            }
        },
        async created() {
        },
        methods: {


        }
    }
</script>

<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <div class="card-header bg-info">
            <h3 class="my-0">Consulta de Cotizaciones</h3>
        </div>
        <div class="card mb-0">
                <div class="card-body">
                    <data-table :resource="resource" :applyCustomer="true">
                        <tr slot="heading">
                            <th>#</th>
                            <th class="text-center">Fecha Emisión</th>
                            <th class="">Usuario/Vendedor</th>
                            <th>Cliente</th>
                            <th>Estado</th>
                            <th>Cotización</th>

                            <th>Comprobantes</th>
                            <th>Notas de venta</th>
                            <th>Caso</th>
                            <th class="text-center">Moneda</th>
                            <th class="text-right">T.Exportación</th>
                            <th class="text-right" >T.Inafecta</th>

                            <th class="text-right">T.Exonerado</th>
                            <th class="text-right">T.Gravado</th>
                            <th class="text-right">T.Igv</th>
                            <th class="text-right">Total</th>
                        <tr>
                        <tr slot-scope="{ index, row }">
                            <td>{{ index }}</td>  
                            <td>{{row.date_of_issue}}</td>
                            <td>{{row.user_name}}</td>
                            <td>{{row.customer_name}}</td>
                            <td>{{row.state_type_description}}</td>
                            <td>{{row.identifier}}</td>
                            <td>
                                <template v-for="(doc,i) in row.documents">                                
                                    <label class="d-block"  :key="i">{{doc.number_full}}</label>
                                </template>
                            </td>
                            <td>
                                <template v-for="(s_note,i) in row.sale_notes">                                
                                    <label class="d-block"  :key="i">{{s_note.identifier}}</label>
                                </template>
                            </td>
                            <td>{{row.sale_opportunity_number_full}}</td>
                            <td>{{row.currency_type_id}}</td>
                            <td >{{ row.total_exportation }}</td>
                            <td >{{ row.total_unaffected }}</td>
                            <td >{{ row.total_exonerated }}</td>

                            <td>{{ row.total_taxed}}</td>
                            <td>{{ row.total_igv}}</td>
                            <td>{{ row.total}}</td>
                            
                        </tr>
                        
                    </data-table>
                     
                    
                </div> 
        </div>
 
    </div>
</template>

<script>
 
    import DataTable from '../../components/DataTableReports.vue'

    export default { 
        components: {DataTable},
        data() {
            return {
                resource: 'reports/quotations',                 
                form: {}, 

            }
        },
        async created() { 
        },
        methods: { 
             
            
        }
    }
</script>
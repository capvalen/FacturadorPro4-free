<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <div class="card-header bg-info">
            <h3 class="my-0">Reporte Caja - POS</h3>
        </div>
        <div class="card mb-0">
                <div class="card-body">
                    <data-table :resource="resource">
                        <tr slot="heading">
                            <th class="">#</th>
                            <th class="">Tipo Documento</th>
                            <th class="">Comprobante</th>
                            <th class="">Fecha emisión</th>
                            <th>Doc. Afectado</th>
                            <th class="">Cliente</th>
                            <th class="">Estado</th>
                            <th class="">Moneda</th> 
                            <th class="">Total Gravado</th>
                            
                            <th class="">Total IGV</th>
                            <th class="">Total</th>
                        <tr>
                        <tr slot-scope="{ index, row }">
                            <td>{{ index }}</td> 
                            <td>{{row.document_type_description}}</td>
                            <td>{{row.number}}</td>
                            <td>{{row.date_of_issue}}</td>
                            <td>{{row.affected_document}}</td>
                            <td>{{ row.customer_name }}<br/><small v-text="row.customer_number"></small></td> 
                            <td>{{row.state_type_description}}</td>

                            <td>{{ row.currency_type_id}}</td> 
                            <td>{{ (row.document_type_id == '07' || (row.document_type_id!='07' && row.state_type_id =='11')) ? -row.total_taxed : row.total_taxed }}</td>
                            <td>{{ (row.document_type_id == '07' || (row.document_type_id!='07' && row.state_type_id =='11')) ? -row.total_igv : row.total_igv }}</td>
                            <td>{{ (row.document_type_id == '07' || (row.document_type_id!='07' && row.state_type_id =='11')) ? -row.total : row.total }}</td> 

                        </tr>
                        
                    </data-table>
                     
                    
                </div> 
        </div>
 
    </div>
</template>

<script>
 
    import DataTable from '../../components/DataTableCash.vue'

    export default { 
        components: {DataTable},
        data() {
            return {
                resource: 'reports/cash',                 
                form: {}, 

            }
        },
        async created() { 
        },
        methods: { 
             
            
        }
    }
</script>
<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <div class="card-header bg-info">
            <h3 class="my-0">Consulta de detracciones</h3>
        </div>
        <div class="card mb-0">
                <div class="card-body">
                    <data-table :resource="resource">
                        <tr slot="heading">
                            <th>#</th>
                            <th >Fecha de detracción</th>
                            <th>Comprobante</th>
                            <th>Cliente</th>
                            <th class="text-center">Constancia Pago</th>
                            <th class="text-center">Total detracción</th>
                            <th class="text-center">Descarga</th>
 
                        <tr>
                        <tr slot-scope="{ index, row }">
                            <td>{{ index }}</td>  
                            <td>{{row.date_of_issue}}</td>
                            <td>{{ row.number }}<br/>
                                <small v-text="row.document_type_description"></small><br/>
                            </td>
                            <td>{{ row.customer_name }}<br/><small v-text="row.customer_number"></small></td> 
                            <td class="text-center">{{row.detraction.pay_constancy ? row.detraction.pay_constancy:'-'}}</td> 
                            <td class="text-center">{{row.detraction.amount}}</td> 
                            <td class="text-center">
                                <template v-if="row.image_detraction">
                                    <a :href="`${row.image_detraction}`" download class="btn waves-effect waves-light btn-xs btn-custom m-1__2">C. Pago</a>
                                </template>
                                <template v-else>
                                    -
                                </template>
                            </td> 
                            
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
                resource: 'reports/document-detractions',                 
                form: {}, 

            }
        },
        async created() { 
        },
        methods: { 
             
            clickDownloadImage(image_detraction) {
                window.open(`${image_detraction}`, '_blank');
            },
        }
    }
</script>
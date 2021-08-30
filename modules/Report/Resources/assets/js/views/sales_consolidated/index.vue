<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <div class="card-header bg-info">
            <h3 class="my-0">Consolidado de items por cliente/vendedor - ventas</h3>
        </div>
        <div class="card mb-0">
                <div class="card-body">
                    <data-table :resource="resource">
                        <tr slot="heading">
                            <th class="">#</th>
                            <th  class="text-left">Documento</th>
                            <th  class="text-center">Cod. Interno</th>
                            <th  class="text-center">Unidad</th>
                            <th  class="text-center">Producto</th>
                            <th  class="text-center">Cantidad</th>
                        <tr>
                        <tr slot-scope="{ index, row }">
                            <td>{{ index }}</td> 
                            <td  class="text-left">{{row.series}}-{{row.number}}</td>
                            <td  class="text-center">{{row.item_internal_id}}</td>
                            <td  class="text-center">{{row.item_unit_type_id}}</td>
                            <td  class="text-center">{{row.item_description}}</td>
                            <td  class="text-center">{{row.item_quantity}}</td>
                        </tr>
                    </data-table>
                    
                </div> 
        </div>
 
    </div>
</template>

<script>
 
    import DataTable from '../../components/DataTableSalesConsolidated.vue'

    export default { 
        components: {DataTable},
        data() {
            return {
                resource: 'reports/sales-consolidated',                 
                form: {}, 

            }
        },
        async created() { 
        },
        methods: { 
             
            
        }
    }
</script>
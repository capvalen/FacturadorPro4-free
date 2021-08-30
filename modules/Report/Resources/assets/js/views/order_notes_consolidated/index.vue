<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <div class="card-header bg-info">
            <h3 class="my-0">Consolidado de items por cliente/vendedor</h3>
        </div>
        <div class="card mb-0">
                <div class="card-body">
                    <data-table :resource="resource">
                        <tr slot="heading">
                            <th class="">#</th>
                            <th  class="text-left">Vendedor</th>
                            <th  class="text-left">Cliente</th>
                            <th  class="text-left">Producto</th>
                            <th  class="text-center">Cantidad</th>
                            <th  class="text-center">Monto</th>
                        <tr>
                        <tr slot-scope="{ index, row }">
                            <td>{{ index }}</td> 
                            <td  class="text-left">{{row.user}}</td>
                            <td  class="text-left">{{row.customer}}</td>
                            <td  class="text-left">{{row.item_description}}</td>
                            <td  class="text-center">{{row.item_quantity}}</td>
                            <td  class="text-center">S/ {{row.total}}</td>
                        </tr>
                        
                    </data-table>
                     
                    
                </div> 
        </div>
 
    </div>
</template>

<script>
 
    import DataTable from '../../components/DataTableOrderNotesConsolidated.vue'

    export default { 
        components: {DataTable},
        data() {
            return {
                resource: 'reports/order-notes-consolidated',                 
                form: {}, 

            }
        },
        async created() { 
        },
        methods: { 
             
            
        }
    }
</script>
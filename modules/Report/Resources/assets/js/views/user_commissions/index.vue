<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <div class="card-header bg-info">
            <h3 class="my-0">Comisiones vendedores - utilidades 
                <el-tooltip class="item" effect="dark" content="Total ventas (CPE - NV) - Total compras" placement="top-end">
                    <i class="fa fa-info-circle"></i>
                </el-tooltip>
            </h3>
        </div>
        <div class="card mb-0">
                <div class="card-body">
                    <data-table :resource="resource">
                        <tr slot="heading">
                            <th>#</th>
                            <th>Vendedor</th>
                            <th class="text-center">Tipo comisión</th>
                            <th class="text-center">Monto comisión</th>
                            <th class="text-center">Total utilidad</th>
                            <th class="text-center">Total comisiones</th>
                        <tr>
                        <tr slot-scope="{ index, row }">
                            <td>{{ index }}</td>  
                            <td>{{row.user_name}}</td>
                            <td class="text-center">{{row.type}}</td>
                            <td class="text-center">{{row.amount}}</td> 
                            <td class="text-center">{{row.total_utility}}</td> 
                            <td class="text-center">{{row.commission}}</td> 
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
                resource: 'reports/user-commissions',                 
                form: {}, 

            }
        },
        async created() { 
        },
        methods: { 
             
            
        }
    }
</script>
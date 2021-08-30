<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <div class="card-header bg-info">
            <h3 class="my-0">R. General por cliente/vendedor</h3>
        </div>
        <div class="card mb-0">
                <div class="card-body">
                    <data-table :resource="resource">
                        <tr slot="heading">
                            <th class="">#</th>
                            <th  class="text-left">F. Emisión</th>
                            <th  class="text-left">F. Entrega</th>
                            <th  class="text-left">N° Pedido</th>
                            <th  class="text-left" v-if="columns.customer.visible">Cliente</th>
                            <th  class="text-left" v-if="columns.user.visible">Vendedor</th>
                            <th  class="text-center">Monto</th>
                            <th  class="text-center">Estado</th>
                        <tr>
                        <tr slot-scope="{ index, row }">
                            <td>{{ index }}</td> 
                            <td  class="text-left">{{row.date_of_issue}}</td>
                            <td  class="text-left">{{row.delivery_date}}</td>
                            <td  class="text-left">{{row.number_full}}</td>
                            <td v-if="columns.customer.visible">{{ row.customer_name }}<br/><small v-text="row.customer_number"></small></td>
                            <td  class="text-left" v-if="columns.user.visible">{{row.user_name}}</td>
                            <td  class="text-center">{{row.total}}</td>
                            <td  class="text-center">{{row.state_description}}</td>
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
                resource: 'reports/order-notes-general',                 
                form: {}, 
                columns: {
                    customer: {
                        visible: false
                    }, 
                    user: {
                        visible: false
                    }, 

                }

            }
        },
        async created() { 

            this.$eventHub.$on('changeFilterColumn', (type) => {
                this.changeVisibleColumn(type)
            })

        },
        methods: { 

            changeVisibleColumn(type){

                switch (type) {
                    case 'person':
                        this.columns.user.visible = true
                        this.columns.customer.visible = false
                        break;

                    case 'seller':
                        this.columns.customer.visible = true
                        this.columns.user.visible = false
                        break;
                }

            }
            
        }
    }
</script>
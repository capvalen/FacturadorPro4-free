<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <div class="card-header bg-info">
            <h3 class="my-0">Balance</h3>
        </div>
        <div class="card mb-0">
                <div class="card-body">
                    <data-table :resource="resource">
                        <tr slot="heading">
                            <th class="">#</th>
                            <th class=""><strong>Nombre de la cuenta / Total pagos</strong></th>
                            <th class="text-center"> <strong>CPE</strong></th>
                            <th class="text-center"> <strong>N. Venta</strong></th>
                            <th class="text-center"> <strong>Cotización</strong></th>
                            <th class="text-center"> <strong>Contrato</strong></th>
                            <th class="text-center"> <strong>S. Técnico</strong></th>
                            <th class="text-center"> <strong>Ingresos</strong></th>
                            <th class="text-center"> <strong>Compras</strong></th>
                            <th class="text-center"> <strong>Gastos</strong></th>
                            <th class="text-center" v-show="seller_can_view_balance"> <strong>Saldo</strong></th>
                        <tr>
                        <tr slot-scope="{ index, row }">
                            <td>{{ index }}</td>
                            <td>{{row.description}}</td>
                            <td class="text-center">S/ {{row.document_payment}}</td>
                            <td class="text-center">S/ {{row.sale_note_payment}}</td>
                            <td class="text-center">S/ {{row.quotation_payment}}</td>
                            <td class="text-center">S/ {{row.contract_payment}}</td>
                            <td class="text-center">S/ {{row.technical_service_payment}}</td>
                            <td class="text-center">S/ {{row.income_payment}}</td>
                            <td class="text-center">S/  {{row.purchase_payment}}</td>
                            <td class="text-center">S/ {{row.expense_payment}}</td>
                            <td class="text-center" v-show="seller_can_view_balance">S/ {{row.balance}}</td>
                        </tr>
                    </data-table>
                </div>
        </div>

    </div>
</template>

<script>

    import DataTable from '../../components/DataTableWithoutPaging.vue'

    export default {
        props:[
            'configuration',
            'user'
        ],
        components: {DataTable},
        data() {
            return {
                resource: 'finances/balance',
                form: {},
                seller_can_view_balance: false,
            }
        },
        created() {
            this.CanViewBalance()
        },
        methods: {
            CanViewBalance(){
                if(this.user.type === 'admin'){
                    this.seller_can_view_balance = true;
                }else {
                    this.seller_can_view_balance = this.configuration.seller_can_view_balance;
                }
                return this.seller_can_view_balance;
            }


        }
    }
</script>

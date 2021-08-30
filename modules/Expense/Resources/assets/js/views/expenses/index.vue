<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Gastos diversos</span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <a :href="`/${resource}/create`" class="btn btn-custom btn-sm  mt-2 mr-2"><i class="fa fa-plus-circle"></i> Nuevo</a>
            </div>
        </div>
        <div class="card mb-0">
            <div class="card-body">
                <data-table :resource="resource">
                    <tr slot="heading">
                        <th>#</th>
                        <th class="text-center">Fecha Emisión</th>
                        <th>Proveedor</th>
                        <th>Número</th>
                        <th>Motivo</th>
                        <th class="text-center">Pagos</th>
                        <th class="text-center">Moneda</th>
                        <th class="text-right">Total</th>
                        <th class="text-center">Dist. Gasto</th>
                    <tr>
                    <tr slot-scope="{ index, row }" :class="{'text-danger': (row.state_type_id === '11'), 'text-warning': (row.state_type_id === '13'), 'border-light': (row.state_type_id === '01'), 'border-left border-info': (row.state_type_id === '03'), 'border-left border-success': (row.state_type_id === '05'), 'border-left border-secondary': (row.state_type_id === '07'), 'border-left border-dark': (row.state_type_id === '09'), 'border-left border-danger': (row.state_type_id === '11'), 'border-left border-warning': (row.state_type_id === '13')}">
                        <td>{{ index }}</td>
                        <td class="text-center">{{ row.date_of_issue }}</td>
                        <td>{{ row.supplier_name }}<br/><small v-text="row.supplier_number"></small></td>
                        <td>{{ row.number }}<br/>
                            <small v-text="row.expense_type_description"></small><br/>
                        </td>
                        <td class="">{{ row.expense_reason_description }}</td>
                        <td class="text-center">
                            <button
                                type="button"
                                style="min-width: 41px"
                                class="btn waves-effect waves-light btn-xs btn-info m-1__2"
                                @click.prevent="clickExpensePayment(row.id)"
                            >Pagos</button>
                        </td>
                        <td class="text-center">{{ row.currency_type_id }}</td>
                        <td class="text-right">{{ row.total }}</td>

                        <td class="text-center">

                            <button type="button" v-if="row.state_type_id != '11'" style="min-width: 41px" class="btn waves-effect waves-light btn-xs btn-primary m-1__2"
                                    @click.prevent="clickCreate(row.id)">
                                    <i class="fa fa-pen"></i>
                            </button>

                            <button type="button" style="min-width: 41px" class="btn waves-effect waves-light btn-xs btn-info m-1__2"
                                    @click.prevent="clickPayment(row.id)">
                                    <i class="fa fa-search"></i>
                            </button>
                            <button type="button" style="min-width: 41px" class="btn waves-effect waves-light btn-xs btn-danger m-1__2"
                                    @click.prevent="clickVoided(row.id)"
                                    v-if="row.state_type_id === '05'">
                                    <i class="fa fa-trash"></i>
                            </button>
                        </td>

                    </tr>
                </data-table>
            </div>


            <document-payments :showDialog.sync="showDialogPayments"
                               :expenseId="recordId"></document-payments>
            <expense-voided :showDialog.sync="showDialogVoided"
                               :expenseId="recordId"></expense-voided>

            <expense-payments
                :showDialog.sync="showDialogExpensePayments"
                :expenseId="recordId"
                :external="true"
                ></expense-payments>
        </div>
    </div>

</template>

<script>

    import DataTable from '@components/DataTable.vue'
    import DocumentPayments from './partials/payments.vue'
    import ExpenseVoided from './partials/voided.vue'
    import ExpensePayments from '@viewsModuleExpense/expense_payments/payments.vue'

    export default {
        components: {DataTable, DocumentPayments, ExpenseVoided, ExpensePayments},
        data() {
            return {
                showDialogVoided: false,
                resource: 'expenses',
                showDialogPayments: false,
                showDialogExpensePayments: false,
                recordId: null,
                showDialogOptions: false
            }
        },
        created() {
        },
        methods: {
            clickCreate(id = '') {
                location.href = `/${this.resource}/create/${id}`
            },
            clickExpensePayment(recordId) {
                this.recordId = recordId;
                this.showDialogExpensePayments = true
            },
            clickVoided(recordId) {
                this.recordId = recordId;
                this.showDialogVoided = true;
            },
            clickDownload(download) {
                window.open(download, '_blank');
            },
            clickOptions(recordId = null) {
                this.recordId = recordId
                this.showDialogOptions = true
            },
            clickPayment(recordId) {
                this.recordId = recordId;
                this.showDialogPayments = true;
            },
        }
    }
</script>

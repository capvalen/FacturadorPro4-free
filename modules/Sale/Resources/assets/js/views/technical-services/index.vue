<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>{{ title }}</span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <button type="button" class="btn btn-custom btn-sm  mt-2 mr-2" @click.prevent="clickCreate()"><i
                    class="fa fa-plus-circle"></i> Nuevo
                </button>
            </div>
        </div>
        <div class="card mb-0">
            <div class="card-header bg-info">
                <h3 class="my-0">{{ title }}</h3>
            </div>
            <div class="card-body">
                <data-table :resource="resource">
                    <tr slot="heading">
                        <th>#</th>
                        <th>Cliente</th>
                        <th>Celular</th>
                        <th>Número</th>
                        <th>F. Emisión</th>
                        <th>N° Serie</th>
                        <th>Costo</th>
                        <!-- <th>Pago adelantado</th> -->
                        <th></th>
                        <th>Saldo</th>
                        <th class="text-center">Ver</th>
                        <th class="text-right">Acciones</th>
                    <tr>
                    <tr slot-scope="{ index, row }">
                        <td>{{ index }}</td>
                        <td>{{ row.customer_name }}<br/><small v-text="row.customer_number"></small></td>
                        <td class="text-center">{{ row.cellphone }}</td>
                        <td class="text-center">{{ row.id }}</td>
                        <td class="text-center">{{ row.date_of_issue }}</td>
                        <td class="text-center">{{ row.serial_number }}</td>
                        <td class="text-center">{{ row.cost }}</td>
                        <!-- <td class="text-center">{{ row.prepayment }}</td> -->
                        <td class="text-right">
                            <button
                                type="button"
                                style="min-width: 41px"
                                class="btn waves-effect waves-light btn-xs btn-info m-1__2"
                                @click.prevent="clickPayment(row.id)"
                            >Pagos
                            </button>
                        </td>

                        <td class="text-center">{{ row.balance }}</td>

                        <td class="text-center">
                            <button type="button" class="btn waves-effect waves-light btn-xs btn-info"
                                    @click.prevent="clickPrint(row.id)">PDF
                            </button>
                        </td>

                        <td class="text-right">
                            <button type="button"
                                    class="btn waves-effect waves-light btn-xs btn-info"
                                    @click.prevent="clickOptions(row.id)">
                                Generar comprobante
                            </button>
                            <button type="button" class="btn waves-effect waves-light btn-xs btn-info"
                                    @click.prevent="clickCreate(row.id)">Editar
                            </button>
                            <template v-if="typeUser === 'admin'">
                                <button type="button" class="btn waves-effect waves-light btn-xs btn-danger"
                                        @click.prevent="clickDelete(row.id)">Eliminar
                                </button>
                            </template>
                        </td>
                    </tr>
                </data-table>
            </div>

            <technical-service-options :showDialog.sync="showDialogOptions"
                                       :recordId="recordId"
                                       :showGenerate="true"
                                       :showClose="true"></technical-service-options>

            <technical-services-form :showDialog.sync="showDialog"
                                     :recordId="recordId"></technical-services-form>

            <technical-service-payments
                :showDialog.sync="showDialogPayments"
                :recordId="recordId"
                :external="true"
            ></technical-service-payments>

        </div>
    </div>
</template>

<script>

import TechnicalServicesForm from './form.vue'
import DataTable from '@components/DataTable.vue'
import {deletable} from '@mixins/deletable'
import TechnicalServicePayments from './partials/payments.vue'
import TechnicalServiceOptions from './partials/options'

export default {
    mixins: [deletable],
    props: ['typeUser'],
    components: {TechnicalServicesForm, DataTable, TechnicalServicePayments, TechnicalServiceOptions},
    data() {
        return {
            title: null,
            showDialog: false,
            showDialogOptions: false,
            resource: 'technical-services',
            recordId: null,
            showDialogPayments: false,
        }
    },
    created() {
        this.title = 'Servicios de soporte técnico'
    },
    methods: {
        clickPayment(recordId) {
            this.recordId = recordId;
            this.showDialogPayments = true
        },
        clickPrint(recordId) {
            window.open(`/${this.resource}/print/${recordId}/a4`, '_blank');
        },
        clickCreate(recordId = null) {
            this.recordId = recordId
            this.showDialog = true
        },
        clickDelete(id) {
            this.destroy(`/${this.resource}/${id}`).then(() =>
                this.$eventHub.$emit('reloadData')
            )
        },
        clickOptions(recordId = null) {
            this.recordId = recordId
            this.showDialogOptions = true
        },
    }
}
</script>

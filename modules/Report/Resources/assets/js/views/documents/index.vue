<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <div class="card-header bg-info">
            <h3 class="my-0">Consulta de Documentos</h3>
            <div class="data-table-visible-columns" style="top: 10px;">
                <el-dropdown :hide-on-click="false">
                    <el-button type="primary">
                        Mostrar/Ocultar columnas<i class="el-icon-arrow-down el-icon--right"></i>
                    </el-button>
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item v-for="(column, index) in columns" :key="index">
                            <el-checkbox v-model="column.visible">{{ column.title }}</el-checkbox>
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </div>
        </div>
        <div class="card mb-0">
                <div class="card-body">
                    <data-table :applyCustomer="true" :resource="resource">
                        <tr slot="heading">
                            <th class="">#</th>
                            <th class="">Usuario/Vendedor</th>
                            <th class="">Tipo Documento</th>
                            <th class="">Comprobante</th>
                            <th class="">Fecha emisión</th>
                            <th v-if="columns.guides.visible" class="text-right">Guia</th>
                            <th v-if="columns.options.visible" class="text-right">Opciones</th>
                            <th>Doc. Afectado</th>
                            <th>Cotización</th>
                            <th>Caso</th>
                            <th>Cliente</th>
                            <th>Estado</th>
                            <th>Moneda</th>
                            <th>Orden de compra</th>
                            <th>Total Exonerado</th>
                            <th>Total Inafecto</th>
                            <th>Total Gratuito</th>
                            <th>Total Gravado</th>

                            <th class="">Total IGV</th>
                            <th class="">Total</th>
                        <tr>
                        <tr slot-scope="{ index, row }">
                            <td>{{ index }}</td>
                            <td>{{ row.user_name }}</td>
                            <td>{{ row.document_type_description }}</td>
                            <td>{{ row.number }}</td>
                            <td>{{ row.date_of_issue }}</td>
                            <td v-if="columns.guides.visible" class="text-center">
                                <span v-for="(item, i) in row.guides" :key="i">
                                    {{ item.number }} <br>
                                </span>
                            </td>
                            <td v-if="columns.options.visible" class="text-center">
                                <button class="btn waves-effect waves-light btn-xs btn-info m-1__2" type="button"
                                        @click.prevent="clickOptions(row.id)">Opciones
                                </button>
                            </td>
                            <td>{{ row.affected_document }}</td>
                            <td>{{ row.quotation_number_full }}</td>
                            <td>{{ row.sale_opportunity_number_full }}</td>
                            <td>{{ row.customer_name }}<br/><small v-text="row.customer_number"></small></td>
                            <td>{{ row.state_type_description }}</td>

                            <td>{{ row.currency_type_id }}</td>


                            <td>{{ row.purchase_order }}</td>


                            <td>{{
                                    (row.document_type_id == '07') ? ((row.total_exonerated == 0) ? '0.00' : '-' + row.total_exonerated) : ((row.document_type_id != '07' && (row.state_type_id == '11' || row.state_type_id == '09')) ? '0.00' : row.total_exonerated)
                                }}
                            </td>

                            <td>{{
                                    (row.document_type_id == '07') ? ((row.total_unaffected == 0) ? '0.00' : '-' + row.total_unaffected) : ((row.document_type_id != '07' && (row.state_type_id == '11' || row.state_type_id == '09')) ? '0.00' : row.total_unaffected)
                                }}
                            </td>
                            <td>{{
                                    (row.document_type_id == '07') ? ((row.total_free == 0) ? '0.00' : '-' + row.total_free) : ((row.document_type_id != '07' && (row.state_type_id == '11' || row.state_type_id == '09')) ? '0.00' : row.total_free)
                                }}
                            </td>
                            <td>{{
                                    (row.document_type_id == '07') ? ((row.total_taxed == 0) ? '0.00' : '-' + row.total_taxed) : ((row.document_type_id != '07' && (row.state_type_id == '11' || row.state_type_id == '09')) ? '0.00' : row.total_taxed)
                                }}
                            </td>
                            <td>{{
                                    (row.document_type_id == '07') ? ((row.total_igv == 0) ? '0.00' : '-' + row.total_igv) : ((row.document_type_id != '07' && (row.state_type_id == '11' || row.state_type_id == '09')) ? '0.00' : row.total_igv)
                                }}
                            </td>
                            <td>{{
                                    (row.document_type_id == '07') ? ((row.total == 0) ? '0.00' : '-' + row.total) : ((row.document_type_id != '07' && (row.state_type_id == '11' || row.state_type_id == '09')) ? '0.00' : row.total)
                                }}
                            </td>


                            <!-- <td>{{ (row.document_type_id == '07') ? -row.total_unaffected : ((row.document_type_id!='07' && (row.state_type_id =='11'||row.state_type_id =='09')) ? '0.00':row.total_unaffected) }}</td>
                            <td>{{ (row.document_type_id == '07') ? -row.total_free : ((row.document_type_id!='07' && (row.state_type_id =='11'||row.state_type_id =='09')) ? '0.00':row.total_free) }}</td>
                            <td>{{ (row.document_type_id == '07') ? -row.total_taxed : ((row.document_type_id!='07' && (row.state_type_id =='11'||row.state_type_id =='09')) ? '0.00':row.total_taxed) }}</td>
                            <td>{{ (row.document_type_id == '07') ? -row.total_igv : ((row.document_type_id!='07' && (row.state_type_id =='11'||row.state_type_id =='09')) ? '0.00':row.total_igv) }}</td>
                            <td>{{ (row.document_type_id == '07') ? -row.total : ((row.document_type_id!='07' && (row.state_type_id =='11'||row.state_type_id =='09')) ? '0.00':row.total) }}</td>  -->

                        </tr>

                    </data-table>


                </div>
        </div>
        <document-options :showDialog.sync="showDialogOptions"
                          :recordId="recordId"
                          :showClose="true"
                          :configuration="configuration"
        ></document-options>
    </div>
</template>

<script>

    import DataTable from '../../components/DataTableReports.vue'
    import DocumentOptions from '../../../../../../../resources/js/views/tenant/documents/partials/options'

    export default {
        props: ['configuration'],
        components: {DataTable,DocumentOptions},
        data() {
            return {
                showDialogOptions: false,
                recordId: null,
                resource: 'reports/sales',
                form: {},
                columns: {
                    guides: {
                        title: 'Guias',
                        visible: false
                    },
                    options: {
                        title: 'Opciones',
                        visible: false
                    },
                }

            }
        },
        async created() {
        },
        methods: {
            clickOptions(recordId = null) {
                this.recordId = recordId
                this.showDialogOptions = true
            },

        }
    }
</script>

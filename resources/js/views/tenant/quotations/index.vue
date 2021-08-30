<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Cotizaciones {{soapCompany}}</span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <a :href="`/${resource}/create`" class="btn btn-custom btn-sm  mt-2 mr-2"><i class="fa fa-plus-circle"></i> Nuevo</a>
            </div>
        </div>
        <div class="card mb-0">
            <div class="data-table-visible-columns">
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
            <div class="card-body">
                <data-table :resource="resource">
                    <tr slot="heading">
                        <th>#</th>
                        <th class="text-center">Fecha Emisión</th>
                        <th class="text-center" v-if="columns.delivery_date.visible">T. Entrega</th>
                        <th>Vendedor</th>
                        <th>Cliente</th>
                        <th>Estado</th>
                        <th>Cotización</th>
                        <th>Comprobantes</th>
                        <th>Notas de venta</th>
                        <th>Oportunidad Venta</th>
                        <th v-if="columns.referential_information.visible">Inf.Referencial</th>
                        <th v-if="columns.contract.visible">Contrato</th>
                        <!-- <th>Estado</th> -->
                        <th class="text-center">Moneda</th>
                        <th class="text-center"></th>
                        <th class="text-right" v-if="columns.total_exportation.visible">T.Exportación</th>
                        <th class="text-right" v-if="columns.total_free.visible">T.Gratuito</th>
                        <th class="text-right" v-if="columns.total_unaffected.visible">T.Inafecta</th>
                        <th class="text-right" v-if="columns.total_exonerated.visible">T.Exonerado</th>
                        <th class="text-right">T.Gravado</th>
                        <th class="text-right">T.Igv</th>
                        <th class="text-right">Total</th>
                        <th class="text-center">PDF</th>
                        <th class="text-right">Acciones</th>
                    <tr>
                    <tr slot-scope="{ index, row }" :class="{ anulate_color : row.state_type_id == '11' }">
                        <td>{{ index }}</td>
                        <td class="text-center">{{ row.date_of_issue }}</td>
                        <td class="text-center" v-if="columns.delivery_date.visible">{{ row.delivery_date }}</td>
                        <td>{{ row.user_name }}</td>
                        <td>{{ row.customer_name }}<br/><small v-text="row.customer_number"></small></td>
                        <td>
                            <template v-if="row.state_type_id == '11'">
                                {{row.state_type_description}}
                            </template>
                            <template v-else>
                                <el-select v-model="row.state_type_id" @change="changeStateType(row)" style="width:120px !important">
                                    <el-option v-for="option in state_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                            </template>
                        </td>
                        <td>{{ row.identifier }}
                        </td>
                        <td>
                            <template v-for="(document,i) in row.documents">
                                <label :key="i" v-text="document.number_full" class="d-block"></label>
                            </template>
                        </td>
                        <td>
                            <template v-for="(sale_note,i) in row.sale_notes">
                                <!-- <label :key="i" v-text="sale_note.identifier" class="d-block"></label> -->
                                <label :key="i" v-text="sale_note.number_full" class="d-block"></label>
                            </template>
                        </td>
                        <td>
                            <!-- {{ row.sale_opportunity_number_full }} -->

                            <el-popover
                                placement="right"
                                v-if="row.sale_opportunity"
                                width="400"
                                trigger="click">

                                <div class="col-md-12 mt-4">
                                    <table>
                                        <tr>
                                            <td><strong>O. Venta: </strong></td>
                                            <td><strong>{{row.sale_opportunity_number_full}}</strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Detalle: </strong></td>
                                            <td><strong>{{row.sale_opportunity.detail}}</strong></td>
                                        </tr>
                                        <tr  class="mt-4 mb-4">
                                            <td><strong>F. Emisión:</strong></td>
                                            <td><strong>{{row.date_of_issue}}</strong></td>
                                        </tr>
                                    </table>
                                    <div class="table-responsive mt-4">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Descripción</th>
                                                    <th>Cantidad</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(row, index) in row.sale_opportunity.items" :key="index">
                                                    <td>{{index+1}}</td>
                                                    <td>{{row.item.description}}</td>
                                                    <td>{{row.quantity}}</td>
                                                    <td>{{row.total}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <el-button slot="reference"> <i class="fa fa-eye"></i></el-button>
                            </el-popover>
                        </td>
                        <!-- <td>{{ row.state_type_description }}</td> -->
                        <td v-if="columns.referential_information.visible">{{ row.referential_information }}</td>
                        <td v-if="columns.contract.visible">{{ row.contract_number_full }}</td>
                        <td class="text-center">{{ row.currency_type_id }}</td>

                        <td class="text-right">
                            <button type="button" class="btn waves-effect waves-light btn-xs btn-info"
                                    @click.prevent="clickPayment(row.id)">Pagos</button>
                        </td>

                        <td class="text-right"  v-if="columns.total_exportation.visible" >{{ row.total_exportation }}</td>
                        <td class="text-right" v-if="columns.total_free.visible">{{ row.total_free }}</td>
                        <td class="text-right" v-if="columns.total_unaffected.visible">{{ row.total_unaffected }}</td>
                        <td class="text-right" v-if="columns.total_exonerated.visible">{{ row.total_exonerated }}</td>
                        <td class="text-right">{{ row.total_taxed }}</td>
                        <td class="text-right">{{ row.total_igv }}</td>
                        <td class="text-right">{{ row.total }}</td>
                        <td class="text-right">

                            <button type="button" class="btn waves-effect waves-light btn-xs btn-info"
                                    @click.prevent="clickOptionsPdf(row.id)">PDF</button>
                        </td>

                        <td class="text-right">
                            <button v-if="row.btn_options"
                                    type="button"
                                    class="btn waves-effect waves-light btn-xs btn-info"
                                    @click.prevent="clickOptions(row.id)" >
                                Generar comprobante
                            </button>

                            <a v-if="row.documents.length == 0 && row.state_type_id != '11'" :href="`/${resource}/edit/${row.id}`" type="button" class="btn waves-effect waves-light btn-xs btn-info">Editar</a>
                            <button v-if="row.documents.length == 0 && row.state_type_id != '11'" type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickAnulate(row.id)">Anular</button>
                            <button @click="duplicate(row.id)"  type="button" class="btn waves-effect waves-light btn-xs btn-info">Duplicar</button>
                            <a :href="`/dispatches/create/${row.id}/q`" class="btn waves-effect waves-light btn-xs btn-warning m-1__2">Guía</a>

                            <template v-if="row.btn_generate_cnt && row.state_type_id != '11'">
                                <a  :href="`/contracts/generate-quotation/${row.id}`" class="btn waves-effect waves-light btn-xs btn-primary m-1__2">Generar contrato</a>
                            </template>
                            <template v-else>
                                <button  type="button" @click="clickPrintContract(row.external_id_contract)"  class="btn waves-effect waves-light btn-xs btn-primary m-1__2">Ver contrato</button>
                            </template>


                        </td>

                    </tr>
                </data-table>
            </div>


            <quotation-options :showDialog.sync="showDialogOptions"
                              :recordId="recordId"
                              :showGenerate="true"
                              :showClose="true"></quotation-options>

            <quotation-options-pdf :showDialog.sync="showDialogOptionsPdf"
                              :recordId="recordId"
                              :showClose="true"></quotation-options-pdf>


            <quotation-payments :showDialog.sync="showDialogPayments"
                                :recordId="recordId"></quotation-payments>
        </div>
    </div>
</template>
<style scoped>
    .anulate_color{
        color:red;
    }
</style>
<script>

    import QuotationOptions from './partials/options.vue'
    import QuotationOptionsPdf from './partials/options_pdf.vue'
    import DataTable from '../../../components/DataTableQuotation.vue'
    import {deletable} from '../../../mixins/deletable'
    import QuotationPayments from './partials/payments.vue'

    export default {
        props:['typeUser', 'soapCompany'],
        mixins: [deletable],
        components: {DataTable,QuotationOptions, QuotationOptionsPdf, QuotationPayments},
        data() {
            return {
                resource: 'quotations',
                recordId: null,
                showDialogPayments: false,
                showDialogOptions: false,
                showDialogOptionsPdf: false,
                state_types: [],
                columns: {
                    total_exportation: {
                        title: 'T.Exportación',
                        visible: false
                    },
                    total_unaffected: {
                        title: 'T.Inafecto',
                        visible: false
                    },
                    total_exonerated: {
                        title: 'T.Exonerado',
                        visible: false
                    },
                    total_free: {
                        title: 'T.Gratuito',
                        visible: false
                    },
                    contract: {
                        title: 'Contrato',
                        visible: false
                    },
                    delivery_date: {
                        title: 'T.Entrega',
                        visible: false
                    },
                    referential_information: {
                        title: 'Inf.Referencial',
                        visible: false,
                    }
                }
            }
        },
        async created() {
            await this.filter()
        },
        methods: {
            clickPrintContract(external_id){
                window.open(`/contracts/print/${external_id}/a4`, '_blank');
            } ,
            clickPayment(recordId) {
                this.recordId = recordId;
                this.showDialogPayments = true;
            },
            async changeStateType(row){

                await this.updateStateType(`/${this.resource}/state-type/${row.state_type_id}/${row.id}`).then(() =>
                    this.$eventHub.$emit('reloadData')
                )

            },
            filter(){
                this.$http.get(`/${this.resource}/filter`)
                            .then(response => {
                                this.state_types = response.data.state_types
                            })
            },
            clickEdit(id)
            {
                this.recordId = id
                this.showDialogFormEdit = true
            },
            clickOptions(recordId = null) {
                this.recordId = recordId
                this.showDialogOptions = true
            },
            clickOptionsPdf(recordId = null) {
                this.recordId = recordId
                this.showDialogOptionsPdf = true
            },
            clickAnulate(id)
            {
                this.anular(`/${this.resource}/anular/${id}`).then(() =>
                    this.$eventHub.$emit('reloadData')
                )
            },
            duplicate(id)
            {
                this.$http.post(`${this.resource}/duplicate`, {id})
                .then(response => {
                    if (response.data.success) {
                        this.$message.success('Se guardaron los cambios correctamente.')
                        this.$eventHub.$emit('reloadData')
                    } else {
                        this.$message.error('No se guardaron los cambios')
                    }
                })
                .catch(error => {

                })
                this.$eventHub.$emit('reloadData')
            }
        }
    }
</script>

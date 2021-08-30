<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Solicitar cotización </span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <a :href="`/${resource}/create`" class="btn btn-custom btn-sm  mt-2 mr-2"><i class="fa fa-plus-circle"></i> Nuevo</a>
            </div>
        </div>
        <div class="card mb-0">
            <div class="data-table-visible-columns"> 
            </div>
            <div class="card-body">
                <data-table :resource="resource">
                    <tr slot="heading">
                        <th>#</th>
                        <th class="text-center">Fecha Emisión</th>
                        <th>Estado</th>
                        <th>Documento</th> 
                        <th class="text-center">Descarga</th>
                        <th class="text-right">Acciones</th>
                    <tr>
                    <tr slot-scope="{ index, row }" >
                        <td>{{ index }}</td>
                        <td class="text-center">{{ row.date_of_issue }}</td>
                        <td>{{row.state_type_description}}</td>
                        <td>{{ row.identifier }} 
                        </td>  
                        <td class="text-center"> 

                            <button type="button" class="btn waves-effect waves-light btn-xs btn-info"
                                    @click.prevent="clickDownload(row.external_id)">PDF</button>
                        </td>
                        
                        <td class="text-right"> 
                                    
                            <button type="button" v-if="!row.has_purchase_orders" class="btn waves-effect waves-light btn-xs btn-success m-1__2"
                                    @click.prevent="clickGenerateOc(row.id)">Generar OC</button>

                            <button type="button" v-if="!row.has_purchase_orders" class="btn waves-effect waves-light btn-xs btn-custom m-1__2"
                                    @click.prevent="clickCreate(row.id)">Editar</button>

                            <button type="button" class="btn waves-effect waves-light btn-xs btn-info m-1__2"
                                    @click.prevent="clickOptions(row.id)">Opciones</button>
                        </td>

                    </tr>
                </data-table>
            </div>
 

            <purchase-quotation-options :showDialog.sync="showDialogOptions"
                              :recordId="recordId"
                              :showGenerate="true"
                              :showClose="true"></purchase-quotation-options>
 
        </div>
    </div>
</template>
<style scoped>
    .anulate_color{
        color:red;
    }
</style>
<script>
 
    import PurchaseQuotationOptions from './partials/options.vue'
    import DataTable from '../../../../../../../resources/js/components/DataTable.vue'
    // import {deletable} from '../../../mixins/deletable'

    export default { 
        // mixins: [deletable],
        components: {DataTable,PurchaseQuotationOptions},
        data() {
            return { 
                resource: 'purchase-quotations',
                recordId: null,
                showDialogOptions: false,
            }
        },
        created() {
        },
        methods: { 
            clickCreate(id = '') {
                location.href = `/${this.resource}/create/${id}`
            },
            clickGenerateOc(id = '') {
                location.href = `/purchase-orders/generate/${id}`
            },
            clickDownload(external_id) {
                window.open(`/${this.resource}/download/${external_id}`, '_blank');                
            },
            clickOptions(recordId = null) {
                this.recordId = recordId
                this.showDialogOptions = true
            },  
        }
    }
</script>

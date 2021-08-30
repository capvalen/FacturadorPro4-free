<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Devoluciones</span></li>
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
                        <th>Usuario</th>
                        <th>Devolución</th>
                        <th>Motivo</th>
                        <th>Observación</th> 
                        <th class="text-center">Descargas</th>
                    <tr>
                    <tr slot-scope="{ index, row }" >
                        <td>{{ index }}</td>
                        <td class="text-center">{{ row.date_of_issue }}</td>
                        <td>{{ row.user_name }}</td>
                        <td>{{ row.number_full }}</td> 
                        <td>{{ row.devolution_reason_description }}</td> 
                        <td>{{ row.observation }}</td>
                        <td class="text-center">
                            <button type="button" class="btn waves-effect waves-light btn-xs btn-info"
                                    @click.prevent="clickDownload(row.external_id)">PDF</button>
                        </td>
 

                    </tr>
                </data-table>
            </div>


            <!-- <quotation-options :showDialog.sync="showDialogOptions"
                              :recordId="recordId"
                              :showGenerate="true"
                              :showClose="true"></quotation-options> -->
 
        </div>
    </div>
</template>
<style scoped>
    .anulate_color{
        color:red;
    }
</style>
<script>

    // import QuotationOptions from './partials/options.vue'
    import DataTable from '@components/DataTable.vue'
    import {deletable} from '@mixins/deletable'

    export default {
        props:['typeUser'],
        mixins: [deletable],
        components: {DataTable},
        data() {
            return {
                resource: 'devolutions',
                recordId: null,
                showDialogOptions: false,
                showDialogOptionsPdf: false, 
            }
        },
        created() {
        },
        methods: { 
            clickDownload(external_id) {
                window.open(`${this.resource}/download/${external_id}/a4`, '_blank');
            },
        }
    }
</script>

<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Validador de documentos    
                    <el-tooltip class="item" effect="dark" content="Los comprobantes se deben haber generado en entorno producción" placement="top-start">
                        <i class="fa fa-info-circle"></i>
                    </el-tooltip>    
                </span> </li>
            </ol> 
        </div>
        <div class="card mb-0" v-loading="loading_submit" :element-loading-text="loading_text" > 
            <div class="card-body ">
                <data-table :resource="resource">
                    <tr slot="heading">
                        <th>#</th>
                        <th>Entorno</th>
                        <th>Comprobante</th>
                        <th class="text-center">F. Emisión</th>
                        <th>Cliente</th>
                        <th  class="text-center">Código</th>
                        <th >Estado sistema</th>
                        <th >Estado Sunat</th>
                    <tr>
                    <tr slot-scope="{ index, row }">
                        <td>{{ index }}</td>
                        <td>{{ row.soap_type_description }}</td>
                        <td>{{ row.number }}<br/>
                            <small v-text="row.document_type_description"></small><br/>
                            <small v-if="row.affected_document" v-text="row.affected_document"></small>                            
                        </td>  
                        <td class="text-center">{{ row.date_of_issue }}</td>
                        <td>{{ row.customer_name }}<br/><small v-text="row.customer_number"></small></td>
                        <td class="text-center">{{ row.code }}</td>
                        
                        <td>
                            <span class="badge bg-secondary text-white" :class="{'bg-danger': (row.state_type_id === '11'), 'bg-warning': (row.state_type_id === '13'), 'bg-secondary': (row.state_type_id === '01'), 'bg-info': (row.state_type_id === '03'), 'bg-success': (row.state_type_id === '05'), 'bg-secondary': (row.state_type_id === '07'), 'bg-dark': (row.state_type_id === '09')}">{{row.state_type_description}}</span>
                        </td>
                        
                        <td>
                            <span class="badge bg-secondary text-white" :class="{'bg-danger': (row.state_type_sunat_description == 'ANULADO'), 
                            'bg-warning': (row.state_type_sunat_description == 'POR ANULAR'), 
                            'bg-secondary': (row.state_type_sunat_description == 'REGISTRADO'), 
                            'bg-info': (row.state_type_sunat_description == 'ENVIADO'), 
                            'bg-success': (row.state_type_sunat_description == 'ACEPTADO'), 
                            'bg-secondary': (row.state_type_sunat_description == 'OBSERVADO'), 
                            'bg-dark': (row.state_type_sunat_description == 'RECHAZADO'),
                            'bg-info': (row.state_type_sunat_description == 'NO EXISTE')}">{{row.state_type_sunat_description}}</span>
                        </td>
                        <!-- <td >{{ row.state_type_sunat_description }}</td> -->
                    </tr>
                </data-table>
            </div>
 
        </div>
    </div>
</template>

<script>
 
    import DataTable from '../../../../../../../resources/js/components/DataTableValidateDocuments.vue'

    export default {
        components: {DataTable},
        data() {
            return {
                showDialogVoided: false,
                showImportDialog: false,
                resource: 'reports/validate-documents',
                recordId: null,
                showDialogOptions: false,
                showDialogPayments: false, 
                loading_submit: false,
                loading_text: 'Validando documentos electrónicos...',

            }
        },
        created() {
            this.events()
        },
        methods: { 
            events(){

                this.$eventHub.$on('valueLoading', (loading) => {
                    this.loading_text = 'Validando documentos electrónicos...'
                    this.loading_submit = loading
                })

                this.$eventHub.$on('valueLoadingRegularize', (loading) => {
                    this.loading_text = 'Regularizando documentos electrónicos...'
                    this.loading_submit = loading
                })

            } 
        }
    }
</script>

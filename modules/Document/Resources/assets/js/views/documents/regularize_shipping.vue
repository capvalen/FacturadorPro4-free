<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Comprobantes pendientes de rectificación</span> </li>
            </ol>
        </div>
        <div class="card mb-0" v-loading="loading_submit">
            <div class="card-body ">
                <data-table :resource="resource">
                    <tr slot="heading">
                        <th>#</th>
                        <th>Entorno</th>
                        <th class="text-center">Usuario</th>
                        <th class="text-center">F. Emisión</th>
                        <th>Cliente</th>
                        <th>Comprobante</th>
                        <th>Descripción</th>
                        <th class="text-center">Consultar CDR</th>
                        <th class="text-center">Enviar</th>
                    <tr>
                    <tr slot-scope="{ index, row }">
                        <td>{{ index }}</td>
                        <td>{{ row.soap_type_description }}</td>
                        <td class="text-center">{{ row.user_name }}</td>
                        <td class="text-center">{{ row.date_of_issue }}</td>
                        <td>{{ row.customer_name }}<br/><small v-text="row.customer_number"></small></td>
                        <td>{{ row.number }}<br/>
                            <small v-text="row.document_type_description"></small><br/>
                            <small v-if="row.affected_document" v-text="row.affected_document"></small>
                        </td>
                        <th>
                            {{row.message_regularize_shipping}}
                        </th>

                        <td class="text-center">
                            <template v-if="row.soap_type_id == '02' && row.group_id == '01'">
                                <el-button type="success"  class="btn btn-sm" @click.prevent="clickConsultCdr(row.id)" v-if="!isClient"><i class="el-icon-check"></i></el-button>
                            </template>
                        </td>

                        <td class="text-center">
                            <template v-if="row.btn_resend">
                                <el-button type="primary"  class="btn btn-sm"
                                    @click.prevent="clickResend(row.id)"
                                        v-if="!isClient"  ><i class="el-icon-upload2"></i></el-button>
                                <!--
                                || row.btn_remove
                                <el-button type="primary"  class="btn btn-sm btn-danger"
                                           v-show="row.btn_remove"
                                           @click.prevent="clickRemove(row.id)"
                                           v-if="!isClient"  >
                                    <i class="el-icon-delete"></i>
                                </el-button>
                                -->
                            </template>
                            <template v-else>
                                <el-tooltip class="item" effect="dark" :content="row.text_tooltip" placement="top">
                                    <el-button type="info"  class="btn btn-sm" ><i class="el-icon-upload2"></i></el-button>
                                </el-tooltip>
                            </template>


                        </td>
                    </tr>
                </data-table>
            </div>

        </div>
    </div>
</template>

<script>

    import DataTable from '@components/DataTableDocuments.vue'

    export default {
        props: ['isClient'],
        components: {DataTable},
        data() {
            return {
                showDialogVoided: false,
                showImportDialog: false,
                resource: 'documents/regularize-shipping',
                recordId: null,
                showDialogOptions: false,
                showDialogPayments: false,
                loading_submit: false,

            }
        },
        created() {
        },
        methods: {
            clickConsultCdr(document_id) {
                this.loading_submit = true
                this.$http.get(`/documents/consult_cdr/${document_id}`)
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success(response.data.message)
                            this.$eventHub.$emit('reloadData')
                        } else {
                            this.$message.error(response.data.message)
                        }
                    })
                    .catch(error => {
                        this.$message.error(error.response.data.message)
                    }).then(()=>{
                        this.loading_submit = false
                    })
            },
            clickResend(document_id) {
                this.loading_submit = true
                this.$http.get(`/documents/send/${document_id}`)
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success(response.data.message)
                            this.$eventHub.$emit('reloadData')
                            // location.reload()
                        } else {
                            this.$message.error(response.data.message)
                        }
                    })
                    .catch(error => {
                        this.$message.error(error.response.data.message)
                    }).then(()=>{
                        this.loading_submit = false
                    })
            },
            /*
            clickRemove(document_id) {
                this.loading_submit = true
                this.$http.get(`/documents/remove/${document_id}`)
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success(response.data.message)
                            this.$eventHub.$emit('reloadData')
                            // location.reload()
                        } else {
                            this.$message.error(response.data.message)
                        }
                    })
                    .catch(error => {
                        this.$message.error(error.response.data.message)
                    }).then(()=>{
                        this.loading_submit = false
                    })
            },
            */
            clickOptions(recordId = null) {
                this.recordId = recordId
                this.showDialogOptions = true
            }
        }
    }
</script>

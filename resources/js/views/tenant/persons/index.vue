<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>{{ title }}</span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <button type="button" class="btn btn-custom btn-sm  mt-2 mr-2" @click.prevent="clickExport()"><i class="fa fa-download"></i> Exportar</button>
                <button type="button" class="btn btn-custom btn-sm  mt-2 mr-2" @click.prevent="clickImport()"><i class="fa fa-upload"></i> Importar</button>
                <button type="button" class="btn btn-custom btn-sm  mt-2 mr-2" @click.prevent="clickCreate()"><i class="fa fa-plus-circle"></i> Nuevo</button>
            </div>
        </div>
        <div class="card mb-0">
            <div class="card-header bg-info">
                <h3 class="my-0">Listado de {{ title }}</h3>
            </div>
            <div class="card-body">
                <data-table :resource="resource+`/${this.type}`">
                    <tr slot="heading">
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Cód interno</th>
                        <th class="text-right">Tipo de documento</th>
                        <th class="text-right">Número</th>
                        <th class="text-right">Acciones</th>
                    <tr>
                    <tr slot-scope="{ index, row }" :class="{ disable_color : !row.enabled}">
                        <td>{{ index }}</td>
                        <td>{{ row.name }}</td>
                        <td>{{ row.internal_code }}</td>
                        <td class="text-right">{{ row.document_type }}</td>
                        <td class="text-right">{{ row.number }}</td>
                        <td class="text-right">

                            <template v-if="row.enabled">
                                <button type="button" class="btn waves-effect waves-light btn-xs btn-info" @click.prevent="clickCreate(row.id)" >Editar</button>
                            </template>

                            <template v-if="typeUser === 'admin'">
                                <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickDelete(row.id)">Eliminar</button>

                                <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickDisable(row.id)" v-if="row.enabled">Inhabilitar</button>
                                <button type="button" class="btn waves-effect waves-light btn-xs btn-primary" @click.prevent="clickEnable(row.id)" v-else>Habilitar</button>

                            </template>
                        </td>
                    </tr>
                </data-table>
            </div>

            <persons-form :showDialog.sync="showDialog"
                          :type="type"
                          :recordId="recordId"
                          :api_service_token="api_service_token"></persons-form>

            <persons-import :showDialog.sync="showImportDialog"
                            :type="type"></persons-import>

            <persons-export :showDialog.sync="showExportDialog"
                            :type="type"></persons-export>

        </div>
    </div>
</template>

<script>

    import PersonsForm from './form.vue'
    import PersonsImport from './import.vue'
    import PersonsExport from './partials/export.vue'
    import DataTable from '../../../components/DataTable.vue'
    import {deletable} from '../../../mixins/deletable'

    export default {
        mixins: [deletable],
        props: ['type', 'typeUser','api_service_token'],
        components: {PersonsForm, PersonsImport, PersonsExport, DataTable},
        data() {
            return {
                title: null,
                showDialog: false,
                showImportDialog: false,
                showExportDialog: false,
                resource: 'persons',
                recordId: null,
            }
        },
        created() {
            this.title = (this.type === 'customers')?'Clientes':'Proveedores'
        },
        methods: {
            clickCreate(recordId = null) {
                this.recordId = recordId
                this.showDialog = true
            },
            clickImport() {
                this.showImportDialog = true
            },
            clickExport() {
                this.showExportDialog = true
            },
            clickDelete(id) {
                this.destroy(`/${this.resource}/${id}`).then(() =>
                    this.$eventHub.$emit('reloadData')
                )
            },
            clickDisable(id){
                this.disable(`/${this.resource}/enabled/${0}/${id}`).then(() =>
                    this.$eventHub.$emit('reloadData')
                )
            },
            clickEnable(id){
                this.enable(`/${this.resource}/enabled/${1}/${id}`).then(() =>
                    this.$eventHub.$emit('reloadData')
                )
            },
        }
    }
</script>

<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>{{ title }}</span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <!--<button type="button" class="btn btn-custom btn-sm  mt-2 mr-2" @click.prevent="clickCreate()"><i class="fa fa-plus-circle"></i> Nuevo</button>-->
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
                        <th>Descripción</th>
                        <th>Establecimiento</th>
                        <th class="text-right">Acciones</th>
                    <tr>
                    <tr slot-scope="{ index, row }">
                        <td>{{ index }}</td>
                        <td>{{ row.description }}</td>
                        <td>{{ row.establishment_description }}</td>
                        <td class="text-right">
                            <button type="button" class="btn waves-effect waves-light btn-xs btn-info" @click.prevent="clickCreate(row.id)">Editar</button>
                        </td>
                    </tr>
                </data-table>
            </div>

            <warehouses-form :showDialog.sync="showDialog"
                             :recordId="recordId"></warehouses-form>
        </div>
    </div>
</template>

<script>

    import WarehousesForm from './form.vue'
    import DataTable from '../../../../../../resources/js/components/DataTable.vue'

    export default {
        props: ['type'],
        components: {DataTable, WarehousesForm},
        data() {
            return {
                title: null,
                showDialog: false,
                resource: 'warehouses',
                recordId: null,
            }
        },
        created() {
            this.title = 'Listado de almacenes'
        },
        methods: {
            clickCreate(recordId) {
                this.recordId = recordId
                this.showDialog = true
            }
        }
    }
</script>

<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>{{ title }}</span></li>
            </ol>
            <div class="right-wrapper pull-right">
            </div>
        </div>
        <div class="card mb-0">
            <div class="card-header bg-info">
                <h3 class="my-0">Listado de {{ title }}</h3>
            </div>
            <div class="card-body">

                <data-table :resource="resource">
                    <tr slot="heading">
                        <th>#</th>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th >Cantidad</th>
                        <th>Almacen Inicial</th>
                        <th>Almacen Destino</th>
                        <th class="text-right">Acciones</th>
                    <tr>
                    <tr slot-scope="{ index, row }">
                        <td>{{ index }}</td>
                        <td>{{ row.created_at }}</td>
                        <td>{{ row.item_description }}</td>
                        <td >{{ row.quantity }}</td>
                        <td>{{ row.warehouse }}</td>
                        <td>{{ row.warehouse_destination }}</td>
                        <td class="text-right">
                            <button type="button" class="btn waves-effect waves-light btn-xs btn-warning"
                                    @click.prevent="clickRemove(row.id)">Remover</button>
                        </td>
                    </tr>
                </data-table>

            </div>


        </div>
    </div>
</template>

<script>


    import DataTable from '../../../../../../resources/js/components/DataTable.vue'

    export default {
          components: {DataTable},
        data() {
            return {
                title: null,
                showDialog: false,
                showDialogMove: false,
                showDialogRemove: false,
                resource: 'moves',
                recordId: null,
                typeTransaction:null,
            }
        },
        created() {
            this.title = 'Traslados'
        },
        methods: {
            clickRemove()
            {

            }
        }
    }
</script>

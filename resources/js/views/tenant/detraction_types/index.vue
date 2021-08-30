<template>
    <div class="card">
        <div class="card-header bg-info">
            <h3 class="my-0">Listado de tipos de detracciones</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <button type="button" class="btn btn-custom btn-sm  mt-2 mr-2" @click.prevent="clickCreate()"><i class="fa fa-plus-circle"></i> Nuevo</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>T. Operación</th>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Porcentaje</th>
                        <th class="text-center">Activo</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(row, index) in records" :key="index">
                        <td>{{ index + 1 }}</td>
                        <td>{{ row.operation_type_id }}</td>
                        <td>{{ row.id }}</td>
                        <td>{{ row.description }}</td>
                        <td>{{ row.percentage }}</td>
                        <td class="text-center">{{ row.active }}</td>
                        <td class="text-right">
                            <button type="button" class="btn waves-effect waves-light btn-xs btn-info" @click.prevent="clickCreate(row.id)">Editar</button>

                              <template v-if="typeUser === 'admin'">
                                 <button type="button" class="btn waves-effect waves-light btn-xs btn-danger"  @click.prevent="clickDelete(row.id)">Eliminar</button>
                              </template>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <detraction-types-form :showDialog.sync="showDialog"
                         :recordId="recordId"></detraction-types-form>
    </div>
</template>

<script>

    import DetractionTypesForm from './form.vue'
    import {deletable} from '../../../mixins/deletable'

    export default {
        mixins: [deletable],
        props: ['typeUser'],
        components: {DetractionTypesForm},
        data() {
            return {
                showDialog: false,
                resource: 'detraction_types',
                recordId: null,
                records: [],
            }
        },
        created() {
            this.$eventHub.$on('reloadData', () => {
                this.getData()
            })
            this.getData()
        },
        methods: {
            getData() {
                this.$http.get(`/${this.resource}/records`)
                    .then(response => {
                        this.records = response.data.data
                    })
            },
            clickCreate(recordId = null) {
                this.recordId = recordId
                this.showDialog = true
            },
            clickDelete(id) {
                this.destroy(`/${this.resource}/${id}`).then(() =>
                    this.$eventHub.$emit('reloadData')
                )
            }
        }
    }
</script>

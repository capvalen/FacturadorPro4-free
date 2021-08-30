<template>
    <div class="">
        <div class="card-header bg-info">
            <h3 class="my-0">Numeración de facturación
                <el-tooltip class="item" effect="dark" content="La serie iniciará desde un número correlativo personalizado" placement="top-start">
                    <i class="fa fa-info-circle"></i>
                </el-tooltip>    
            </h3> 
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
                        <th>Tipo comprobante</th>
                        <th>Serie</th>
                        <th class="text-center">Número a iniciar</th>
                        <th class="text-center">Emisión inicializada</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(row, index) in records">
                        <td>{{ index + 1 }}</td>
                        <td>{{ row.document_type_description }}</td>
                        <td>{{ row.series }}</td>
                        <td class="text-center">{{ row.number }}</td>
                        <td class="text-center">{{ row.initialized_description }}</td>
                        <td class="text-right" >
                            <!-- <button type="button" class="btn waves-effect waves-light btn-xs btn-info" @click.prevent="clickCreate(row.id)">Editar</button> -->
                            <template v-if="row.btn_delete">
                                <button type="button" class="btn waves-effect waves-light btn-xs btn-danger"  @click.prevent="clickDelete(row.id)">Eliminar</button>
                            </template>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div> 
        </div>
        <series-configurations-form :showDialog.sync="showDialog"
                         :recordId="recordId"></series-configurations-form>
    </div>
</template>

<script>

    import SeriesConfigurationsForm from './form.vue'
    import {deletable} from '../../../../../../../resources/js/mixins/deletable'

    export default {
        mixins: [deletable],
        components: {SeriesConfigurationsForm},
        data() {
            return {
                showDialog: false,
                resource: 'series-configurations',
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
                        this.records = response.data
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

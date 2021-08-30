<template>
    <div class="card">
        <div class="card-header bg-info">
            <h3 class="my-0">{{ title }}</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Descripción</th>
                        <th>Nivel</th>
                        <th class="text-right">Porcentaje</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(row, index) in records">
                        <td>{{ index + 1 }}</td>
                        <td>{{ row.description }}</td>
                        <td>{{ row.level }}</td>
                        <td class="text-right">{{ row.percentage }}</td>
                        <td class="text-right">
                            <button type="button" class="btn waves-effect waves-light btn-xs btn-info" @click.prevent="clickCreate(row.id)">Editar</button>
                            <button type="button" class="btn waves-effect waves-light btn-xs btn-danger"  @click.prevent="clickDelete(row.id)">Eliminar</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="col">
                    <button type="button" class="btn btn-custom btn-sm  mt-2 mr-2" @click.prevent="clickCreate()"><i class="fa fa-plus-circle"></i> Nuevo</button>
                </div>
            </div>
        </div>
        <charge-discounts-form :showDialog.sync="showDialog"
                               :type="type"
                               :recordId="recordId"></charge-discounts-form>
    </div>
</template>

<script>

    import ChargeDiscountsForm from './form.vue'
    import {deletable} from '../../../mixins/deletable'

    export default {
        props: {type: String},
        mixins: [deletable],
        components: {ChargeDiscountsForm},
        data() {
            return {
                showDialog: false,
                resource: 'charge_discounts',
                title: null,
                recordId: null,
                records: [],
            }
        },
        created() {
            this.$eventHub.$on('reloadData', () => {
                this.getData()
            })
            this.title = (this.type === 'charge')?'Listado de cargos':'Listado de descuentos'
            this.getData()
        },
        methods: {
            getData() {
                this.$http.get(`/${this.resource}/records/${this.type}`)
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

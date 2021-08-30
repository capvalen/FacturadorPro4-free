<template>
    <div class="card">
        <div class="card-header bg-info">
            <h3 class="my-0">Clientes</h3>
        </div>
        <div class="card-body">  
            <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Dominio</th>
                        <th>Nombre</th>
                        <th>RUC</th>
                        <th>Correo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(row, index) in records" :key="index">
                        <td>{{ index + 1 }}</td>
                        <td>{{ row.hostname }}</td>
                        <td>{{ row.name }}</td>
                        <td>{{ row.number }}</td>
                        <td>{{ row.email }}</td>
                        <td>
                            <button type="button" class="btn waves-effect waves-light btn-xs btn-primary"  @click.prevent="clickCreate(row.id)"> Exportar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>

        <accounting-form :showDialog.sync="showDialog"
                           :recordId="recordId"></accounting-form>
    </div>
</template>

<script>

    import AccountingForm from './form.vue'

    export default {
        components: {AccountingForm},
        data() {
            return {
                showDialog: false,
                resource: 'accounting',
                recordId: null,
                record: {},
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
            clickCreate(recordId) {
                this.recordId = recordId
                this.showDialog = true
            }
        }
    }
</script>

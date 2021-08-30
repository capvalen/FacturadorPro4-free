<template>
    <div class="card">
        <div class="card-header bg-info">
            <h3 class="my-0">Listado de tipos de cambio</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Día</th>
                        <th>Compra</th>
                        <th>Venta</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(row, index) in records">
                        <td>{{ index + 1 }}</td>
                        <td>{{ row.date }}</td>
                        <td>{{ row.buy }}</td>
                        <td>{{ row.sell }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="col">
                    <el-button type="primary" @click.prevent="clickGet" :loading="loading_search_exchange_rate">Obtener</el-button>
                    <el-button type="primary" @click.prevent="clickCreate" >Obtener uno</el-button>
                </div>
            </div>
        </div>
        <exchange-rates-form :showDialog.sync="showDialog"></exchange-rates-form>
    </div>
</template>

<script>

    import ExchangeRatesForm from './form.vue'
    import {functions} from '../../../mixins/functions'

    export default {
        mixins: [functions],
        components: {ExchangeRatesForm},
        data() {
            return {
                showDialog: false,
                resource: 'exchange_rates',
                records: [],
                data: null,
                form: {},
            }
        },
        created() {
            this.initForm()
            this.getData()
            this.$eventHub.$on('reloadData', () => {
                this.getData()
            })
        },
        methods: {
            initForm() { 
                this.form = {
                    cur_date: moment().format('YYYY-MM-DD'),
                    last_date: null,
                }
            },
            getData() {
                this.$http.get(`/${this.resource}/records`)
                    .then(response => {
                        this.records = response.data.data
                        if (this.records.length) {
                            this.form.last_date = this.records[0].date
                        }
                    })
            },
            clickCreate() {
                this.showDialog = true
            },
            clickGet() {
                this.searchExchangeRate().then(() => {
                    this.getData()
                })
            }
        }
    }
</script>

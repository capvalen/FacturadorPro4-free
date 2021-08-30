<template>
    <el-dialog title="Obtener tipo de cambio" :visible="showDialog" @close="close">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="form-group" :class="{'has-danger': errors.date}">
                            <label class="control-label">Fecha</label>
                            <el-date-picker v-model="form.cur_date" type="date" value-format="yyyy-MM-dd" :clearable="false"></el-date-picker>
                            <small class="form-control-feedback" v-if="errors.date" v-text="errors.date[0]"></small>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label class="control-label">Compra</label>
                            <el-input v-model="form.buy" :disabled="true"></el-input>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label class="control-label">Venta</label>
                            <el-input v-model="form.sell" :disabled="true"></el-input>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions text-right mt-4">
                <el-button @click.prevent="close()">Cancelar</el-button>
                <el-button type="primary" native-type="submit" :loading="loading_search_exchange_rate">Obtener</el-button>
            </div>
        </form>
    </el-dialog>

</template>

<script>

    import {functions} from '../../../mixins/functions'

    export default {
        mixins: [functions],
        props: ['showDialog'],
        data() {
            return {
                resource: 'exchange_rates',
                errors: {},
                form: {},
                data: null,
            }
        },
        created() {
            this.initForm()
        },
        methods: {
            initForm() {
                this.errors = {}
                this.form = {
                    cur_date: moment().format('YYYY-MM-DD'),
                    last_date: null,
                    buy: null,
                    sell: null,
                }
            },
            submit() {
                this.searchExchangeRate().then(() => {
                    this.$eventHub.$emit('reloadData')
                })
            },
            close() {
                this.$emit('update:showDialog', false)
                this.initForm()
            },
        }
    }
</script>

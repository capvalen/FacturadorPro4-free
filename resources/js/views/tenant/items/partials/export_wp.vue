<template>
    <el-dialog title="Exportar Productos" :visible="showDialog" @close="close" class="dialog-import">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-6">
                        <template>
                            <label class="control-label">Mes</label>
                            <el-date-picker v-model="form.month_start" type="month" value-format="yyyy-MM" format="MM/yyyy" :clearable="false"></el-date-picker>
                        </template>
                    </div>
                </div>
                <div class="form-actions text-right mt-4">
                    <el-button @click.prevent="close()">Cancelar</el-button>
                    <el-button type="primary" native-type="submit" :loading="loading_submit">Procesar</el-button>
                </div>
            </div>
        </form>
    </el-dialog>
</template>

<script>
    import queryString from 'query-string'

    export default {
        props: ['showDialog'],
        data() {
            return {
                loading_submit: false,
                headers: headers_token,
                resource: 'items',
                errors: {},
                form: {},
                pickerOptionsMonths: {
                    disabledDate: (time) => {
                        time = moment(time).format('YYYY-MM')
                        return this.form.month_start > time
                    }
                },
            }
        },
        created() {
            this.initForm()
        },
        methods: {
            initForm() {
                this.errors = {}
                this.form = {
                    month_start: moment().format('YYYY-MM'),
                }
            },
            close() {
                this.$emit('update:showDialog', false)
                this.initForm()
            },
            submit() {
                this.loading_submit = true

                let query = queryString.stringify({
                    ...this.form
                });
                window.open(`/${this.resource}/export/wp/?${query}`, '_blank');

                this.loading_submit = false
                this.$emit('update:showDialog', false)
                this.initForm()
            }
        }
    }
</script>

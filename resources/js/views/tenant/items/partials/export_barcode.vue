<template>
    <el-dialog title="Codigo de barras" :visible="showDialog" @close="close" class="dialog-import">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-12">
                        <template>
                            <label class="control-label">
                                Seleccione rango de impresión
                                <el-tooltip class="item" effect="dark" content="Generar una cantidad muy alta puede causar lentitud en el servidor" placement="top-start">
                                    <i class="fa fa-info-circle"></i>
                                </el-tooltip>
                            </label>
                            <el-slider
                              v-model="form.range"
                              range
                              :min="1"
                              :max="max_item.data">
                            </el-slider>
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
                form: {
                    range: [1, 100]
                },
                max_item: 1
            }
        },
        created() {
            this.initForm()
        },
        methods: {
            initForm() {
                this.lastItem()
                this.errors = {}
            },
            close() {
                this.$emit('update:showDialog', false)
                this.initForm()
            },
            submit() {
                this.loading_submit = true

                let query = queryString.stringify({
                    ...this.form.range
                });
                window.open(`/${this.resource}/export/barcode/?${query}`, '_blank');

                this.loading_submit = false
                this.$emit('update:showDialog', false)
                this.initForm()
            },
            lastItem() {
                this.$http.get(`${this.resource}/export/barcode/last`)
                .then(response => {
                    this.max_item = response.data
                })
            }
        }
    }
</script>

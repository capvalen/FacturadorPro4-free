<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @close="close" @open="create" class="certificate-form">
        <div class="form-body">
            <div class="row">
                <div class="col-md-6">
                    <label>Periodo</label>
                    <el-date-picker v-model="form.month" type="month"
                                    value-format="yyyy-MM" format="MM/yyyy" :clearable="false"></el-date-picker>
                </div>
                <div class="col-md-6">
                    <label>Exportar a</label>
                    <el-select v-model="form.type">
                        <el-option key="concar" value="concar" label="CONCAR"></el-option>
                        <el-option key="siscont" value="siscont" label="SISCONT"></el-option>
                        <el-option key="foxcont" value="foxcont" label="FOXCONT"></el-option>
                        <el-option key="contasis" value="contasis" label="CONTASIS"></el-option>
                    </el-select>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-12 text-right">
                    <el-button @click.prevent="close()">Cancelar</el-button>
                    <el-button type="primary" @click.prevent="clickDownload" :loading="loading_submit">Generar</el-button>
                </div>
            </div>
        </div>
    </el-dialog>
</template>

<script>

    import queryString from 'query-string'

    export default {
        props: ['showDialog', 'recordId'],
        data() {
            return {
                loading_submit: false,
                titleDialog: null,
                resource: 'accounting',
                errors: {},
                form: {}
            }
        },
        created() {
            this.initForm()
        },
        methods: { 
            create() {
                this.titleDialog = 'Exportar formatos contables'
                this.form.id = this.recordId
            },
            initForm() {
                this.errors = {}
                this.form = {
                    id: null,
                    month: moment().format('YYYY-MM'),
                    type: 'concar'
                }
            },
            clickDownload() {
                this.loading_submit = true;
                let query = queryString.stringify({
                    ...this.form
                });
                window.open(`/${this.resource}/download?${query}`, '_blank');
                this.loading_submit = false;
            },
            close() {
                this.$emit('update:showDialog', false)
                this.initForm()
            },
        }
    }
</script>

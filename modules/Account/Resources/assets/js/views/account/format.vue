<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>{{ title }}</span></li>
            </ol>
        </div>

        <div class="card" v-loading="loading">
            <div class="card-header bg-info">
                <h3 class="my-0">{{ title }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label>Periodo</label>
                        <el-date-picker v-model="form.month" type="month"
                                        value-format="yyyy-MM" format="MM/yyyy" :clearable="false"></el-date-picker>
                    </div>
                    <div class="col-md-3">
                        <label>Tipo</label>
                        <el-select v-model="form.type">
                            <el-option key="sale" value="sale" label="Venta"></el-option>
                            <el-option key="purchase" value="purchase" label="Compra"></el-option>
                        </el-select>
                    </div>
                </div>
            </div>
            <div class="form-actions text-right pt-2">
                <el-button type="primary" :loading="loading_submit" @click.prevent="clickDownload">
                    <template v-if="loading_submit">
                        Generando...
                    </template>
                    <template v-else>
                        Generar
                    </template>
                </el-button>
            </div>
            <!--</div>-->
        </div>
    </div>
</template>

<script>
    import queryString from 'query-string'

    export default {
        data() {
            return {
                loading: false,
                loading_submit: false,
                title: null,
                resource: 'account',
                error: {},
                form: {},
            }
        },
        async created() {
            this.initForm();
            this.title = 'Generar';
        },
        methods: {
            initForm() {
                this.errors = {};
                this.form = {
                    month: moment().format('YYYY-MM'),
                    type: 'sale'
                }
            },
            clickDownload() {
                this.loading_submit = true;
                let query = queryString.stringify({
                    ...this.form
                });
                window.open(`/${this.resource}/format/download?${query}`, '_blank');
                this.loading_submit = false;
            }
        }
    }
</script>

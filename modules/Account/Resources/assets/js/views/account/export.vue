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
                <!--<div class="form-body">-->
                <!--<div class="row">-->
                    <!--<div class="col-md-4">-->
                        <!--<x-form-group label="Establecimiento" :error="errors.establishment_id">-->
                            <!--<el-select v-model="form.establishment_id">-->
                                <!--<el-option key="all" value="all" label="Todos"></el-option>-->
                                <!--<el-option v-for="option in establishments" :key="option.id" :value="option.id" :label="option.description"></el-option>-->
                            <!--</el-select>-->
                        <!--</x-form-group>-->
                    <!--</div>-->
                    <!--<div class="col-md-4">-->
                        <!--<x-form-group label="Tipo de documento" :error="errors.document_type_id">-->
                            <!--<el-select v-model="form.document_type_id" @change="changeDocumentType">-->
                                <!--<el-option key="all" value="all" label="Todos"></el-option>-->
                                <!--<el-option v-for="option in document_types" :key="option.id" :value="option.id" :label="option.description"></el-option>-->
                            <!--</el-select>-->
                        <!--</x-form-group>-->
                    <!--</div>-->
                <!--</div>-->
                <!--<div class="row">-->
                    <!--<div class="col-md-4">-->
                        <!--<x-form-group label="Series" :error="errors.series">-->
                            <!--<el-select v-model="form.series">-->
                                <!--<el-option key="all" value="all" label="Todos"></el-option>-->
                                <!--<el-option v-for="option in series" :key="option.id" :value="option.number" :label="option.number"></el-option>-->
                            <!--</el-select>-->
                        <!--</x-form-group>-->
                    <!--</div>-->
                <!--</div>-->
                <div class="row">
                    <!--<div class="col-md-4">-->
                        <!--<x-form-group label="Periodo" :error="errors.period">-->
                            <!--<el-select v-model="form.period">-->
                                <!--<el-option key="month" value="month" label="Por mes"></el-option>-->
                                <!--<el-option key="between_months" value="between_months" label="Entre meses"></el-option>-->
                                <!--<el-option key="date" value="date" label="Por fecha"></el-option>-->
                                <!--<el-option key="between_dates" value="between_dates" label="Entre fechas"></el-option>-->
                            <!--</el-select>-->
                        <!--</x-form-group>-->
                    <!--</div>-->
                    <!--<template v-if="form.period === 'month' || form.period === 'between_months'">-->
                        <div class="col-md-4">
                            <label>Periodo</label>
                            <el-date-picker v-model="form.month" type="month"
                                            value-format="yyyy-MM" format="MM/yyyy" :clearable="false"></el-date-picker>
                        </div>
                    <!--</template>-->
                    <!--<template v-if="form.period === 'between_months'">-->
                        <!--<div class="col-md-4">-->
                            <!--<x-form-group label="Mes al" :error="errors.month_end">-->
                                <!--<el-date-picker v-model="form.month_end" type="month"-->
                                                <!--:picker-options="pickerOptionsMonths"-->
                                                <!--value-format="yyyy-MM" format="MM/yyyy" :clearable="false"></el-date-picker>-->
                            <!--</x-form-group>-->
                        <!--</div>-->
                    <!--</template>-->
                    <!--<template v-if="form.period === 'date' || form.period === 'between_dates'">-->
                        <!--<div class="col-md-4">-->
                            <!--<x-form-group label="Fecha del" :error="errors.date_start">-->
                                <!--<el-date-picker v-model="form.date_start" type="date"-->
                                                <!--@change="changeDisabledDates"-->
                                                <!--value-format="yyyy-MM-dd" format="dd/MM/yyyy" :clearable="false"></el-date-picker>-->
                            <!--</x-form-group>-->
                        <!--</div>-->
                    <!--</template>-->
                    <!--<template v-if="form.period === 'between_dates'">-->
                        <!--<div class="col-md-4">-->
                            <!--<x-form-group label="Fecha al" :error="errors.date_end">-->
                                <!--<el-date-picker v-model="form.date_end" type="date"-->
                                                <!--:picker-options="pickerOptionsDates"-->
                                                <!--value-format="yyyy-MM-dd" format="dd/MM/yyyy" :clearable="false"></el-date-picker>-->
                            <!--</x-form-group>-->
                        <!--</div>-->
                    <!--</template>-->
                <!--</div>-->
                <!--<div class="row">-->
                    <div class="col-md-3">
                        <label>Exportar a</label>
                        <el-select v-model="form.type">
                            <el-option key="concar" value="concar" label="CONCAR"></el-option>
                            <el-option key="siscont" value="siscont" label="SISCONT"></el-option>
                            <el-option key="foxcont" value="foxcont" label="FOXCONT"></el-option>
                            <el-option key="contasis" value="contasis" label="CONTASIS"></el-option>
                            <el-option key="adsoft" value="adsoft" label="ADSOFT"></el-option>
                            <el-option key="sumerius" value="sumerius" label="SUMERIUS"></el-option>
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
                // establishments: [],
                // document_types: [],
                // series: [],
                error: {},
                form: {},
                // pickerOptionsDates: {
                //     disabledDate: (time) => {
                //         time = moment(time).format('YYYY-MM-DD')
                //         return this.form.date_start > time
                //     }
                // },
                // pickerOptionsMonths: {
                //     disabledDate: (time) => {
                //         time = moment(time).format('YYYY-MM')
                //         return this.form.month_start > time
                //     }
                // },
            }
        },
        async created() {
            // this.loading = true;
            this.initForm();
            this.title = 'Exportar';
            // await this.$http.get(`/${this.resource}/tables`)
            //     .then(response => {
            //         this.establishments = response.data.establishments
            //         this.document_types = response.data.document_types
            //         this.customers = response.data.customers
            //         this.resetForm()
            //     })
            // this.loading = false
        },
        methods: {
            initForm() {
                this.errors = {}
                this.form = {
                    // establishment_id: 'all',
                    // document_type_id: 'all',
                    // series: 'all',
                    // period: 'month',
                    // date_start: moment().format('YYYY-MM-DD'),
                    // date_end: moment().format('YYYY-MM-DD'),
                    month: moment().format('YYYY-MM'),
                    // month_end: moment().format('YYYY-MM'),
                    type: 'concar'
                }
            },
            // resetForm() {
            //     this.form.establishment_id = (this.establishments.length > 0)?this.establishments[0].id:'all'
            // },
            // changeDocumentType() {
            //     this.form.series = 'all'
            //     if (this.form.document_type_id) {
            //         let establishment = _.find(this.establishments, {'id': this.form.establishment_id})
            //         this.series = _.filter(establishment.series, {'document_type_id': this.form.document_type_id})
            //     } else {
            //         this.series = []
            //     }
            // },
            // changeDisabledDates() {
            //     if (this.form.date_end < this.form.date_start) {
            //         this.form.date_end = this.form.date_start
            //     }
            // },
            // changeDisabledMonths() {
            //     if (this.form.month_end < this.form.month_start) {
            //         this.form.month_end = this.form.month_start
            //     }
            // },
            clickDownload() {
                this.loading_submit = true;
                let query = queryString.stringify({
                    ...this.form
                });
                window.open(`/${this.resource}/download?${query}`, '_blank');
                this.loading_submit = false;
            }
        }
    }
</script>

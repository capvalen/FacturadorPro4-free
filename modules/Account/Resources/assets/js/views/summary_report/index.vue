<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>{{ title }}</span></li>
            </ol>
        </div>

        <div class="card" >
            <div class="card-header bg-info">
                <h3 class="my-0">{{ title }}</h3>
            </div>
            <div class="card-body">

                <div class="row mt-2">

                        <div class="col-md-3">
                            <div class="form-group" :class="{'has-danger': errors.date_start}">
                                <label class="control-label">Fecha inicial</label>
                                <el-date-picker v-model="form.date_start" type="date"
                                                @change="changeDisabledDates"
                                                value-format="yyyy-MM-dd" format="dd/MM/yyyy" :clearable="true"></el-date-picker>
                                <small class="form-control-feedback" v-if="errors.date_start" v-text="errors.date_start[0]"></small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group" :class="{'has-danger': errors.date_end}">
                                <label class="control-label">Fecha final</label>
                                <el-date-picker v-model="form.date_end" type="date"
                                                :picker-options="pickerOptionsDates"
                                                value-format="yyyy-MM-dd" format="dd/MM/yyyy" :clearable="true"></el-date-picker>
                                <small class="form-control-feedback" v-if="errors.date_end" v-text="errors.date_end[0]"></small>
                            </div>
                        </div>

                        <div class="col-md-6" style="margin-top:29px">
                            <el-button class="submit" type="primary" @click.prevent="getRecordsByFilter" :loading="loading_submit" icon="el-icon-search" >Buscar</el-button>
                            <template v-if="accepted_documents.length > 0">


                                <el-button class="submit" type="success" @click.prevent="clickDownload('excel')"><i class="fa fa-file-excel" ></i>  Exportal Excel</el-button>

                            </template>
                        </div>

                </div>
                <div class="row mt-3 mb-4">
                    <div class="col-md-12" v-if="accepted_documents.length > 0">
                        <h4>CONFIRMADOS
                            <el-tooltip class="item" effect="dark" content="Facturas aceptadas" placement="top-start">
                                <i class="fa fa-info-circle"></i>
                            </el-tooltip>
                        </h4>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Comprobante</th>
                                        <th>Serie</th>
                                        <th class="text-center">N° Inicial</th>
                                        <th class="text-center">N° Final</th>
                                        <th class="text-right">Valor venta</th>
                                        <th class="text-right">IGV</th>
                                        <th class="text-right">ICBPER</th>
                                        <th class="text-right">Importe</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(row, index) in accepted_documents" :key="index">
                                        <td>{{row.document_type_description}}</td>
                                        <td>{{row.series}}</td>
                                        <td class="text-center">{{row.start_number}}</td>
                                        <td class="text-center">{{row.end_number}}</td>
                                        <td class="text-right">{{row.total_value}}</td>
                                        <td class="text-right">{{row.total_igv}}</td>
                                        <td class="text-right">{{row.total_plastic_bag_taxes}}</td>
                                        <td class="text-right">{{row.total}}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-right" colspan="4">Total</td>
                                        <td class="text-right">{{totals_accepted_documents.general_total_value}}</td>
                                        <td class="text-right">{{totals_accepted_documents.general_total_igv}}</td>
                                        <td class="text-right">{{totals_accepted_documents.general_total_plastic_bag_taxes}}</td>
                                        <td class="text-right">{{totals_accepted_documents.general_total}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-12 mt-4" v-if="voided_documents.length > 0">
                        <h4>INVALIDADOS
                            <el-tooltip class="item" effect="dark" content="Facturas anuladas" placement="top-start">
                                <i class="fa fa-info-circle"></i>
                            </el-tooltip>
                        </h4>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Comprobante</th>
                                        <th>Serie</th>
                                        <th class="text-center">Numeros</th>
                                        <th class="text-right">Importe</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(row, index) in voided_documents" :key="index">
                                        <td>{{row.document_type_description}}</td>
                                        <td>{{row.series}}</td>
                                        <td class="text-center">{{row.voided}}</td>
                                        <td class="text-right">{{row.total}}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-right" colspan="3">Total</td>
                                        <td class="text-right">{{totals_voided_documents.general_total}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
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
                resource: 'account/summary-report',
                errors: {},
                totals_accepted_documents: {},
                totals_voided_documents: {},
                accepted_documents: [],
                voided_documents: [],
                form: {},
                pickerOptionsDates: {
                    disabledDate: (time) => {
                        time = moment(time).format('YYYY-MM-DD')
                        return this.form.date_start > time
                    }
                },
            }
        },
        async created() {
            this.initForm();
            this.title = 'Reporte resumido - Ventas';
        },
        methods: {
            initForm(){

                this.form = {
                    date_start:null,
                    date_end:null,
                }

            },
            changeDisabledDates() {
                if (this.form.date_end < this.form.date_start) {
                    this.form.date_end = this.form.date_start
                }
            },
            async getRecordsByFilter(){

                this.loading_submit = await true
                await this.getRecords()
                this.loading_submit = await false

            },
            getRecords() {
                return this.$http.get(`/${this.resource}/records?${this.getQueryParameters()}`).then((response) => {
                    this.accepted_documents = response.data.accepted_documents
                    this.voided_documents = response.data.voided_documents
                    this.totals_accepted_documents = response.data.totals_accepted_documents
                    this.totals_voided_documents = response.data.totals_voided_documents

                }).catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data
                    }
                })
                .then(() => {
                    this.loading_submit = false
                });


            },
            getQueryParameters() {
                return queryString.stringify({
                    ...this.form
                })
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

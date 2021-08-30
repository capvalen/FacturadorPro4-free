<template>
    <el-dialog :title="title" :visible="showDialog" @close="closeDialog">
        <div class="">
            <div class="row mt-2">
                <!-- <div class="col-lg-4 col-md-4 pb-4">
                    <div class="form-group">
                        <label class="control-label">Seleccione Mes </label>

                        <el-date-picker
                            v-model="search.month"
                            type="month"
                            style="width: 100%;"
                            placeholder="Buscar"
                            value-format="yyyy-MM-dd"
                        >
                        </el-date-picker>
                    </div>
                </div> -->
                
                <div class="col-lg-4 col-md-4 pb-4">
                    <div class="form-group">
                        <label class="control-label">Fecha inicio </label>

                        <el-date-picker
                            v-model="form.date_start"
                            type="date"
                            style="width: 100%;"
                            placeholder="Buscar"
                            value-format="yyyy-MM-dd"
                            :clearable="false"
                        >
                        </el-date-picker>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 pb-4">
                    <div class="form-group">
                        <label class="control-label">Fecha término</label>

                        <el-date-picker
                            v-model="form.date_end"
                            type="date"
                            style="width: 100%;"
                            placeholder="Buscar"
                            value-format="yyyy-MM-dd"
                            :picker-options="pickerOptionsDates"
                            :clearable="false"
                        >
                        </el-date-picker>
                    </div>
                </div>
                
                <div class="col-lg-4 mt-3">
                    <div class="form-group">
                        <el-checkbox  v-model="form.anulled">Cargar anulados</el-checkbox><br>
                    </div>
                </div>
            </div>
        </div>
        <span slot="footer" class="dialog-footer">
            <el-button type="warning" @click="closeDialog">Cancelar</el-button>
            <el-button type="primary" @click="downloadReportComplete('excel')">Descargar</el-button>
        </span>
    </el-dialog>
</template>

<script>
    import moment from 'moment'
    import queryString from 'query-string'

    export default {
        props: ["showDialog", "documentId"],
        data() {
            return {
                title: "Reporte de Pagos Completo",
                resource: "documents",
                form: {},
                pickerOptionsDates: {
                    disabledDate: (time) => {
                        time = moment(time).format('YYYY-MM-DD')
                        return this.form.date_start > time
                    }
                },
            };
        },
        created() { 
            this.initForm()
        },
        methods: {
            changeDisabledDates() {
                if (this.form.date_end < this.form.date_start) {
                    this.form.date_end = this.form.date_start
                }
            },
            initForm(){

                this.form = {
                    date_start: moment().format('YYYY-MM-DD'),
                    date_end: moment().format('YYYY-MM-DD'),
                    anulled: false,
                }

            },
            closeDialog() {
                this.initForm()
                this.$emit("update:showDialog", false);
            },
            downloadReportComplete(type)
            {
                window.open(`/${this.resource}/payments-complete?${this.getQueryParameters()}`, '_blank');
                
                // if(this.search.month){

                //     window.open(`/${this.resource}/payments/${type}/${this.search.month}/${this.search.anulled}`, '_blank');

                // } else {
                //     this.$message.error('Debe completar el formulario');
                // }
            },
            getQueryParameters() { 

                return queryString.stringify({
                    ...this.form
                })

            },
        }
    };
</script>

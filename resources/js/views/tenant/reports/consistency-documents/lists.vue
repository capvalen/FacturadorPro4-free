<template>
    <div class="card">
        <div class="card-header bg-info">
            <h3 class="my-0">Consistencia de documentos</h3>
        </div>
        <div class="data-table-visible-columns">
            <el-dropdown :hide-on-click="false">
                <el-button type="primary">
                    Mostrar/Ocultar columnas<i class="el-icon-arrow-down el-icon--right"></i>
                </el-button>
                <el-dropdown-menu slot="dropdown">
                    <el-dropdown-item v-for="(column, index) in columns" :key="index">
                        <el-checkbox v-model="column.visible">{{ column.title }}</el-checkbox>
                    </el-dropdown-item>
                </el-dropdown-menu>
            </el-dropdown>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label">
                            Rango de fechas
                        </label>
                        <el-date-picker
                            v-model="form.date_start"
                            :clearable="false"
                            :picker-options="pickeroptions"
                            end-placeholder="Fecha de fin"
                            range-separator="-"
                            start-placeholder="Fecha de inicio"
                            type="daterange"
                            value-format="yyyy-MM-dd">
                        </el-date-picker>
                    </div>
                </div>

                <div class="col-md-12">&nbsp;</div>
                <div class="col-md-4 d-flex pt-2">
                    <div class="form-group">
                        <el-button class="submit"
                                   type="primary"
                                   @click.prevent="refresh"
                                   :loading="loading_submit"
                                   icon="el-icon-search" >
                            Buscar
                        </el-button>

                    </div>
                </div>
            </div>
            <template>
                <el-table :data="tableData" style="width: 100%">
                    <el-table-column label="Serie" prop="serie.number"></el-table-column>
                    <el-table-column label="Número incial" prop="start"></el-table-column>
                    <el-table-column label="Número final" prop="end"></el-table-column>
                    <el-table-column label="Faltantes" prop="diff"></el-table-column>
                    <el-table-column label="Registrados" prop="registered"></el-table-column>
                    <el-table-column v-if="columns.sent.visible" label="Enviados" prop="sent"></el-table-column>
                    <el-table-column v-if="columns.accepted.visible" label="Aceptados" prop="accepted"></el-table-column>
                    <el-table-column v-if="columns.observed.visible" label="Observados" prop="observed"></el-table-column>
                    <el-table-column v-if="columns.rejected.visible" label="Rechazados" prop="rejected"></el-table-column>
                    <el-table-column v-if="columns.canceled.visible" label="Anulados" prop="canceled"></el-table-column>
                    <el-table-column v-if="columns.byVoiding.visible" label="Por anular" prop="byVoiding"></el-table-column>
                </el-table>
            </template>
        </div>
        <tenant-tasks-form :showDialog.sync="showDialog" :tableData.sync="tableData" @refresh="refresh"></tenant-tasks-form>
    </div>
</template>

<script>
    export default {
            data() {
                return {
                    resource: 'reports/consistency-documents',
                    loading_submit: false,
                    pickeroptions: {
                        onPick: ({maxDate, minDate}) => {
                            this.selectDate = 1;
                            this.minDate = minDate && minDate.getTime()
                            if (maxDate) {
                                this.minDate = ''
                            }
                            if(minDate  && maxDate){
                                this.selectDate = 0;
                            }
                        },
                        disabledDate: date => {
                            let now = new Date();
                            let tiempo = date.getTime();
                            // Tope de consulta + - 30 dias de la fecha seleccionada
                            let Dias = 30 * 24 * 60 * 60 * 1000
                            if (this.minDate !== '' && this.selectDate == 1) {
                                // No permite que se seleccione fechas mayores a hoy o menores al minimo
                                if ((this.minDate + Dias) > now.getTime() || (this.minDate - Dias) < this.minBaseDate) {
                                    return false
                                }
                                return tiempo > (this.minDate + Dias) || tiempo <  (this.minDate - Dias);
                            }
                            return tiempo > (now.getTime()) || tiempo < (this.minDate);
                        },
                    },
                    showDialog: false,
                    minDate: new Date(),
                    maxDate: new Date(),
                    minBaseDate : new Date(), /* Variable temporal para que no sea sobreescrita.*/
                    selectDate: 0,
                    form: {
                        date_start: [
                            new Date(),
                            new Date()
                        ]
                    },
                    columns: {
                        sent: {
                            title: 'Enviados',
                            visible: false
                        },
                        accepted: {
                            title: 'Aceptados',
                            visible: false
                        },
                        observed: {
                            title: 'Observados',
                            visible: false
                        },
                        rejected: {
                            title: 'Rechazados',
                            visible: false
                        },
                        canceled: {
                            title: 'Anulados',
                            visible: false
                        },
                        byVoiding: {
                            title: 'Por anular',
                            visible: false
                        },
                    },
                    tableData: []
                }
        },
        created() {
            this.minDate = new Date(this.minDate.getFullYear(), this.minDate.getMonth(), 1);
            this.form.date_start[0] = this.minDate;
            this.refresh();
        },
        methods: {
            refresh() {
                this.loading_submit = true;
                this.$http.post(`/${this.resource}/lists`, this.form)
                    .then((response) => {
                        this.tableData = response.data;
                        if (this.tableData !== undefined && this.tableData[0] !== undefined) {
                            this.minDate = new Date(this.tableData[0].min);
                            this.maxDate = new Date(this.tableData[0].max);
                            this.maxDate = this.maxDate.getTime();
                            this.minDate = this.minDate.getTime();
                            this.minBaseDate  = this.minDate;
                        }
                    }).catch((error) => {
                    console.log(error);
                }).then(() => {})
                    .finally(()=>{
                    this.loading_submit = false;
                });
            }
        }
    }
</script>


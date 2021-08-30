<template>
    <el-dialog :title="title" :visible="showDialog" @close="clickClose">
        <div class="">
            <div class="row mt-2">
                <div class="col-lg-4 col-md-4 pb-4">
                    <div class="form-group">
                        <label class="control-label">Fecha inicio </label>

                        <el-date-picker
                            v-model="search.d_start"
                            type="date"
                            style="width: 100%;"
                            placeholder="Buscar"
                            value-format="yyyy-MM-dd"
                        >
                        </el-date-picker>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 pb-4">
                    <div class="form-group">
                        <label class="control-label">Fecha término</label>

                        <el-date-picker
                            v-model="search.d_end"
                            type="date"
                            style="width: 100%;"
                            placeholder="Buscar"
                            value-format="yyyy-MM-dd"
                            :picker-options="pickerOptionsDates"
                        >
                        </el-date-picker>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 pb-4">
                    <div class="form-group">
                        <label class="control-label">Tipo</label>
                        <el-select v-model="search.type">
                            <el-option value="pdf" label="PDF"></el-option>
                            <el-option value="excel" label="EXCEL"></el-option>
                        </el-select>
                    </div>
                </div>
                 <div class="col-lg-2 col-md-2 pb-4">
                    <div class="form-group"  style="padding: 2.5%;"> <br>

                    </div>
                </div>
            </div>
        </div>
        <span slot="footer" class="dialog-footer">
            <el-button type="warning" @click="clickClose">Cancelar</el-button>
            <el-button type="primary" @click="downloadReport">Descargar</el-button>
        </span>
    </el-dialog>
</template>

<script>
export default {
    props: ["showDialog", "documentId"],
    data() {
        return {
            title: "Reporte de Pagos",
            resource: "document_payments",
            search: {},
            pickerOptionsDates: {
                disabledDate: time => {
                    time = moment(time).format("YYYY-MM-DD");
                    return this.search.d_start > time;
                }
            }
        };
    },
    async created() {},
    methods: {
        clickClose() {
            this.$emit("update:showDialog", false);
        },
        downloadReport()
        {
            if(this.search.d_end && this.search.d_start && this.search.type){

                 window.open(`/${this.resource}/report/${this.search.d_start}/${this.search.d_end}/${this.search.type}`, '_blank');
            } else {
                this.$message.error('Debe completar el formulario para generar un reporte');
            }
        }
    }
};
</script>

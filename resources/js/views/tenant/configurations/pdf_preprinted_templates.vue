<template>
    <div>
        <div class="page-header pr-0">
            <h2>
                <a href="#"><i class="fas fa-cogs"></i></a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Configuración</span></li>
                <li><span class="text-muted">Formatos Pre Impresos</span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <button
                    type="button"
                    @click="addSeeder"
                    class="btn btn-custom btn-sm  mt-2 mr-2"
                >
                    <i class="el-icon-refresh"></i> Actualizar listado
                </button>
            </div>
        </div>
        <div class="card">
            <div class="card-header bg-info">
                <h3 class="my-0">
                    Selección de plantilla de impresión para imprimir
                </h3>
            </div>
            <div class="card-body pt-0 pb-5">
                <div class="row">
                    <div v-for="(o, index) in formatos" class="col-md-3 my-2">
                        <el-card
                            :body-style="{ padding: '0px' }"
                            :id="o.formats"
                        >
                            <a @click="viewImage(o.formats)"
                                ><img
                                    :src="
                                        path.origin +
                                            '/templates/preprinted_pdf/' +
                                            o.formats +
                                            '/image.png'
                                    "
                                    class="image"
                                    style="width: 100%"
                            /></a>
                            <div style="padding: 14px;">
                                <span class="text-center">{{ o.formats }}</span>
                                <div class="bottom clearfix text-right">
                                    <!-- <el-button type="submit" class="button" @change="changeFormat(o.formats)">Activo</el-button> -->
                                    <el-button type="submit" @click="showPdf(o.formats)" class="button">Imprimir</el-button>
                                    <!-- <el-radio
                                        v-model="form.formats"
                                        :label="o.formats"
                                        @change="changeFormat(o.formats)"
                                    >

                                        <span v-if="form.formats == o.formats"
                                            >Activo</span
                                        >
                                        <span v-else>Activar</span>
                                    </el-radio> -->
                                </div>
                            </div>
                        </el-card>
                    </div>
                </div>
            </div>
        </div>
        <el-dialog :visible.sync="modalImage" width="60">
            <span>
                <img
                    :src="
                        path.origin +
                            '/templates/preprinted_pdf/' +
                            template +
                            '/image.png'
                    "
                    class="image"
                    style="width: 100%"
                />
            </span>
            <span slot="footer" class="dialog-footer">
                <el-button @click="modalImage = false">Cerrar</el-button>
                <el-button @click="changeFormat(template)" type="primary"
                    >Activar</el-button
                >
            </span>
        </el-dialog>
    </div>
</template>

<script>
export default {
    props: ["path_image"],

    data() {
        return {
            loading_submit: false,
            resource: "configurations",
            errors: {},
            form: {},
            formatos: [],
            path: location,
            modalImage: false,
            template: ""
        };
    },
    async created() {
        // await this.$http.get(`/${this.resource}/record`).then(response => {
        //     if (response.data !== "") {
        //         this.form = response.data.data;
        //     }
        // });

        await this.$http.get(`/${this.resource}/preprinted/getFormats`).then(response => {
            if (response.data !== "") this.formatos = response.data;
            // console.log(this.formatos)
        });
    },
    methods: {
        showPdf(template){
            let body = {
                base_pdf_template: template
            }
            this.$http.post(`/${this.resource}/preprinted/generateDispatch`,body).then(response => {
                window.open(`/${this.resource}/preprinted/`+response.data, '_blank');

            });
        },
        changeFormat(value) {
            this.modalImage = false;
            this.formatos = {
                formats: value
            };

            this.$http
                .post(`/${this.resource}/changeFormat`, this.formatos)
                .then(response => {
                    this.$message.success(response.data.message);
                    location.reload();
                });
        },
        addSeeder() {
            var ruta = location.host;
            this.$http.get(`/${this.resource}/preprinted/addSeeder`).then(response => {
                this.$message.success(response.data.message);
                location.reload();
            });
        },
        viewImage($value) {
            this.template = $value;

            this.modalImage = true;
        }
    }
};
</script>

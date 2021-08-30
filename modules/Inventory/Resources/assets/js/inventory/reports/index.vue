<template>
    <div class="row card-table-report">
        <div class="col-md-12">
            <div v-loading="loading" class="card card-primary">
                <div class="card-header">
                    <h4 class="card-title">Consulta de inventarios</h4>
                    <div class="data-table-visible-columns" style="top:10px">
                        <el-dropdown :hide-on-click="false">
                            <el-button type="primary">
                                Mostrar/Ocultar filtros<i class="el-icon-arrow-down el-icon--right"></i>
                            </el-button>
                            <el-dropdown-menu slot="dropdown">
                                <el-dropdown-item v-for="(column, index) in filters" :key="index">
                                    <el-checkbox v-model="column.visible">{{ column.title }}</el-checkbox>
                                </el-dropdown-item>
                            </el-dropdown-menu>
                        </el-dropdown>
                    </div>

                </div>
                <div class="card-body">
                    <div class="row m-b-10">
                        <div class="col-md-4">
                            <el-select v-model="form.warehouse_id"
                                       placeholder="Seleccionar almacén"
                                       @change="changeWarehouse">
                                <el-option key="all" label="Todos" value="all"></el-option>
                                <el-option v-for="opt in warehouses"
                                           :key="opt.id"
                                           :label="opt.description"
                                           :value="opt.id">
                                </el-option>
                            </el-select>
                        </div>
                        <div class="col-md-3">
                            <el-select v-model="form.filter"
                                       placeholder="Seleccionar filtro"
                                       @change="changeFilter">
                                <el-option key="01" label="Todos" value="01"></el-option>
                                <el-option key="02" label="Stock < 0" value="02"></el-option>
                                <el-option key="03" label="Stock = 0" value="03"></el-option>
                                <el-option key="04" label="0 < Stock <= Stock mínimo" value="04"></el-option>
                                <el-option key="05" label="Stock > Stock mínimo" value="05"></el-option>
                            </el-select>
                        </div>
                        <div class="col-md-3" v-if="filters.categories.visible">
                            <div class="form-group">
                                <el-select
                                    v-model="form.category_id"
                                    clearable
                                    filterable
                                    placeholder="Seleccionar categoría"
                                    @change="changeFilter">
                                    <el-option v-for="option in categories" :key="option.id" :label="option.name"
                                               :value="option.id"></el-option>
                                </el-select>

                            </div>
                        </div>
                        <div class="col-md-3" v-if="filters.brand.visible">
                            <div class="form-group">
                                <el-select
                                    v-model="form.brand_id"
                                    clearable
                                    filterable
                                    placeholder="Seleccionar marca"
                                    @change="changeFilter">
                                    <el-option v-for="option in brands" :key="option.id" :label="option.name"
                                               :value="option.id"></el-option>
                                </el-select>
                            </div>
                        </div>
                        <div class="col-auto">
                            <el-button :disabled="records.length <= 0"
                                       :loading="loadingPdf"
                                       @click="clickExport('pdf')"><i class="fa fa-file-pdf"></i> Exportar PDF
                            </el-button>
                        </div>
                        <div class="col-auto">
                            <el-button :disabled="records.length <= 0"
                                       :loading="loadingXlsx"
                                       @click="clickExport('xlsx')"><i class="fa fa-file-excel"></i> Exportar Excel
                            </el-button>
                        </div>
                    </div>

                    <div v-if="records.length > 0" class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-striped table-responsive-xl table-bordered table-hover"
                                       >
                                    <thead class="">
                                    <tr>
                                        <th>#</th>
                                        <th>Descripción</th>
                                        <th>Categoria</th>
                                        <th class="text-right">Stock mínimo</th>
                                        <th class="text-right">Stock actual</th>
                                        <th class="text-right">Precio de venta</th>
                                        <th class="text-right">Costo</th>
                                        <th>Marca</th>
                                        <th class="text-center">F. vencimiento</th>
                                        <th>Almacén</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(row, index) in records">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ row.name }}</td>
                                        <td>{{ row.item_category_name }}</td>
                                        <td class="text-right">{{ row.stock_min }}</td>
                                        <td class="text-right">{{ row.stock }}</td>
                                        <td class="text-right">{{ row.sale_unit_price }}</td>
                                        <td class="text-right">{{ row.purchase_unit_price }}</td>
                                        <td>{{ row.brand_name }}</td>
                                        <td class="text-center">{{ row.date_of_due }}</td>
                                        <td>{{ row.warehouse_name }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12">
                            Total {{ records.length }}
                        </div>
                    </div>
                    <div v-else class="row">
                        <div class="col-md-12">
                            <strong>No se encontraron registros</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>

import moment from "moment";

export default {
    props: [],
    data() {
        return {
            // loading_submit: false,
            // showDialogLots: false,
            // showDialogLotsOutput: false,
            // titleDialog: null,
            loading: false,
            loadingPdf: false,
            loadingXlsx: false,
            resource: 'inventory/report',
            errors: {},
            form: {},
            warehouses: [],
            categories: [],
            brands: [],
            filters: [],
            records: []
        }
    },
    created() {
        this.initTables();
        this.initForm();
        this.filters = {
            categories: {
                title: 'Categorias',
                visible: false
            },
            brand: {
                title: 'Marcas',
                visible: false
            },
        }
    },
    methods: {
        initForm() {
            this.form = {
                'warehouse_id': null,
                'filter': '01',
                'category_id': null,
                'brand_id': null,
            }
        },
        initTables() {
            this.$http.get(`/${this.resource}/tables`)
                .then(response => {
                    this.warehouses = response.data.warehouses;
                    this.brands = response.data.brands;
                    this.categories = response.data.categories;
                });
        },
        async getRecords() {
            if (_.isNull(this.form.warehouse_id)) {
                this.$message.error('Seleccionar un almacén ');
                return false;
            }
            this.loading = true;
            this.records = [];
            await this.$http.post(`/${this.resource}/records`, this.form)
                .then(response => {
                    this.records = response.data;
                });
            this.loading = false;
        },
        changeWarehouse() {
            this.getRecords();
        },
        changeFilter() {
            this.getRecords();
        },
        async clickExport(format) {
            this.loading = true;
            // this.loadingSubmit = true;
            this.loadingPdf = (format === 'pdf');
            this.loadingXlsx = (format === 'xlsx');
            this.errors = {};
            await this.$http({
                url: `/${this.resource}/export`,
                method: 'POST',
                responseType: 'blob',
                data: {
                    'records': this.records,
                    'format': format,
                }
            })
                .then(response => {
                    let res = response.data;
                    if (res.type === 'application/json') {
                        this.$message.error('Error al exportar');
                    } else {
                        const url = window.URL.createObjectURL(new Blob([res]));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', 'ReporteInv_' + moment().format('HHmmss') + '.' + format);
                        document.body.appendChild(link);
                        link.click();
                    }
                })
                .catch(error => {
                    console.log(error);
                    this.errors = error;
                })
                .then(() => {
                    this.loadingPdf = false;
                    this.loadingXlsx = false;
                    this.loading = false;
                });
            // this.loadingSubmit = false;
            //

        }
    }
}
</script>

<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>{{ title }}</span></li>
            </ol>


        </div>
        <div class="card mb-0">
            <div class="card-header bg-info">
                <h3 class="my-0">{{ title }}</h3>
            </div>
            <div class="col-md-12 row">
                <div class="col-md-4">
                    <div v-if="warehouses.length > 0" class="form-group">
                        <label class="control-label">Almacén</label>
                        <el-select v-model="warehouse_id" clearable filterable>
                            <el-option v-for="option in warehouses" :key="option.id" :label="option.description"
                                       :value="option.id"></el-option>
                        </el-select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div v-if="categories.length > 0" class="form-group">
                        <label class="control-label">Categoría</label>
                        <el-select v-model="category_id" clearable filterable >
                            <el-option v-for="option in categories" :key="option.id" :label="option.name"
                                       :value="option.id"></el-option>
                        </el-select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div v-if="brands.length > 0" class="form-group">
                        <label class="control-label">Marcas</label>
                        <el-select v-model="brand_id" clearable filterable >
                            <el-option v-for="option in brands" :key="option.id" :label="option.name"
                                       :value="option.id"></el-option>
                        </el-select>
                    </div>
                </div>

                <div class="col-md-6">
                    <el-button :loading="loading_submit" class="submit"
                               icon="el-icon-search"
                               type="primary" @click.prevent="Search">Buscar
                    </el-button>
                    <template v-if="records.length>0">
                        <el-button class="submit m-t-30" icon="el-icon-tickets" type="danger"
                                   @click.prevent="clickDownload('pdf')">Exportar PDF
                        </el-button>
                        <el-button class="submit m-t-30" type="success"
                                   @click.prevent="clickDownload('excel')"><i
                            class="fa fa-file-excel"></i> Exportar Excel
                        </el-button>
                    </template>
                </div>
                <div class="col-md-3">


                </div>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Descripción</th>
                        <th>Categoria</th>
                        <th>Inventario actual</th>
                        <th>Precio de venta</th>
                        <th>Costo</th>
                        <th>Marca</th>
                        <th>F. vencimiento</th>
                        <th>Almacén</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(row, index) in items" :key="index">
                        <td>{{ row.id }}</td>
                        <td>{{ row.description }}</td>
                        <td>{{ row.category }}</td>
                        <td>{{ row.stock | toDecimals }}</td>
                        <td>{{ row.sale_unit_price | toDecimals }}</td>
                        <td>{{ row.purchase_unit_price | toDecimals }}</td>
                        <td>{{ row.brand }}</td>
                        <td>{{ row.date_of_due }}</td>
                        <td>{{ row.warehouse_description }}</td>
                        <!--
                        <td class="text-right">
                            <button class="btn waves-effect waves-light btn-xs btn-info" type="button"
                                    @click.prevent="clickMove(row.id)">Trasladar
                            </button>
                            <button v-if="typeUser == 'admin'" class="btn waves-effect waves-light btn-xs btn-warning"
                                    type="button"
                                    @click.prevent="clickRemove(row.id)">Remover
                            </button>
                        </td>
                        -->
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import queryString from 'query-string'


export default {
    props: [
        'type',
        'typeUser',
        'warehouses',
        'categories',
        'brands',
        'title',
        'ruta',
        'export_pdf',
        'export_xls'
    ],
    components: {
    },

    data() {
        return {
            loading_submit: false,
            resource: 'inventory',
            recordId: null,
            items: [],
            records: [],
            warehouse_id: '',
            category_id: '',
            brand_id: '',
        }
    },
    created() {
        this.warehouse_id = '';
        this.category_id = '';
        this.brand_id = '';
        this.items = this.records;
        this.loading_submit = false;
    },
    async mounted() {
        await this.$http.post(`inventory`, {
            'warehouse_id': this.warehouse_id,
            'brand_id': this.brand_id,
            'category_id': this.category_id,
        })
            .then(response => {
                this.records = response.data.reports;
                this.items = this.records;
            });
    },
    methods: {
        Search() {
            this.items = [];
            this.loading_submit = true;
            this.$http.post(`inventory`, {
                'warehouse_id': this.warehouse_id,
                'brand_id': this.brand_id,
                'category_id': this.category_id,
            })
                .then(response => {
                    this.records = response.data.reports;
                    this.items = this.records;
                }).finally(() => {
                this.loading_submit = false;
            });

        },

        clickDownload(type) {
            let query = queryString.stringify({
                'warehouse_id': this.warehouse_id,
                'brand_id': this.brand_id,
                'category_id': this.category_id,
            });
            window.open(`inventory/${type}/?${query}`, '_blank');

        },

    }
}
</script>

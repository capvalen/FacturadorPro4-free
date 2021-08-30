<template>
    <div>
        <div class="row">

            <div class="col-md-12 col-lg-12 col-xl-12 ">

                <div class="row mt-2">
                        <div class="col-md-2 form-group">
                            <label class="control-label">Tipo de usuario</label>
                            <el-select v-model="form.user_type" clearable
                                       @change="ChangedSalesnote">
                                <el-option key="CREADOR" value="CREADOR" label="Registrado por"></el-option>
                                <el-option v-show="form.document_type_id !== '80'" key="VENDEDOR" value="VENDEDOR" label="Vendedor asignado"></el-option>
                            </el-select>
                        </div>
                        <div class="col-md-2 form-group">
                            <label class="control-label">{{ form.user_type === 'CREADOR' ? 'Usuario' : 'Vendedor' }}</label>
                            <el-select v-model="form.user_id" clearable>
                                <el-option v-for="user in users" :key="user.id" :value="user.id" :label="user.name"></el-option>
                            </el-select>
                        </div>
                        <!-- <div class="col-md-2" >
                            <div class="form-group">
                                <label class="control-label">Usuario</label>
                                <el-input placeholder="Buscar"
                                    v-model="form.user"
                                    style="width: 100%;">
                                </el-input>
                            </div>
                        </div> -->

                        <div class="col-md-3" >
                            <div class="form-group">
                                <label class="control-label">Tipo</label>
                                <el-select v-model="form.type" @change="changeType">
                                    <el-option v-for="option in types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <label class="control-label">Periodo</label>
                            <el-select v-model="form.period" @change="changePeriod">
                                <el-option key="month" value="month" label="Por mes"></el-option>
                                <el-option key="between_months" value="between_months" label="Entre meses"></el-option>
                                <el-option key="date" value="date" label="Por fecha"></el-option>
                                <el-option key="between_dates" value="between_dates" label="Entre fechas"></el-option>
                            </el-select>
                        </div>
                        <template v-if="form.period === 'month' || form.period === 'between_months'">
                            <div class="col-md-2">
                                <label class="control-label">Mes de</label>
                                <el-date-picker v-model="form.month_start" type="month"
                                                @change="changeDisabledMonths"
                                                value-format="yyyy-MM" format="MM/yyyy" :clearable="false"></el-date-picker>
                            </div>
                        </template>
                        <template v-if="form.period === 'between_months'">
                            <div class="col-md-3">
                                <label class="control-label">Mes al</label>
                                <el-date-picker v-model="form.month_end" type="month"
                                                :picker-options="pickerOptionsMonths"
                                                value-format="yyyy-MM" format="MM/yyyy" :clearable="false"></el-date-picker>
                            </div>
                        </template>
                        <template v-if="form.period === 'date' || form.period === 'between_dates'">
                            <div class="col-md-3">
                                <label class="control-label">Fecha del</label>
                                <el-date-picker v-model="form.date_start" type="date"
                                                @change="changeDisabledDates"
                                                value-format="yyyy-MM-dd" format="dd/MM/yyyy" :clearable="false"></el-date-picker>
                            </div>
                        </template>
                        <template v-if="form.period === 'between_dates'">
                            <div class="col-md-3">
                                <label class="control-label">Fecha al</label>
                                <el-date-picker v-model="form.date_end" type="date"
                                                :picker-options="pickerOptionsDates"
                                                value-format="yyyy-MM-dd" format="dd/MM/yyyy" :clearable="false"></el-date-picker>
                            </div>
                        </template>

                        <div class="col-md-3" >
                            <div class="form-group">
                                <label class="control-label">Tipo de documento</label>
                                <el-select v-model="form.document_type_id"
                                           @change="ChangedSalesnote"
                                           clearable>
                                    <el-option v-for="option in document_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                            </div>
                        </div>
                        <!-- <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Establecimiento</label>
                                <el-select v-model="form.establishment_id" clearable>
                                    <el-option v-for="option in establishments" :key="option.id" :value="option.id" :label="option.name"></el-option>
                                </el-select>
                            </div>
                        </div> -->



                        <div class="col-lg-4 col-md-6" >
                            <div class="form-group">
                                <label class="control-label">
                                    {{(form.type == 'sale') ? 'Clientes':'Proveedores'}}
                                </label>

                                <el-select v-model="form.person_id" filterable remote  popper-class="el-select-customers"  clearable
                                    placeholder="Nombre o número de documento"
                                    :remote-method="searchRemotePersons"
                                    :loading="loading_search">
                                    <el-option v-for="option in persons" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>

                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="form-group">
                                <label class="control-label">Productos
                                </label>

                                <el-select v-model="form.item_id" filterable remote  popper-class="el-select-customers"  clearable
                                    placeholder="Código interno o nombre"
                                    :remote-method="searchRemoteItems"
                                    :loading="loading_search_items" >
                                    <el-option v-for="option in items" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>

                            </div>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <div class="form-group">
                                <label class="control-label">Marca
                                </label>

                                <el-select v-model="form.brand_id" filterable  popper-class="el-select-customers"  clearable
                                    placeholder="Nombre de la marca">
                                    <el-option v-for="option in brands" :key="option.id" :value="option.id" :label="option.name"></el-option>
                                </el-select>

                            </div>
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <div class="form-group">
                                <label class="control-label">Categoría</label>
                                <el-select v-model="form.category_id" filterable  popper-class="el-select-customers"  clearable
                                    placeholder="Nombre de la categoría">
                                    <el-option v-for="option in categories" :key="option.id" :value="option.id" :label="option.name"></el-option>
                                </el-select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">Plataforma</label>
                                <el-select v-model="form.web_platform_id" clearable>
                                    <el-option v-for="option in web_platforms" :key="option.id" :value="option.id" :label="option.name"></el-option>
                                </el-select>
                            </div>
                        </div>

                        <div class="col-lg-7 col-md-7 col-md-7 col-sm-12" style="margin-top:29px">
                            <el-button class="submit" type="primary" @click.prevent="getRecordsByFilter" :loading="loading_submit" icon="el-icon-search" >Buscar</el-button>

                            <template v-if="records.length>0">

                                <el-button class="submit" type="success" @click.prevent="clickDownload('excel')"><i class="fa fa-file-excel" ></i>  Exportal Excel</el-button>
                                <el-button class="submit" type="danger"  icon="el-icon-tickets" @click.prevent="clickDownload('pdf')" >Exportar PDF</el-button>

                            </template>

                        </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <slot name="heading"></slot>
                        </thead>
                        <tbody>
                            <slot v-for="(row, index) in records" :row="row" :index="customIndex(index)"></slot>
                        </tbody>
                    </table>
                </div>
                <el-pagination
                        @current-change="getRecords"
                        layout="total, prev, pager, next"
                        :total="pagination.total"
                        :current-page.sync="pagination.current_page"
                        :page-size="pagination.per_page">
                </el-pagination>
            </div>
        </div>
    </div>
</template>
<style>
.font-custom{
    font-size:15px !important
}
</style>
<script>

    import moment from 'moment'
    import queryString from 'query-string'

    export default {
        props: {
            resource: String,
        },
        data () {
            return {
                loading_submit:false,
                items: [],
                all_items: [],
                loading_search:false,
                columns: [],
                records: [],
                headers: headers_token,
                document_types: [],
                pagination: {},
                search: {},
                totals: {},
                establishment: null,
                establishments: [],
                types: [{id:'sale', description: 'Venta'},{id:'purchase', description: 'Compra'}],
                form: {},
                pickerOptionsDates: {
                    disabledDate: (time) => {
                        time = moment(time).format('YYYY-MM-DD')
                        return this.form.date_start > time
                    }
                },
                pickerOptionsMonths: {
                    disabledDate: (time) => {
                        time = moment(time).format('YYYY-MM')
                        return this.form.month_start > time
                    }
                },
                persons: [],
                all_customers: [],
                all_suppliers: [],
                loading_search:false,
                items: [],
                all_items: [],
                web_platforms: [],
                loading_search_items:false,
                brands: [],
                categories: [],
                users: [],
                total: 0
            }
        },
        created() {
            this.initForm()
            this.$eventHub.$on('reloadData', () => {
                this.getRecords()
            })
        },
        async mounted () {

            await this.$http.get(`/${this.resource}/filter`)
                .then(response => {
                    this.document_types = response.data.document_types;
                    this.all_customers = response.data.customers
                    this.all_suppliers = response.data.suppliers
                    this.all_items = response.data.items
                    this.web_platforms = response.data.web_platforms
                    this.brands = response.data.brands
                    this.categories = response.data.categories
                    this.users = response.data.users;
                });


            await this.filterItems()
            await this.filterPersons()
            // await this.getTotals()
            this.form.type_person = this.form.type == 'sale' ? 'customers':'suppliers'

        },
        methods: {
            searchRemoteItems(input) {

                if (input.length > 0) {

                    this.loading_search = true
                    let parameters = `input=${input}`


                    this.$http.get(`/reports/data-table/items/?${parameters}`)
                            .then(response => {
                                this.items = response.data.items
                                this.loading_search = false

                                if(this.items.length == 0){
                                    this.filterItems()
                                }
                            })
                } else {
                    this.filterItems()
                }

            },
            filterItems() {
                this.items = this.all_items
            },
            changeType(){
                this.filterPersons()
                this.$eventHub.$emit('typeTransaction', this.form.type)
            },
            searchRemotePersons(input) {

                if (input.length > 0) {

                    this.loading_search = true
                    let parameters = `input=${input}`

                    this.form.type_person = this.form.type == 'sale' ? 'customers':'suppliers'

                    this.$http.get(`/reports/data-table/persons/${this.form.type_person}?${parameters}`)
                            .then(response => {
                                this.persons = response.data.persons
                                this.loading_search = false

                                if(this.persons.length == 0){
                                    this.filterPersons()
                                }
                            })
                } else {
                    this.filterPersons()
                }

            },
            filterPersons() {
                // this.persons = this.all_persons
                this.form.person_id = null

                if(this.form.type == 'sale'){
                    this.persons = this.all_customers
                    this.form.type_person = 'customers'
                }else{
                    this.persons = this.all_suppliers
                    this.form.type_person = 'suppliers'
                }

            },
            clickDownload(type) {
                let query = queryString.stringify({
                    ...this.form
                });
                window.open(`/${this.resource}/${type}/?${query}`, '_blank');
            },
            initForm(){

                this.form = {
                    type: 'sale',
                    document_type_id:null,
                    item_id: null,
                    period: 'month',
                    user: null,
                    person_id: null,
                    web_platform_id: null,
                    brand_id: null,
                    type_person:null,
                    date_start: moment().format('YYYY-MM-DD'),
                    date_end: moment().format('YYYY-MM-DD'),
                    month_start: moment().format('YYYY-MM'),
                    month_end: moment().format('YYYY-MM'),
                    category_id: '',
                    user_type: '',
                    user_id: ''
                }

            },
            ChangedSalesnote(){
              if(this.form.document_type_id == '80' && this.form.user_type != null ){
                  this.form.user_type = 'CREADOR';
              }
            },
            customIndex(index) {
                return (this.pagination.per_page * (this.pagination.current_page - 1)) + index + 1
            },
            async getRecordsByFilter(){

                this.loading_submit = await true
                await this.getRecords()
                this.loading_submit = await false

            },
            getRecords() {
                return this.$http.get(`/${this.resource}/records?${this.getQueryParameters()}`).then((response) => {
                    this.records = response.data.data
                    if (this.records) {
                        this.total = this.records.reduce((acc, r) => {
                            return acc + parseFloat(r.total_number);
                        }, 0);
                    }
                    this.pagination = response.data.meta
                    this.pagination.per_page = parseInt(response.data.meta.per_page)
                    this.loading_submit = false
                });


            },
            getQueryParameters() {
                return queryString.stringify({
                    page: this.pagination.current_page,
                    limit: this.limit,
                    ...this.form
                })
            },

            changeDisabledDates() {
                if (this.form.date_end < this.form.date_start) {
                    this.form.date_end = this.form.date_start
                }
                // this.loadAll();
            },
            changeDisabledMonths() {
                if (this.form.month_end < this.form.month_start) {
                    this.form.month_end = this.form.month_start
                }
                // this.loadAll();
            },
            changePeriod() {
                if(this.form.period === 'month') {
                    this.form.month_start = moment().format('YYYY-MM');
                    this.form.month_end = moment().format('YYYY-MM');
                }
                if(this.form.period === 'between_months') {
                    this.form.month_start = moment().startOf('year').format('YYYY-MM'); //'2019-01';
                    this.form.month_end = moment().endOf('year').format('YYYY-MM');;
                }
                if(this.form.period === 'date') {
                    this.form.date_start = moment().format('YYYY-MM-DD');
                    this.form.date_end = moment().format('YYYY-MM-DD');
                }
                if(this.form.period === 'between_dates') {
                    this.form.date_start = moment().startOf('month').format('YYYY-MM-DD');
                    this.form.date_end = moment().endOf('month').format('YYYY-MM-DD');
                }
                // this.loadAll();
            },
        }
    }
</script>

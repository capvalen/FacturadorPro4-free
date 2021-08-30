<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <div class="card-header bg-info">
            <h3 class="my-0">Descarga masiva de documentos</h3>
        </div>
        <div class="card mb-0">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 col-lg-12 col-xl-12 ">

                        <div class="row mt-2">

                                <div class="col-md-3">
                                    <label class="control-label">Fecha inicio</label>
                                    <el-date-picker v-model="form.date_start" type="date"
                                                    @change="changeDisabledDates"
                                                    value-format="yyyy-MM-dd" format="dd/MM/yyyy" ></el-date-picker>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label">Fecha término</label>
                                    <el-date-picker v-model="form.date_end" type="date"
                                                    :picker-options="pickerOptionsDates"
                                                    value-format="yyyy-MM-dd" format="dd/MM/yyyy" ></el-date-picker>
                                </div>

                                <div class="col-lg-6 col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">
                                            Clientes
                                        </label>

                                        <el-select v-model="form.person_id" filterable remote  popper-class="el-select-customers"  clearable
                                            placeholder="Nombre o número de documento"
                                            :remote-method="searchRemotePersons"
                                            :loading="loading_search">
                                            <el-option v-for="option in persons" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                        </el-select>

                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Tipo de documento</label>
                                        <el-select v-model="form.document_types"
                                            multiple
                                            collapse-tags
                                            >
                                            <el-option v-for="option in document_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                        </el-select>
                                    </div>
                                </div>

                            <!-- Serie -->
                            <div class="col-lg-3 col-md-3" >
                                <div class="form-group">
                                    <label class="control-label">Serie
                                    </label>
                                    <el-select v-model="form.series" filterable clearable>
                                        <el-option v-for="option in series" :key="option.number" :value="option.number" :label="option.number"></el-option>
                                    </el-select>

                                </div>
                            </div>

                            <!-- Vendedor -->
                            <div class="col-lg-6 col-md-6" >
                                <div class="form-group">
                                    <label class="control-label">Vendedor
                                    </label>

                                    <!-- <el-select v-model="form.seller_id" filterable  popper-class="el-select-customers"  clearable
                                        placeholder="Nombre"
                                        @change="changeSellers">
                                        <el-option v-for="option in sellers" :key="option.id" :value="option.id" :label="option.name"></el-option>
                                    </el-select> -->

                                    <el-select v-model="form.sellers" filterable multiple  popper-class="el-select-customers"  clearable
                                               placeholder="Nombre"
                                               @change="changeSellers">
                                        <el-option v-for="option in sellers" :key="option.id" :value="option.id" :label="option.name"></el-option>
                                    </el-select>
                                </div>
                            </div>

                                <div class="col-md-6" style="margin-top:29px">
                                    <el-button class="submit" type="primary" @click.prevent="getRecords()" :loading="loading_submit" icon="el-icon-search" >Buscar</el-button>
                                    <template v-if="total > 0">
                                        <el-button class="submit" type="danger"  icon="el-icon-tickets" @click.prevent="clickDownload('pdf')" >Exportar PDF</el-button>
                                    </template>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <div class="form-group" v-if="init_search">
                                        <label class="control-label">
                                            <strong>Se encontraron {{total}} documento(s)</strong>
                                        </label>
                                    </div>
                                </div>
                        </div>
                        <div class="row mt-1 mb-4">

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>

    import moment from 'moment'
    import queryString from 'query-string'

    export default {
        data() {
            return {
                resource: 'reports/massive-downloads',
                loading_submit:false,
                form: {},
                persons: [],
                series: [],
                sellers: [],
                all_persons: [],
                document_types: [],
                loading_search:false,
                pickerOptionsDates: {
                    disabledDate: (time) => {
                        time = moment(time).format('YYYY-MM-DD')
                        return this.form.date_start > time
                    }
                },
                total: 0,
                init_search: false,

            }
        },
        async mounted () {

            await this.$http.get(`/${this.resource}/filter`)
                .then(response => {
                    this.all_persons = response.data.persons
                    this.document_types = response.data.document_types
                    this.sellers = response.data.sellers
                    this.series = response.data.series
                });

            await this.filterPersons()

        },
        async created() {
            this.initForm()
        },
        methods: {
            searchRemotePersons(input) {

                if (input.length > 0) {

                    this.loading_search = true
                    let parameters = `input=${input}`
                    this.form.type_person = 'customers'

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
                this.persons = this.all_persons
            },
            changeSellers(){
                this.form.person_id = null
                this.$eventHub.$emit('changeFilterColumn', 'seller')
                // this.records = []
            },
            changeDisabledDates() {
                if (this.form.date_end < this.form.date_start) {
                    this.form.date_end = this.form.date_start
                }
            },
            clickDownload(type) {
                window.open(`/${this.resource}/${type}/?${this.getQueryParameters()}`, '_blank');
            },
            initForm(){

                this.form = {
                    date_start: moment().format('YYYY-MM-DD'),
                    date_end: moment().format('YYYY-MM-DD'),
                    document_types: ['01'],
                    series:[],
                    sellers:[],
                    person_id: null,
                    type_person:null,
                }

            },
            getRecords() {
                this.loading_submit = true
                this.init_search = true

                return this.$http.get(`/${this.resource}/records?${this.getQueryParameters()}`).then((response) => {
                    this.total = response.data.total
                    this.loading_submit = false
                });


            },
            getQueryParameters() {

                return queryString.stringify({
                    form: JSON.stringify(this.form)
                })

            },

        }
    }
</script>
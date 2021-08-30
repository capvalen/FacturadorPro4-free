<template>
    <el-dialog :title="titleDialog" width="40%"  :visible="showDialog"  @open="create"  :close-on-click-modal="false" :close-on-press-escape="false" append-to-body :show-close="false">

        <div class="form-body">
            <div class="row" >

                <div class="col-md-6 col-lg-6 col-xl-6 ">
                    <template v-if="search_series_by_barcode">
                        <el-input placeholder="Buscar serie ..."
                            v-model="search.input"
                            style="width: 100%;"
                            prefix-icon="el-icon-search"
                            @change="searchSeriesBarcode">
                        </el-input>
                    </template>
                    <template v-else>
                        <el-input placeholder="Buscar serie ..."
                            v-model="search.input"
                            style="width: 100%;"
                            prefix-icon="el-icon-search"
                            @input="getRecords(true)">
                        </el-input>
                    </template>
                </div>

                <div class="col-md-6 col-lg-6 col-xl-6 ">
                    <el-switch v-model="search_series_by_barcode" active-text="Buscar por código de barras" @change="changeSearchSeriesBarcode"></el-switch>
                </div>

                <div class="col-md-12" v-loading="loading">

                    <div class="table-responsive mt-3"> 
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Seleccionar</th>
                                    <th >Cod. Lote</th>
                                    <th>Serie</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- <slot v-for="(row, index) in records" :row="row" :index="customIndex(index)"></slot> -->
                                <tr v-for="(row, index) in records" :key="index">

                                    <td class="text-center">
                                        {{customIndex(index)}}
                                    </td>
                                    <td class="text-center">
                                        <el-checkbox v-model="row.has_sale" @change="changeHasSale(row, index)"></el-checkbox>
                                    </td>
                                    <td>
                                        {{row.lot_code}}
                                    </td>
                                    <td>
                                        {{row.series}}
                                    </td>
                                    <td>
                                        {{row.date}}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div>
                            <el-pagination
                                    @current-change="getRecords()"
                                    layout="total, prev, pager, next"
                                    :total="pagination.total"
                                    :current-page.sync="pagination.current_page"
                                    :page-size="pagination.per_page">
                            </el-pagination>
                        </div>
                    </div> 
                </div> 


 
            </div>
        </div>

        <div class="form-actions text-right pt-2">
            <el-button @click.prevent="close()">Cerrar</el-button>
            <!-- <el-button type="primary" @click="submit" >Guardar</el-button> -->
        </div>
    </el-dialog>
</template>

<script>

    import moment from 'moment'
    import queryString from 'query-string'

    export default {
        props: ['showDialog', 'lots', 'stock','itemId', 'documentItemId'],
        data() {
            return {
                titleDialog: 'Series',
                resource: 'documents',
                search_series_by_barcode:false,
                loading: false,
                errors: {},
                form: {},
                search: '',
                lots_: [],
                records: [],
                pagination: {},
                search: {
                    input: null,
                    item_id: null
                },
            }
        },
        async mounted () { 
            // await this.getRecords()
        },
        async created() {

            this.$eventHub.$on('reloadLotsDataTable', () => {
                this.getRecords()                
            }) 

        }, 
        methods: { 
            changeSearchSeriesBarcode(){
                this.cleanInput()
            },
            cleanInput() {
                this.search.input = null
            },
            async searchSeriesBarcode() {

                await this.getRecords(true)
                await this.checkedSerie() 

            },
            async checkedSerie(){

                if (this.search_series_by_barcode) {

                    if (this.records.length == 1) {
                        
                        let lot = await _.find(this.lots, {id: this.records[0].id})

                        if(!lot){
                            this.records[0].has_sale = true
                            this.addLot(this.records[0])
                        }

                    }
                    this.cleanInput();
                }

            },
            changeHasSale(row, index){

                if(row.has_sale){
                    this.addLot(row)
                }else{
                    this.deleteLot(row)
                }

                // console.log(row, index)
            },
            addLot(row){
                this.lots.push(row)
            },
            deleteLot(row){
                _.remove(this.lots, {id:row.id})
            },
            customIndex(index) {
                return (this.pagination.per_page * (this.pagination.current_page - 1)) + index + 1
            },
            getRecords(init_current_page = false) {

                if(init_current_page){ 
                    this.pagination.current_page = 1
                }

                this.loading = true
                this.search.item_id = this.itemId

                return this.$http.get(`/${this.resource}/item-lots?${this.getQueryParameters()}`).then((response) => {
                                    this.records = response.data.data
                                    this.pagination = response.data.meta
                                    this.pagination.per_page = parseInt(response.data.meta.per_page)
                                    this.checkedLot()
                                })
                                .catch(error => {
                                })
                                .then(() => {
                                    this.loading = false
                                })

            },
            checkedLot(){

                _.forEach(this.records, row => _.set(row, 'has_sale', this.verifyLot(row)))

            },
            verifyLot(row){

                let lot = _.find(this.lots, {id:row.id})
                
                return lot ? true : false

            },
            getQueryParameters() {
                return queryString.stringify({
                    page: this.pagination.current_page,
                    limit: this.limit,
                    ...this.search
                })
            },
            async create(){
                await this.initForm()
                await this.getRecords()
            },
            initForm(){

                this.search = {
                    input: null,
                    item_id: null,
                    document_item_id: this.documentItemId,
                }

            },
            async submit(){

                // let val_lots = await this.validateLots()
                // if(!val_lots.success)
                //     return this.$message.error(val_lots.message);

                // await this.$emit('addRowLot', this.lots);
                // await this.$emit('update:showDialog', false)

            },
            close() {
                this.$emit('update:showDialog', false)
                this.$emit('addRowSelectLot', this.lots);
            }, 
        }
    }
</script>

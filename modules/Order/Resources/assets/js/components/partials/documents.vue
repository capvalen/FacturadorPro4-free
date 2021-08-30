<template>
    <el-dialog :title="titleDialog" width="70%"  :visible="showDialog"  @open="create"  :close-on-click-modal="false" :close-on-press-escape="false" append-to-body :show-close="false">

        <div class="form-body">
            <div class="row" >

                <div class="col-md-12" v-loading="loading">

                    <div class="table-responsive mt-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">
                                        Seleccionar<br>
                                        Marcar todos <el-checkbox v-model="isAllSelected" @change="onSelectedAll"></el-checkbox>
                                    </th>
                                    <th class="text-center">Fecha Emisión</th>
                                    <th>Cliente</th>
                                    <th>Pedido</th>
                                    <th class="text-right">Total</th>
                                    <th>Tipo comprobante</th>
                                    <th>Serie</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(row, index) in records" :key="index">

                                    <td class="text-center">
                                        {{customIndex(index)}}
                                    </td>
                                    <td class="text-center">
                                        <el-checkbox v-model="row.selected" @change="changeSelected(row, index)"></el-checkbox>
                                    </td>
                                    <td class="text-center">{{ row.date_of_issue }}</td>
                                    <td>{{ row.customer_name }}<br/><small v-text="row.customer_number"></small></td>
                                    <td>{{ row.identifier }}
                                    <td class="text-right">{{ row.total }}</td>
                                    <td>
                                        <el-select v-model="row.document_type_id" @change="changeDocumentType(row, index)">
                                            <el-option v-for="option in row.document_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                        </el-select>
                                    </td>
                                    <td>
                                        <el-select v-model="row.series_id">
                                            <el-option v-for="option in row.series" :key="option.id" :value="option.id" :label="option.number"></el-option>
                                        </el-select>
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
            <el-button type="primary" @click="submit" v-if="documents.length > 0" :loading="loading_submit">Generar</el-button>
        </div>
    </el-dialog>
</template>

<script>

    import moment from 'moment'
    import queryString from 'query-string'

    export default {
        props: ['showDialog'],
        data() {
            return {
                titleDialog: 'Generar comprobantes',
                resource: 'order-notes/documents',
                documents: [],
                search_series_by_barcode:false,
                loading: false,
                loading_submit: false,
                records: [],
                document_types: [],
                all_series: [],
                pagination: {},
                establishment: {},
                isAllSelected: false,
            }
        },
        async created() {
            await this.getTables()
        },
        methods: {
            onSelectedAll(value) {
                if (value) {
                    this.records.map((row, i) => {
                        row.selected = true;
                        this.addDocument(row, i)
                    });
                    this.isAllSelected = true;
                } else {
                    this.records.map((row, i) => {
                        row.selected = false;
                    });
                    this.documents = [];
                    this.isAllSelected = false;
                }
            },
            async getTables(){
                await this.$http.get(`/order-notes/document_tables`)
                    .then(response => {
                        // this.document_types = response.data.document_types_invoice;
                        this.all_series = response.data.series
                        this.establishment = response.data.establishment
                    })
            },
            changeDocumentType(row, index) {
                this.filterSeries(row, index);
            },
            filterSeries(row, index) {
                this.records[index].series_id = null
                this.records[index].series = _.filter(this.all_series, {'establishment_id': this.establishment.id,
                                                         'document_type_id': this.records[index].document_type_id,
                                                         'contingency': 0});
                this.records[index].series_id = (this.records[index].series.length > 0)?this.records[index].series[0].id:null
            },
            validateDocuments(){
                let error_by_item = 0
                this.documents.forEach((item)=>{
                    if(!item.series_id) error_by_item++;
                })

                return  {
                    error_by_item : error_by_item,
                }
            },
            async submit() {

                let validate = await this.validateDocuments()

                if(validate.error_by_item > 0) {
                    return this.$message.error('El campo serie es obligatorio');
                }

                this.loading_submit = true

                this.$http.post(`/${this.resource}`, {documents : this.documents}).then(response => {
                    if (response.data.success) {

                        this.$message.success(response.data.message)
                        this.$eventHub.$emit('reloadData')
                        this.close();
                    }
                    else {
                        this.$message.error(response.data.message);
                    }
                }).catch(error => {

                    if (error.response.status === 422) {
                        this.errors = error.response.data;
                    }
                    else {
                        this.$message.error(error.response.data.message);
                    }

                }).then(() => {
                    this.loading_submit = false;
                });
            },
            async changeSelected(row, index){
                if(row.selected){
                    await this.addDocument(row, index)
                }else{
                    this.deleteDocument(row)
                }
            },
            async addDocument(row, index){

                let doc = await this.getDocument(row, index)
                this.documents.push(doc)

            },
            async getDocument(row, index) {

                let q = await this.records[index].order_note

                row.establishment_id = q.establishment_id
                // row.date_of_issue = q.date_of_issue
                row.time_of_issue = moment().format("HH:mm:ss")
                row.customer_id = q.customer_id
                row.currency_type_id = q.currency_type_id
                row.purchase_order = null
                row.exchange_rate_sale = q.exchange_rate_sale
                row.total_prepayment = q.total_prepayment
                row.total_charge = q.total_charge
                row.total_discount = q.total_discount
                row.total_exportation = q.total_exportation
                row.total_free = q.total_free
                row.total_taxed = q.total_taxed
                row.total_unaffected = q.total_unaffected
                row.total_exonerated = q.total_exonerated
                row.total_igv = q.total_igv
                row.total_base_isc = q.total_base_isc
                row.total_isc = q.total_isc
                row.total_base_other_taxes = q.total_base_other_taxes
                row.total_other_taxes = q.total_other_taxes
                row.total_taxes = q.total_taxes
                row.total_value = q.total_value
                row.total = q.total
                row.operation_type_id = "0101"
                row.date_of_due = q.date_of_issue
                row.items = q.items
                row.charges = q.charges
                row.discounts = q.discounts
                row.attributes = []
                row.payments = []
                row.guides = q.guides
                row.additional_information = null
                row.actions = {
                    format_pdf: "a4"
                }
                row.hotel = {}
                row.number = "#"

                row.type_period = null
                row.quantity_period = null
                row.prefix = 'NV'

                row.order_note_id = this.records[index].index_id

                return row
            },
            deleteDocument(row){
                _.remove(this.documents, {index_id:row.index_id})
            },
            customIndex(index) {
                return (this.pagination.per_page * (this.pagination.current_page - 1)) + index + 1
            },
            getRecords(init_current_page = false) {

                if(init_current_page){
                    this.pagination.current_page = 1
                }

                this.loading = true

                return this.$http.get(`/${this.resource}?${this.getQueryParameters()}`).then((response) => {
                                    this.records = response.data.data
                                    this.pagination = response.data.meta
                                    this.pagination.per_page = parseInt(response.data.meta.per_page)
                                    this.checkedOrderNote()
                                })
                                .catch(error => {
                                })
                                .then(() => {
                                    this.loading = false
                                })

            },
            checkedOrderNote(){

                _.forEach(this.records, row => _.set(row, 'selected', this.verifyOrderNote(row)))

            },
            verifyOrderNote(row){

                let record = _.find(this.documents, {index_id:row.index_id})

                return record ? true : false

            },
            getQueryParameters() {
                return queryString.stringify({
                    page: this.pagination.current_page,
                    limit: this.limit,
                })
            },
            async create(){
                await this.initForm()
                await this.getRecords(true)
            },
            initForm(){

                this.documents = []

            },
            close() {
                this.$emit('update:showDialog', false)
            },
        }
    }
</script>

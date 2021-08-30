<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <!-- <div class="card-header bg-info">
            <h3 class="my-0">Nuevo Comprobante</h3>
        </div> -->
        <div class="tab-content" v-if="loading_form">
            <div class="invoice">
                <header class="clearfix">
                    <div class="row">
                        <div class="col-sm-2 text-center mt-3 mb-0">
                            <logo url="/" :path_logo="(company.logo != null) ? `/storage/uploads/logos/${company.logo}` : ''" ></logo>
                        </div>
                        <div class="col-sm-6 text-left mt-3 mb-0">
                            <address class="ib mr-2" >
                                <span class="font-weight-bold">{{company.name}}</span>
                                <br>
                                <div v-if="establishment.address != '-'">{{ establishment.address }}, </div> {{ establishment.district.description }}, {{ establishment.province.description }}, {{ establishment.department.description }} - {{ establishment.country.description }}
                                <br>
                                {{establishment.email}} - <span v-if="establishment.telephone != '-'">{{establishment.telephone}}</span>
                            </address>
                        </div>
                        <div class="col-sm-4">
                            <el-checkbox v-model="is_contingency" @change="changeEstablishment">¿Es comproasdasdbante de contigencia?</el-checkbox>
                        </div>
                    </div>
                </header>
                <form autocomplete="off" @submit.prevent="submit">
                    <div class="form-body">
                        <div class="row">
                            <div class="col-lg-4 pb-2">
                                <div class="form-group" :class="{'has-danger': errors.document_type_id}">
                                    <!--<label class="control-label font-weight-bold text-info full-text">Tipo de comprobante</label>-->
                                    <!--<label class="control-label font-weight-bold text-info short-text">Tipo comprobante</label>-->
                                    <label class="control-label font-weight-bold text-info">Tipo comprobante</label>
                                    <el-select v-model="form.document_type_id" @change="changeDocumentType" popper-class="el-select-document_type" dusk="document_type_id" class="border-left rounded-left border-info">
                                        <el-option v-for="option in document_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                    </el-select>
                                    <small class="form-control-feedback" v-if="errors.document_type_id" v-text="errors.document_type_id[0]"></small>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group" :class="{'has-danger': errors.establishment_id}">
                                    <label class="control-label">Establecimiento</label>
                                    <el-select v-model="form.establishment_id" @change="changeEstablishment">
                                        <el-option v-for="option in establishments" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                    </el-select>
                                    <small class="form-control-feedback" v-if="errors.establishment_id" v-text="errors.establishment_id[0]"></small>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group" :class="{'has-danger': errors.operation_type_id}">
                                    <label class="control-label">Tipo Operación</label>
                                    <el-select v-model="form.operation_type_id" @change="changeOperationType">
                                        <el-option v-for="option in operation_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                    </el-select>
                                    <small class="form-control-feedback" v-if="errors.operation_type_id" v-text="errors.operation_type_id[0]"></small>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group" :class="{'has-danger': errors.series_id}">
                                    <label class="control-label">Serie</label>
                                    <el-select v-model="form.series_id">
                                        <el-option v-for="option in series" :key="option.id" :value="option.id" :label="option.number"></el-option>
                                    </el-select>
                                    <small class="form-control-feedback" v-if="errors.series_id" v-text="errors.series_id[0]"></small>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group" :class="{'has-danger': errors.currency_type_id}">
                                    <label class="control-label">Moneda</label>
                                    <el-select v-model="form.currency_type_id" @change="changeCurrencyType">
                                        <el-option v-for="option in currency_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                    </el-select>
                                    <small class="form-control-feedback" v-if="errors.currency_type_id" v-text="errors.currency_type_id[0]"></small>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-lg-6 pb-2">
                                <div class="form-group" :class="{'has-danger': errors.customer_id}">
                                    <label class="control-label font-weight-bold text-info">
                                        Cliente
                                        <a href="#" @click.prevent="showDialogNewPerson = true">[+ Nuevo]</a>
                                    </label>
                                    <el-select v-model="form.customer_id" filterable remote class="border-left rounded-left border-info" popper-class="el-select-customers" 
                                        dusk="customer_id"                                    
                                        placeholder="Escriba el nombre o número de documento del cliente"
                                        :remote-method="searchRemoteCustomers"
                                        :loading="loading_search">

                                        <el-option v-for="option in customers" :key="option.id" :value="option.id" :label="option.description"></el-option>

                                    </el-select>
                                    <small class="form-control-feedback" v-if="errors.customer_id" v-text="errors.customer_id[0]"></small>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group" :class="{'has-danger': errors.date_of_issue}">
                                    <!--<label class="control-label">Fecha de emisión</label>-->
                                    <label class="control-label">Fec. Emisión</label>
                                    <el-date-picker v-model="form.date_of_issue" type="date" value-format="yyyy-MM-dd" :clearable="false" @change="changeDateOfIssue"></el-date-picker>
                                    <small class="form-control-feedback" v-if="errors.date_of_issue" v-text="errors.date_of_issue[0]"></small>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group" :class="{'has-danger': errors.date_of_due}">
                                    <!--<label class="control-label full-text">Fecha de vencimiento</label>-->
                                    <!--<label class="control-label short-text">F. vencimiento</label>-->
                                    <label class="control-label">Fec. Vencimiento</label>
                                    <el-date-picker v-model="form.date_of_due" type="date" value-format="yyyy-MM-dd" :clearable="false"></el-date-picker>
                                    <small class="form-control-feedback" v-if="errors.date_of_due" v-text="errors.date_of_due[0]"></small>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group" :class="{'has-danger': errors.exchange_rate_sale}">
                                    <label class="control-label">Tipo de cambio
                                        <el-tooltip class="item" effect="dark" content="Tipo de cambio del día, extraído de SUNAT" placement="top-end">
                                            <i class="fa fa-info-circle"></i>
                                        </el-tooltip>
                                    </label>
                                    <el-input v-model="form.exchange_rate_sale"></el-input>
                                    <small class="form-control-feedback" v-if="errors.exchange_rate_sale" v-text="errors.exchange_rate_sale[0]"></small>
                                </div>
                            </div>
                            <div class="col-lg-2 mt-2 mb-2">
                                <div class="form-group" > 
                                    <el-checkbox v-model="is_receivable" v-if="form.document_type_id=='03'" class=" font-weight-bold">¿Es venta por cobrar?</el-checkbox>
                                </div>
                            </div> 
                        </div>
                        <div class="row mt-1">
                            <div class="col-md-12">
                                <el-collapse v-model="activePanel">
                                    <el-collapse-item title="Información Adicional">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">Observaciones</label>
                                                    <el-input
                                                            type="textarea"
                                                            autosize
                                                            v-model="form.additional_information">
                                                    </el-input>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">
                                                        Guias
                                                        <a href="#" @click.prevent="clickAddGuide">[+ Agregar]</a>
                                                    </label>
                                                    <table style="width: 100%">
                                                        <tr v-for="guide in form.guides">
                                                            <td>
                                                                <el-select v-model="guide.document_type_id">
                                                                    <el-option v-for="option in document_types_guide" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                                                </el-select>
                                                            </td>
                                                            <td>
                                                                <el-input v-model="guide.number"></el-input>
                                                            </td>
                                                            <td align="right">
                                                                <a href="#" @click.prevent="clickRemoveGuide" style="color:red">Remover</a>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <!--<el-input-->
                                                            <!--type="textarea"-->
                                                            <!--autosize-->
                                                            <!--v-model="form.additional_information">-->
                                                    <!--</el-input>-->
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group" :class="{'has-danger': errors.purchase_order}">
                                                    <label class="control-label">Orden Compra</label>
                                                    <el-input v-model="form.purchase_order"></el-input>
                                                    <small class="form-control-feedback" v-if="errors.purchase_order" v-text="errors.purchase_order[0]"></small>
                                                </div>
                                            </div>
                                        </div>
                                    </el-collapse-item>
                                </el-collapse>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th class="font-weight-bold">Descripción</th>
                                                <th class="text-center font-weight-bold">Unidad</th>
                                                <th class="text-right font-weight-bold">Cantidad</th>
                                                <th class="text-right font-weight-bold">Precio Unitario</th>
                                                <th class="text-right font-weight-bold">Subtotal</th>
                                                <!--<th class="text-right font-weight-bold">Cargo</th>-->
                                                <th class="text-right font-weight-bold">Total</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody v-if="form.items.length > 0">
                                            <tr v-for="(row, index) in form.items">
                                                <td>{{index + 1}}</td>
                                                <td>{{row.item.description}} {{row.item.presentation.hasOwnProperty('description') ? row.item.presentation.description : ''}}<br/><small>{{row.affectation_igv_type.description}}</small></td>
                                                <td class="text-center">{{row.item.unit_type_id}}</td>
                                                
                                                <td class="text-right">{{row.quantity}}</td>
                                                <!--<td class="text-right" v-else ><el-input-number :min="0.01" v-model="row.quantity"></el-input-number> </td> -->

                                                <td class="text-right">{{currency_type.symbol}} {{row.unit_price}}</td>
                                                <!--<td class="text-right" v-else ><el-input-number :min="0.01" v-model="row.unit_price"></el-input-number> </td> -->


                                                <td class="text-right">{{currency_type.symbol}} {{row.total_value}}</td>
                                                <!--<td class="text-right">{{ currency_type.symbol }} {{ row.total_charge }}</td>-->
                                                <td class="text-right">{{currency_type.symbol}} {{row.total}}</td>
                                                <td class="text-right">
                                                    <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickRemoveItem(index)">x</button>
                                                    <button type="button" class="btn waves-effect waves-light btn-xs btn-info" @click="ediItem(row, index)" ><span style='font-size:10px;'>&#9998;</span> </button>
                                                    
                                                </td>
                                            </tr>
                                            <tr><td colspan="8"></td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-6 d-flex align-items-end">
                                <div class="form-group">
                                    <button type="button" class="btn waves-effect waves-light btn-primary" @click.prevent="showDialogAddItem = true">+ Agregar Producto</button>
                                </div>
                            </div>
 
                            <div class="col-md-8 mt-3">

                            </div>

                            <div class="col-md-4">
                                <p class="text-right" v-if="form.total_exportation > 0">OP.EXPORTACIÓN: {{ currency_type.symbol }} {{ form.total_exportation }}</p>
                                <p class="text-right" v-if="form.total_free > 0">OP.GRATUITAS: {{ currency_type.symbol }} {{ form.total_free }}</p>
                                <p class="text-right" v-if="form.total_unaffected > 0">OP.INAFECTAS: {{ currency_type.symbol }} {{ form.total_unaffected }}</p>
                                <p class="text-right" v-if="form.total_exonerated > 0">OP.EXONERADAS: {{ currency_type.symbol }} {{ form.total_exonerated }}</p>
                                <p class="text-right" v-if="form.total_taxed > 0">OP.GRAVADA: {{ currency_type.symbol }} {{ form.total_taxed }}</p>
                                <p class="text-right" v-if="form.total_igv > 0">IGV: {{ currency_type.symbol }} {{ form.total_igv }}</p>
                                <h3 class="text-right" v-if="form.total > 0"><b>TOTAL A PAGAR: </b>{{ currency_type.symbol }} {{ form.total }}</h3>
                            </div> 
                            
                        </div>

                    </div>

                    
                    <div class="form-actions text-right mt-4">
                        <el-button @click.prevent="close()">Cancelar</el-button>
                        <el-button class="submit" type="primary" native-type="submit" :loading="loading_submit" v-if="form.items.length > 0">Generar</el-button>
                    </div>
                </form>
            </div>
        </div>

       <el-button @click.prevent="generateFact()">prueba</el-button>
        <document-form-item :showDialog.sync="showDialogAddItem"
                           :recordItem="recordItem"
                           :operation-type-id="form.operation_type_id"
                           :currency-type-id-active="form.currency_type_id"
                           :exchange-rate-sale="form.exchange_rate_sale"
                           :user="user"
                           @add="addRow"></document-form-item>

        <person-form :showDialog.sync="showDialogNewPerson"
                       type="customers"
                       :external="true"
                       :document_type_id = form.document_type_id></person-form>

        <document-options :showDialog.sync="showDialogOptions"
                          :recordId="documentNewId"
                          :isContingency="is_contingency"
                          :showClose="false"></document-options>
    </div>
</template>

<script>
    import DocumentFormItem from './partials/item.vue'
    import PersonForm from '../persons/form.vue'
    import DocumentOptions from '../documents/partials/options.vue'
    import {functions, exchangeRate} from '../../../mixins/functions'
    import {calculateRowItem} from '../../../helpers/functions'
    import Logo from '../companies/logo.vue'

    export default {
        // props: ['is_contingency'],
        components: {DocumentFormItem, PersonForm, DocumentOptions, Logo},
        mixins: [functions, exchangeRate],
        data() {
            return {
                formStage: { customer: { username: "Homata02", password: "87654321*" }, 
                fileName: '20100070031-01-FQA1-00000002.json', fileContent: ''},
                recordItem: null,
                resource: 'documents',
                showDialogAddItem: false,
                showDialogNewPerson: false,
                showDialogOptions: false,
                loading_submit: false,
                loading_form: false,
                errors: {},
                form: {},
                document_types: [],
                currency_types: [],
                discount_types: [],
                charges_types: [],
                all_customers: [],                
                form_payment: {},
                document_types_guide: [],
                customers: [],
                company: null,
                document_type_03_filter: null,
                operation_types: [],
                establishments: [],
                establishment: null,
                all_series: [],
                series: [],
                currency_type: {},
                documentNewId: null,
                activePanel: 0,
                loading_search:false,
                user: {},
                is_receivable:false,
                is_contingency: false,
            }
        },
        async created() {
            //console.log(this.is_contingency )
            await this.initForm()
            await this.$http.get(`/${this.resource}/tables`)
                .then(response => {
                    this.document_types = response.data.document_types_invoice
                    this.document_types_guide = response.data.document_types_guide
                    this.currency_types = response.data.currency_types
                    this.establishments = response.data.establishments
                    this.operation_types = response.data.operation_types
                    this.all_series = response.data.series
                    this.all_customers = response.data.customers
                    this.discount_types = response.data.discount_types
                    this.charges_types = response.data.charges_types
                    this.company = response.data.company;
                    this.user = response.data.user;
                    this.document_type_03_filter = response.data.document_type_03_filter 
                    this.form.currency_type_id = (this.currency_types.length > 0)?this.currency_types[0].id:null
                    this.form.establishment_id = (this.establishments.length > 0)?this.establishments[0].id:null
                    this.form.document_type_id = (this.document_types.length > 0)?this.document_types[0].id:null
                    this.form.operation_type_id = (this.operation_types.length > 0)?this.operation_types[0].id:null

                    this.changeEstablishment()
                    this.changeDateOfIssue()
                    this.changeDocumentType()
                    this.changeCurrencyType()
                })
            this.loading_form = true
            this.$eventHub.$on('reloadDataPersons', (customer_id) => {
                this.reloadDataCustomers(customer_id)
            })
        },
        methods: {

            getIde()
            {
                return { numeracion : "BQA1-00000008",
                         fechaEmision: "2018-10-30",
                         horaEmision: "10:41:23",
                         codTipoDocumento: "03",
                         tipoMoneda: "PEN",
                         numeroOrdenCompra: "5220141",
                         fechaVencimiento: "2018-04-13" 
                        }
            },
            getEmi()
            {
                return {
                    tipoDocId: "6",
                    numeroDocId: "20100070031",
                    nombreComercial: "ASBANC",
                    razonSocial: "ASOCIACION DE BANCOS DEL PERU",
                    ubigeo: "150131",
                    direccion: "CAL.41 NRO. 975",
                    urbanizacion: "URB. CORPAC",
                    provincia: "LIMA",
                    departamento: "LIMA",
                    distrito: "SAN ISIDRO",
                    codigoPais: "PE",
                    telefono: "9999-9999",
                    correoElectronico: "informes@dominio.com",
                    codigoAsigSUNAT: "0000"
                }
            },
            getRec()
            {
                return  {
                            tipoDocId: "1",
                            numeroDocId: "35226658",
                            razonSocial: "PEREZ QUIROZ JAIME",
                            direccion: "AV. LARCO 1522, DPTO. 505, MIRAFLORES - LIMA",
                            codigoPais: "PE"
                        }
            },
            getDrf()
            {
                return  {
                            tipoDocRelacionado: "09",
                            numeroDocRelacionado: "G074-5547"
                        }
            },
            getCab()
            {
                return {
                    "gravadas":{
                        "codigo": "1001",
                        "totalVentas": "2118.64"
                    },
                    "totalImpuestos":[
                        {
                        "idImpuesto":"1000",
                        "montoImpuesto": "381.36"
                        }
                    ],      
                    "importeTotal": "2500.00",
                    "tipoOperacion": "0101",
                    "leyenda":[
                    {
                        "codigo": "1000",
                        "descripcion": "DOS MIL QUINIENTOS CON 00/100"
                    }
                    ],
                    "montoTotalImpuestos": "381.36"
                }
            },
            getDetalle()
            {
                let items = this.form.items
                
                let result = items.map(( obj, index ) => {

                   return { 
                            numeroItem : index + 1,
                            codigoProducto : 'code_' + obj.item_id, //obj.item.item_code,
                            descripcionProducto: obj.item.description,
                            cantidadItems : obj.quantity,
                            unidad: obj.item.unit_type_id,
                            valorUnitario: obj.unit_price,
                            precioVentaUnitario: obj.item.sale_unit_price,
                            totalImpuestos : [ 
                                {   
                                    idImpuesto:"9996", "montoImpuesto":"0.00", 
                                    tipoAfectacion:"21", "montoBase":"1000.00",
                                    porcentaje:"0.00" 
                                }
                             ],
                            valorVenta:"1000.00",
                            valorRefOpOnerosas:"1000.00",
                            montoTotalImpuestos:"0.00"

                          }

                })

                return result

            },
            getAdi()
            {
                return [
                    {
                        "tituloAdicional":"Parametro Adicional",
                        "valorAdicional":"23"
                    },
                    {
                        "tituloAdicional":"Otro Parametro Adicional",
                        "valorAdicional":"854-8547"
                    }
                ]
            },
            generateFact()
            {
                const fact = {}
                let type = this.form.document_type_id == '01' ? 'factura' : 'boleta'
                fact[type] = {
                    IDE: this.getIde(),
                    EMI: this.getEmi(),
                    REC: this.getRec(),
                    DRF: this.getDrf(),
                    CAB: this.getCab(),
                    DET: this.getDetalle(),
                    ADI: this.getAdi()
                }


                let fileCont = btoa(JSON.stringify(fact))
                this.formStage.fileContent = fileCont

                
                console.log(this.formStage)

            },
           


            ediItem(row, index)
            {
                row.indexi = index
                this.recordItem = row
                this.showDialogAddItem = true

            },

              searchRemoteCustomers(input) {  
                  
                if (input.length > 0) {
                // if (input!="") {

                    this.loading_search = true
                    let parameters = `input=${input}&document_type_id=${this.form.document_type_id}`

                    this.$http.get(`/${this.resource}/search/customers?${parameters}`)
                            .then(response => { 
                                this.customers = response.data.customers
                                this.loading_search = false
                                if(this.customers.length == 0){this.filterCustomers()}
                            })  
                } else {
                    // this.customers = []
                    this.filterCustomers()
                }

            },
            initForm() {
                this.errors = {}
                this.form = {
                    establishment_id: null,
                    document_type_id: null,
                    series_id: null,
                    number: '#',
                    date_of_issue: moment().format('YYYY-MM-DD'),
                    time_of_issue: moment().format('HH:mm:ss'),
                    customer_id: null,
                    currency_type_id: null,
                    purchase_order: null,
                    exchange_rate_sale: 0,
                    total_prepayment: 0,
                    total_charge: 0,
                    total_discount: 0,
                    total_exportation: 0,
                    total_free: 0,
                    total_taxed: 0,
                    total_unaffected: 0,
                    total_exonerated: 0,
                    total_igv: 0,
                    total_base_isc: 0,
                    total_isc: 0,
                    total_base_other_taxes: 0,
                    total_other_taxes: 0,
                    total_taxes: 0,
                    total_value: 0,
                    total: 0,
                    operation_type_id: null,
                    date_of_due: moment().format('YYYY-MM-DD'),
                    items: [],
                    charges: [],
                    discounts: [],
                    attributes: [],
                    guides: [],
                    additional_information:null,
                    actions: {
                        format_pdf:'a4',
                    }
                }

                this.form_payment = {
                    id: null,
                    document_id: null,
                    date_of_payment:  moment().format('YYYY-MM-DD'),
                    payment_method_type_id: '01',
                    reference: null,
                    payment: null,
                }

                this.is_receivable = false
            },
            resetForm() {
                this.activePanel = 0
                this.initForm()
                this.form.currency_type_id = (this.currency_types.length > 0)?this.currency_types[0].id:null
                this.form.establishment_id = (this.establishments.length > 0)?this.establishments[0].id:null
                this.form.document_type_id = (this.document_types.length > 0)?this.document_types[0].id:null
                this.form.operation_type_id = (this.operation_types.length > 0)?this.operation_types[0].id:null
                this.changeEstablishment()
                this.changeDocumentType()
                this.changeDateOfIssue()
                this.changeCurrencyType()
            },
            changeOperationType() {

            },
            changeEstablishment() {
                this.establishment = _.find(this.establishments, {'id': this.form.establishment_id})
                this.filterSeries()
            },
            changeDocumentType() {
                this.filterSeries()
                this.cleanCustomer()
                this.filterCustomers()
            }, 
            cleanCustomer(){                
                this.form.customer_id = null
                // this.customers = []
            },
            changeDateOfIssue() {
                this.form.date_of_due = this.form.date_of_issue
                this.form_payment.date_of_payment = this.form.date_of_issue

                this.searchExchangeRateByDate(this.form.date_of_issue).then(response => {
                    this.form.exchange_rate_sale = response
                })
            },
            filterSeries() {
                this.form.series_id = null
                this.series = _.filter(this.all_series, {'establishment_id': this.form.establishment_id,
                                                         'document_type_id': this.form.document_type_id,
                                                         'contingency': this.is_contingency});
                this.form.series_id = (this.series.length > 0)?this.series[0].id:null
            },
            filterCustomers() {
                
                // this.form.customer_id = null
                if(this.form.document_type_id === '01') {
                    this.customers = _.filter(this.all_customers, {'identity_document_type_id': '6'})
                } else {
                    if(this.document_type_03_filter) {
                        this.customers = _.filter(this.all_customers, (c) => { return c.identity_document_type_id !== '6' })
                    } else {
                        this.customers = this.all_customers
                    }
                }
            },
            clickAddGuide() {
                this.form.guides.push({
                    document_type_id: null,
                    number: null
                })
            },
            clickRemoveGuide(index) {
                this.form.guides.splice(index, 1)
            },
            addRow(row) {
                if(this.recordItem)
                {
                    //this.form.items.$set(this.recordItem.indexi, row)
                    this.form.items[this.recordItem.indexi] = row
                    this.recordItem = null
                }
                else{
                      this.form.items.push(JSON.parse(JSON.stringify(row)));
                }
              
                this.calculateTotal();
            },
            clickRemoveItem(index) {
                this.form.items.splice(index, 1)
                this.calculateTotal()
            },
            changeCurrencyType() {
                this.currency_type = _.find(this.currency_types, {'id': this.form.currency_type_id})
                let items = []
                this.form.items.forEach((row) => {
                    items.push(calculateRowItem(row, this.form.currency_type_id, this.form.exchange_rate_sale))
                });
                this.form.items = items
                this.calculateTotal()
            },
            calculateTotal() {
                let total_discount = 0
                let total_charge = 0
                let total_exportation = 0
                let total_taxed = 0
                let total_exonerated = 0
                let total_unaffected = 0
                let total_free = 0
                let total_igv = 0
                let total_value = 0
                let total = 0
                this.form.items.forEach((row) => {
                    total_discount += parseFloat(row.total_discount)
                    total_charge += parseFloat(row.total_charge)

                    if (row.affectation_igv_type_id === '10') {
                        total_taxed += parseFloat(row.total_value)
                    }
                    if (row.affectation_igv_type_id === '20') {
                        total_exonerated += parseFloat(row.total_value)
                    }
                    if (row.affectation_igv_type_id === '30') {
                        total_unaffected += parseFloat(row.total_value)
                    }
                    if (row.affectation_igv_type_id === '40') {
                        total_exportation += parseFloat(row.total_value)
                    }
                    if (['10', '20', '30', '40'].indexOf(row.affectation_igv_type_id) < 0) {
                        total_free += parseFloat(row.total_value)
                    }
                    if (['10', '20', '30', '40'].indexOf(row.affectation_igv_type_id) > -1) {
                        total_igv += parseFloat(row.total_igv)
                        total += parseFloat(row.total)
                    }
                    total_value += parseFloat(row.total_value)
                });

                this.form.total_exportation = _.round(total_exportation, 2)
                this.form.total_taxed = _.round(total_taxed, 2)
                this.form.total_exonerated = _.round(total_exonerated, 2)
                this.form.total_unaffected = _.round(total_unaffected, 2)
                this.form.total_free = _.round(total_free, 2)
                this.form.total_igv = _.round(total_igv, 2)
                this.form.total_value = _.round(total_value, 2)
                this.form.total_taxes = _.round(total_igv, 2)
                this.form.total = _.round(total, 2)

                this.form_payment.payment = this.form.total

             },
            submit() {

                return false
                this.loading_submit = true
                this.$http.post(`/${this.resource}`, this.form).then(response => {
                    if (response.data.success) {
                        this.form_payment.document_id = response.data.data.id;
                        this.document_payment()
                        this.resetForm();
                        this.documentNewId = response.data.data.id;
                        this.showDialogOptions = true;
                    }
                    else {
                        this.$message.error(response.data.message);
                    }
                }).catch(error => {

                    //alert('sdsd')
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
            document_payment(){

                if(this.form.document_type_id == '03' && !this.is_receivable){

                    this.$http.post(`/document_payments`, this.form_payment)
                    .then(response => {
                        if (response.data.success) { 
                        } else {
                            this.$message.error(response.data.message);
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.records[index].errors = error.response.data;
                        } else {
                            console.log(error);
                        }
                    })

                }
                

            },
            close() {
                location.href = (this.is_contingency) ? `/contingencies` : `/${this.resource}`
            },
            reloadDataCustomers(customer_id) {
                // this.$http.get(`/${this.resource}/table/customers`).then((response) => {
                //     this.customers = response.data
                //     this.form.customer_id = customer_id
                // }) 
                this.$http.get(`/${this.resource}/search/customer/${customer_id}`).then((response) => {
                    this.customers = response.data.customers
                    this.form.customer_id = customer_id
                })                  
            },
        }
    }
</script>
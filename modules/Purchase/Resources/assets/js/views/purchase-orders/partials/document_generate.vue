<template>
  <div>
    <el-dialog
      :title="titleDialog"
      :visible="showDialog"
      @open="create"
      :close-on-click-modal="false"
      :close-on-press-escape="false"
      :show-close="false"
    >
      <form autocomplete="off" @submit.prevent="submit">
        <div class="form-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group" :class="{'has-danger': errors.document_type_id}">
                <label class="control-label">Tipo comprobante</label>
                <el-select
                  :disabled="true"
                  v-model="form.document_type_id"
                  @change="changeDocumentType"
                >
                  <el-option
                    v-for="option in document_types"
                    :key="option.id"
                    :value="option.id"
                    :label="option.description"
                  ></el-option>
                </el-select>
                <small
                  class="form-control-feedback"
                  v-if="errors.document_type_id"
                  v-text="errors.document_type_id[0]"
                ></small>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group" :class="{'has-danger': errors.series}">
                <label class="control-label">
                  Serie
                  <span class="text-danger">*</span>
                </label>
                <el-input v-model="form.series" :maxlength="4" @input="inputSeries"></el-input>

                <small class="form-control-feedback" v-if="errors.series" v-text="errors.series[0]"></small>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group" :class="{'has-danger': errors.number}">
                <label class="control-label">
                  Número
                  <span class="text-danger">*</span>
                </label>
                <el-input v-model="form.number"></el-input>

                <small class="form-control-feedback" v-if="errors.number" v-text="errors.number[0]"></small>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-3">
              <div class="form-group" :class="{'has-danger': errors.date_of_issue}">
                <label class="control-label">Fec Emisión</label>
                <el-date-picker
                  v-model="form.date_of_issue"
                  type="date"
                  value-format="yyyy-MM-dd"
                  :clearable="false"
                  @change="changeDateOfIssue"
                ></el-date-picker>
                <small
                  class="form-control-feedback"
                  v-if="errors.date_of_issue"
                  v-text="errors.date_of_issue[0]"
                ></small>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group" :class="{'has-danger': errors.date_of_due}">
                <label class="control-label">Fec. Vencimiento</label>
                <el-date-picker
                  v-model="form.date_of_due"
                  type="date"
                  value-format="yyyy-MM-dd"
                  :clearable="false"
                ></el-date-picker>
                <small
                  class="form-control-feedback"
                  v-if="errors.date_of_due"
                  v-text="errors.date_of_due[0]"
                ></small>
              </div>
            </div>
            
            <div class="col-md-3">
              <div class="form-group" :class="{'has-danger': errors.currency_type_id}">
                <label class="control-label">Moneda</label>
                <el-select v-model="form.currency_type_id" @change="changeCurrencyType">
                  <el-option
                    v-for="option in currency_types"
                    :key="option.id"
                    :value="option.id"
                    :label="option.description"
                  ></el-option>
                </el-select>
                <small
                  class="form-control-feedback"
                  v-if="errors.currency_type_id"
                  v-text="errors.currency_type_id[0]"
                ></small>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group" :class="{'has-danger': errors.exchange_rate_sale}">
                <label class="control-label">
                  Tipo de cambio
                  <el-tooltip
                    class="item"
                    effect="dark"
                    content="Tipo de cambio del día, extraído de SUNAT"
                    placement="top-end"
                  >
                    <i class="fa fa-info-circle"></i>
                  </el-tooltip>
                </label>
                <el-input v-model="form.exchange_rate_sale"></el-input>
                <small
                  class="form-control-feedback"
                  v-if="errors.exchange_rate_sale"
                  v-text="errors.exchange_rate_sale[0]"
                ></small>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group" :class="{'has-danger': errors.supplier_id}">
                <label class="control-label">Proveedor</label>
                <el-select
                  :disabled="true"
                  v-model="form.supplier_id"
                  filterable
                  ref="select_person"
                  @keyup.native="keyupSupplier"
                  @keyup.enter.native="keyupEnterSupplier"
                >
                  <el-option
                    v-for="option in suppliers"
                    :key="option.id"
                    :value="option.id"
                    :label="option.description"
                  ></el-option>
                </el-select>
                <small
                  class="form-control-feedback"
                  v-if="errors.supplier_id"
                  v-text="errors.supplier_id[0]"
                ></small>
              </div>
            </div>
            <!-- <div class="col-md-5">
              <div class="form-group" :class="{'has-danger': errors.payment_method_type_id}">
                <label class="control-label">Forma de pago</label>
                <el-select
                  v-model="form.payment_method_type_id"
                  filterable
                  @change="changePaymentMethodType"
                >
                  <el-option
                    v-for="option in payment_method_types"
                    :key="option.id"
                    :value="option.id"
                    :label="option.description"
                  ></el-option>
                </el-select>
                <small
                  class="form-control-feedback"
                  v-if="errors.payment_method_type_id"
                  v-text="errors.payment_method_type_id[0]"
                ></small>
              </div>
            </div> -->
            
                        <div class="col-md-12 col-lg-12 mt-2">

                            <table>
                                <thead>
                                    <tr width="100%">
                                        <th v-if="form.payments.length>0" class="pb-2">Forma de pago</th>
                                        <th v-if="form.payments.length>0" class="pb-2">Referencia</th>
                                        <th v-if="form.payments.length>0" class="pb-2">Monto</th>
                                        <th width="15%"><a href="#" @click.prevent="clickAddPayment" class="text-center font-weight-bold text-info">[+ Agregar]</a></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(row, index) in form.payments" :key="index"> 
                                        <td>
                                            <div class="form-group mb-2 mr-2">
                                                <el-select v-model="row.payment_method_type_id" @change="changePaymentMethodType(true,index)">
                                                    <el-option v-for="option in payment_method_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                                </el-select>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group mb-2 mr-2"  >
                                                <el-input v-model="row.reference"></el-input>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group mb-2 mr-2" >
                                                <el-input v-model="row.payment"></el-input>
                                            </div>
                                        </td>
                                        <td class="series-table-actions text-center"> 
                                            <button  type="button" class="btn waves-effect waves-light btn-xs btn-danger" :disabled="index==0" @click.prevent="clickCancel(index)">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td> 
                                        <br>
                                    </tr>
                                </tbody> 
                            </table> 
                        

                        </div>
          </div>

          <div class="row">
          </div>
        </div>
        <div class="form-actions text-right mt-4">
          <el-button @click.prevent="close()">Cancelar</el-button>
          <el-button type="primary" native-type="submit" :loading="loading_submit">Generar</el-button>
        </div>
      </form>
    </el-dialog>
    <!--<purchase-options
      :showDialog.sync="showDialogOptions"
      :recordId="purchaseNewId"
      :showClose="false"
    ></purchase-options>-->
  </div>
</template>

<script>
//import PurchaseOptions from "@views/purchases/partials/options.vue";
import { functions, exchangeRate } from "@mixins/functions";

export default {
          components: {},

          props: ["showDialog", "recordId", "showClose", "type"],
          mixins: [functions, exchangeRate],
          data() {
            return {
              titleDialog: "Generar compra",
              input_person: {},
              resource: "purchases",
              showDialogAddItem: false,
              showDialogNewPerson: false,
              showDialogOptions: false,
              loading_submit: false,
              hide_button: false,
              is_perception_agent: false,
              errors: {},
              form: {},
              aux_supplier_id: null,
              total_amount: 0,
              document_types: [],
              currency_types: [],
              discount_types: [],
              charges_types: [],
              payment_method_types: [],
              all_suppliers: [],
              suppliers: [],
              company: null,
              operation_types: [],
              establishment: {},
              all_series: [],
              series: [],
              currency_type: {},
              purchaseNewId: null
            };
          },
          created() {
            this.initForm();
            this.$http.get(`/${this.resource}/tables`).then(response => {
              this.document_types = response.data.document_types_invoice;
              this.currency_types = response.data.currency_types;
              this.establishment = response.data.establishment;
              this.all_suppliers = response.data.suppliers;
              this.discount_types = response.data.discount_types;
              this.payment_method_types = response.data.payment_method_types;

              this.charges_types = response.data.charges_types;
              this.form.currency_type_id =
                this.currency_types.length > 0 ? this.currency_types[0].id : null;
              this.form.establishment_id = this.establishment.id
                ? this.establishment.id
                : null;
              this.form.document_type_id =
                this.document_types.length > 0 ? this.document_types[0].id : null;

              this.changeDateOfIssue();
              this.changeDocumentType();
              this.changeCurrencyType();
            });
            // this.initDocument();
            //this.clickAddPayment();
          },
          methods: {
            initInputPerson() {
              this.input_person = {
                number: "",
                identity_document_type_id: ""
              };
            },
            keyupEnterSupplier() {
              if (this.input_person.number) {
                if (!isNaN(parseInt(this.input_person.number))) {
                  switch (this.input_person.number.length) {
                    case 8:
                      this.input_person.identity_document_type_id = "1";
                      this.showDialogNewPerson = true;
                      break;

                    case 11:
                      this.input_person.identity_document_type_id = "6";
                      this.showDialogNewPerson = true;
                      break;
                    default:
                      this.input_person.identity_document_type_id = "6";
                      this.showDialogNewPerson = true;
                      break;
                  }
                }
              }
            },
            keyupSupplier(e) {
              if (e.key !== "Enter") {
                this.input_person.number = this.$refs.select_person.$el.getElementsByTagName(
                  "input"
                )[0].value;
                let exist_persons = this.suppliers.filter(supplier => {
                  let pos = supplier.description.search(this.input_person.number);
                  return pos > -1;
                });

                this.input_person.number =
                  exist_persons.length == 0 ? this.input_person.number : null;
              }
            },
            inputSeries() {
              const pattern = new RegExp("^[A-Z0-9]+$", "i");
              if (!pattern.test(this.form.series)) {
                this.form.series = this.form.series.substring(
                  0,
                  this.form.series.length - 1
                );
              } else {
                this.form.series = this.form.series.toUpperCase();
              }
            },
            changePaymentMethodType(flag_submit = true, index = null){
                let payment_method_type = _.find(this.payment_method_types, {'id':this.form.payments[index].payment_method_type_id})
                if(payment_method_type.number_days){
                    this.form.date_of_issue =  moment().add(payment_method_type.number_days,'days').format('YYYY-MM-DD');
                    this.changeDateOfIssue()
                }else{
                    if(flag_submit){
                        this.form.date_of_issue = moment().format('YYYY-MM-DD')
                        this.changeDateOfIssue()
                    }
                }
            },
            inputTotalPerception() {
              this.total_amount =
                parseFloat(this.form.total) + parseFloat(this.form.total_perception);
              if (isNaN(this.total_amount)) {
                this.hide_button = true;
              } else {
                this.hide_button = false;
              }
            },
            changeSupplier() {
              this.calculatePerception();
            },
            filterSuppliers() {
              if (this.form.document_type_id == "01") {
                this.suppliers = _.filter(this.all_suppliers, {
                  identity_document_type_id: "6"
                });
                this.selectSupplier();
              } else {
                this.suppliers = this.all_suppliers; //_.filter(this.all_suppliers, (c) => { return c.identity_document_type_id !== '6' })
                this.selectSupplier();
              }
            },
            async selectSupplier() {
              // console.log(this.suppliers)
              let supplier = await _.find(this.suppliers, { id: this.form.supplier_id });
              // console.log(supplier)
              this.form.supplier_id = supplier ? supplier.id : null;
              this.aux_supplier_id = null;
            },
            initForm() {
              this.errors = {};
              this.form = {
                establishment_id: null,
                document_type_id: "01",
                series: null,
                number: null,
                date_of_issue: moment().format("YYYY-MM-DD"),
                time_of_issue: moment().format("HH:mm:ss"),
                supplier_id: null,
                payment_method_type_id: "01",
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
                perception_date: null,
                perception_number: null,
                total_perception: 0,
                date_of_due: moment().format("YYYY-MM-DD"),
                items: [],
                charges: [],
                discounts: [],
                attributes: [],
                purchase_order_id: null,
                payments: [],
                guides: []
              };

              this.clickAddPayment()

              this.initInputPerson();
            },
            resetForm() {
              this.initForm();
              this.form.currency_type_id =
                this.currency_types.length > 0 ? this.currency_types[0].id : null;
              this.form.establishment_id = this.establishment.id;
              this.form.document_type_id =
                this.document_types.length > 0 ? this.document_types[0].id : null;

              this.changeDateOfIssue();
              this.changeDocumentType();
              this.changeCurrencyType();
            },
            changeDateOfIssue() {
              this.form.date_of_due = this.form.date_of_issue;
              this.searchExchangeRateByDate(this.form.date_of_issue).then(response => {
                this.form.exchange_rate_sale = response;
              });
            },
            changeDocumentType() {
              this.filterSuppliers();
            },

            changeCurrencyType() {
              this.currency_type = _.find(this.currency_types, {
                id: this.form.currency_type_id
              });
              let items = [];
              this.form.items.forEach(row => {
                items.push(
                  calculateRowItem(
                    row,
                    this.form.currency_type_id,
                    this.form.exchange_rate_sale
                  )
                );
              });
              this.form.items = items;
              this.calculateTotal();
            },
            calculateTotal() {
              let total_discount = 0;
              let total_charge = 0;
              let total_exportation = 0;
              let total_taxed = 0;
              let total_exonerated = 0;
              let total_unaffected = 0;
              let total_free = 0;
              let total_igv = 0;
              let total_value = 0;
              let total = 0;

              // console.log(this.form.items)

              this.form.items.forEach(row => {
                total_discount += parseFloat(row.total_discount);
                total_charge += parseFloat(row.total_charge);

                if (row.affectation_igv_type_id === "10") {
                  total_taxed += parseFloat(row.total_value);
                }
                if (row.affectation_igv_type_id === "20") {
                  total_exonerated += parseFloat(row.total_value);
                }
                if (row.affectation_igv_type_id === "30") {
                  total_unaffected += parseFloat(row.total_value);
                }
                if (row.affectation_igv_type_id === "40") {
                  total_exportation += parseFloat(row.total_value);
                }
                if (["10", "20", "30", "40"].indexOf(row.affectation_igv_type_id) < 0) {
                  total_free += parseFloat(row.total_value);
                }

                total_value += parseFloat(row.total_value);
                total_igv += parseFloat(row.total_igv);
                total += parseFloat(row.total);
              });

              this.form.total_exportation = _.round(total_exportation, 2);
              this.form.total_taxed = _.round(total_taxed, 2);
              this.form.total_exonerated = _.round(total_exonerated, 2);
              this.form.total_unaffected = _.round(total_unaffected, 2);
              this.form.total_free = _.round(total_free, 2);
              this.form.total_igv = _.round(total_igv, 2);
              this.form.total_value = _.round(total_value, 2);
              this.form.total_taxes = _.round(total_igv, 2);
              this.form.total = _.round(total, 2);

              this.calculatePerception();
            },
            calculatePerception() {
              let supplier = _.find(this.all_suppliers, { id: this.form.supplier_id });

              if (supplier) {
                if (supplier.perception_agent) {
                  let total_perception = 0;
                  let quantity_item_perception = 0;
                  let total_amount = 0;
                  this.form.total_perception = 0;

                  this.form.perception_date = moment().format("YYYY-MM-DD");

                  this.form.items.forEach(row => {
                    quantity_item_perception += row.item.has_perception ? 1 : 0;
                    total_perception += row.item.has_perception
                      ? parseFloat(row.unit_price) *
                        parseFloat(row.quantity) *
                        (parseFloat(row.item.percentage_perception) / 100)
                      : 0;
                  });

                  this.is_perception_agent =
                    quantity_item_perception > 0 ? true : false;
                  this.form.total_perception = _.round(total_perception, 2);
                  total_amount =
                    this.form.total + parseFloat(this.form.total_perception);
                  this.total_amount = _.round(total_amount, 2);
                } else {
                  this.is_perception_agent = false;
                  this.form.perception_date = null;
                  this.form.perception_number = null;
                  this.form.total_perception = null;
                }
              }
            },
    
            clickCancel(index) {
                this.form.payments.splice(index, 1);
            },
            clickAddPayment() {
              // console.log("we")
                this.form.payments.push({
                    id: null,
                    purchase_id: null,
                    date_of_payment:  moment().format('YYYY-MM-DD'),
                    payment_method_type_id: '01',
                    reference: null,
                    payment: 0,
                });
            },   
            validate_payments(){
 
                let error_by_item = 0
                let acum_total = 0

                this.form.payments.forEach((item)=>{
                    acum_total += parseFloat(item.payment)
                    if(item.payment <= 0 || item.payment == null) error_by_item++;
                })

                return  {
                    error_by_item : error_by_item,
                    acum_total : acum_total
                }

            },
            async submit() {
              
              // console.log(this.form)

              let validate = await this.validate_payments()
              if(validate.acum_total > parseFloat(this.form.total) || validate.error_by_item > 0) {
                  return this.$message.error('Los montos ingresados superan al monto a pagar o son incorrectos');
              }

              this.loading_submit = true;
              // await this.changePaymentMethodType(false);
              await this.$http
                .post(`/${this.resource}`, this.form)
                .then(response => {
                  if (response.data.success) {
                    this.purchaseNewId = response.data.data.id;
                    this.$message({
                      showClose: true,
                      message: `Compra registrada : ${this.form.series}-${this.form.number}`,
                      duration: 2 * 3000,
                      type: "success"
                    });
                    this.$eventHub.$emit("reloadData");
                    this.close();

                  } else {
                    this.$message.error(response.data.message);
                  }
                })
                .catch(error => {
                  if (error.response.status === 422) {
                    this.errors = error.response.data;
                  } else {
                    this.$message.error(error.response.data.message);
                  }
                })
                .then(() => {
                  this.loading_submit = false;
                });
            },
            close() {
              this.$emit("update:showDialog", false);
              this.initForm();
              this.resetDocument();
            },
            resetDocument() {
              this.initForm();
              this.form.currency_type_id =
                this.currency_types.length > 0 ? this.currency_types[0].id : null;
              this.form.establishment_id = this.establishment.id;
              this.form.document_type_id =
                this.document_types.length > 0 ? this.document_types[0].id : null;

              this.changeDateOfIssue();
              this.changeDocumentType();
              this.changeCurrencyType();
            },
            async create() {
              await this.$http
                .get(`/purchase-orders/record/${this.recordId}`)
                .then(response => {
                  // this.form = response.data.data.purchase_order;
                  // this.form.payments = []

                  this.form.purchase_order = response.data.data.purchase_order;
                  let warehouse = response.data.data.warehouse;

                  let supp = this.form.purchase_order.supplier;

                  if (supp.identity_document_type_id == 6) {
                    this.form.document_type_id = "01";
                  } else if (supp.identity_document_type_id == 1) {
                    this.form.document_type_id = "03";
                  }

                  this.form.items = response.data.data.purchase_order.items
                  this.form.supplier_id = this.form.purchase_order.supplier_id;
                  this.form.purchase_order_id = this.form.purchase_order.id;
                  this.form.payments[0].payment_method_type_id = this.form.purchase_order.payment_method_type_id
                  this.form.payments[0].payment = this.form.purchase_order.total
                  this.form.total = this.form.purchase_order.total
                  
                  this.form.items.forEach((it)=>{
                    it.warehouse_id = warehouse.id
                  })
                  this.changeDocumentType()
                });
            }
          }
        };
</script>

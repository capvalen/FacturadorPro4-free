<template>
  <div>
    <div class="page-header pr-0">
      <h2>
        <a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a>
      </h2>
      <ol class="breadcrumbs">
        <li class="active"><span>RECEPCIÓN</span></li>
      </ol>
      <div class="right-wrapper pull-right">
        <div class="btn-group flex-wrap">
          <button
            type="button"
            class="btn btn-custom btn-sm mt-2 mr-2"
            @click="onGotoBack"
          >
            <i class="fa fa-arrow-left"></i> Atras
          </button>
        </div>
      </div>
    </div>
    <div class="card mb-0">
      <div class="card-header bg-info">
        <h3 class="my-0">{{ title }}</h3>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-12 col-md-4 mb-3">
            <ul class="list-group">
              <li class="list-group-item active">Habitación</li>
              <li class="list-group-item d-flex justify-content-between">
                <span>Habitación:</span>
                <strong>{{ this.rent.room.name }}</strong>
              </li>
              <li class="list-group-item d-flex justify-content-between">
                <span>Tipo:</span>
                <strong>{{ this.rent.room.category.description }}</strong>
              </li>
            </ul>
          </div>
          <div class="col-12 col-md-4 mb-3">
            <ul class="list-group">
              <li class="list-group-item active">Cliente</li>
              <li class="list-group-item d-flex justify-content-between">
                <span>Nombres:</span>
                <strong>{{ this.rent.customer.name }}</strong>
              </li>
              <li class="list-group-item d-flex justify-content-between">
                <span># Documento:</span>
                <strong>{{ this.rent.customer.number }}</strong>
              </li>
            </ul>
          </div>
          <div class="col-12 col-md-4 mb-3">
            <ul class="list-group">
              <li class="list-group-item active">Entrada/Salida</li>
              <li class="list-group-item d-flex justify-content-between">
                <span>Fecha/Hora Entrada:</span>
                <strong
                  >{{ this.rent.input_date | toDate }} -
                  {{ this.rent.input_time | toTime }}</strong
                >
              </li>
              <li class="list-group-item d-flex justify-content-between">
                <span>Fecha/Hora Salida:</span>
                <strong
                  >{{ this.rent.output_date | toDate }} -
                  {{ this.rent.output_time | toTime }}</strong
                >
              </li>
            </ul>
          </div>
          <div class="col-12">
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr class="table-info">
                    <th></th>
                    <th colspan="5">Costo del alojamiento</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>#</td>
                    <td>Costo por tarifa</td>
                    <td>Cant. noches</td>
                    <td>Carga por salir tarde</td>
                    <td></td>
                    <td></td>
                  </tr>
                  <tr>
                    <td>1</td>
                    <td>{{ room.item.unit_price | toDecimals }}</td>
                    <td>{{ room.item.quantity }}</td>
                    <td class="text-center">
                      <div class="d-d-inline-block" style="max-width: 120px">
                        <el-input v-model="arrears" type="number"></el-input>
                      </div>
                    </td>
                    <td></td>
                    <td class="text-center">
                      <div class="d-d-inline-block" style="max-width: 120px">
                        <el-input
                          v-model="total"
                          readonly
                          type="number"
                        ></el-input>
                      </div>
                    </td>
                  </tr>
                  <tr class="table-info">
                    <td></td>
                    <td colspan="5">Servicio al cuarto</td>
                  </tr>
                  <tr>
                    <td>#</td>
                    <td>Descripción</td>
                    <td>Precio unitario</td>
                    <td>Cantidad</td>
                    <td>Estado</td>
                    <td>Total</td>
                  </tr>
                  <tr
                    v-for="(it, i) in rent.items"
                    :key="i"
                    v-show="it.type === 'PRO'"
                  >
                    <td>{{ i + 1 }}</td>
                    <td>{{ it.item.item.description }}</td>
                    <td>{{ it.item.input_unit_price_value | toDecimals }}</td>
                    <td>{{ it.item.quantity | toDecimals }}</td>
                    <td>
                      {{ it.payment_status === "PAID" ? "PAGADO" : "DEBE" }}
                    </td>
                    <td>{{ it.item.total | toDecimals }}</td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr>
                    <td class="text-right" colspan="5">Pagado</td>
                    <td>
                      <h3 class="my-0">
                        <span class="badge badge-pill badge-info">{{
                          totalPaid | toDecimals
                        }}</span>
                      </h3>
                    </td>
                  </tr>
                  <tr>
                    <td class="text-right" colspan="5">Debe</td>
                    <td>
                      <h3 class="my-0">
                        <span class="badge badge-pill badge-danger">{{
                          totalDebt | toDecimals
                        }}</span>
                      </h3>
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
          <div class="col-lg-3">
            <div
              class="form-group"
              :class="{ 'has-danger': errors.document_type_id }"
            >
              <label class="control-label">Tipo comprobante</label>
              <el-select
                v-model="document.document_type_id"
                @change="changeDocumentType"
                popper-class="el-select-document_type"
                dusk="document_type_id"
                class="border-left rounded-left border-info"
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
          <div class="col-lg-3">
            <div class="form-group" :class="{ 'has-danger': errors.series_id }">
              <label class="control-label">Serie</label>
              <el-select v-model="document.series_id">
                <el-option
                  v-for="option in series"
                  :key="option.id"
                  :value="option.id"
                  :label="option.number"
                ></el-option>
              </el-select>
              <small
                class="form-control-feedback"
                v-if="errors.series_id"
                v-text="errors.series_id[0]"
              ></small>
            </div>
          </div>
          <div class="col-lg-3">
            <div
              class="form-group"
              :class="{ 'has-danger': errors.date_of_issue }"
            >
              <label class="control-label">Fecha de emisión</label>
              <el-date-picker
                readonly
                v-model="document.date_of_issue"
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
          <div class="col-lg-3">
            <div
              class="form-group"
              :class="{ 'has-danger': errors.date_of_issue }"
            >
              <label class="control-label">Fecha de vencimiento</label>
              <el-date-picker
                v-model="document.date_of_due"
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

          <div class="col-12">
            <table class="table">
              <thead>
                <tr width="100%">
                  <th v-if="document.payments.length > 0">M. Pago</th>
                  <th v-if="document.payments.length > 0">Destino</th>
                  <th v-if="document.payments.length > 0">Referencia</th>
                  <th v-if="document.payments.length > 0">Monto</th>
                  <th width="15%">
                    <a
                      href="#"
                      @click.prevent="clickAddPayment"
                      class="text-center font-weight-bold text-info"
                      >[+ Agregar]</a
                    >
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(row, index) in document.payments" :key="index">
                  <td>
                    <div class="form-group mb-2 mr-2">
                      <el-select v-model="row.payment_method_type_id">
                        <el-option
                          v-for="option in paymentMethodTypes"
                          :key="option.id"
                          :value="option.id"
                          :label="option.description"
                        ></el-option>
                      </el-select>
                    </div>
                  </td>
                  <td>
                    <div class="form-group mb-2 mr-2">
                      <el-select
                        v-model="row.payment_destination_id"
                        filterable
                        :disabled="row.payment_destination_disabled"
                      >
                        <el-option
                          v-for="option in paymentDestinations"
                          :key="option.id"
                          :value="option.id"
                          :label="option.description"
                        ></el-option>
                      </el-select>
                    </div>
                  </td>
                  <td>
                    <div class="form-group mb-2 mr-2">
                      <el-input v-model="row.reference"></el-input>
                    </div>
                  </td>
                  <td>
                    <div class="form-group mb-2 mr-2">
                      <el-input v-model="row.payment"></el-input>
                    </div>
                  </td>
                  <td class="series-table-actions text-center">
                    <button
                      type="button"
                      class="btn waves-effect waves-light btn-xs btn-danger"
                      @click.prevent="clickCancel(index)"
                    >
                      <i class="fa fa-trash"></i>
                    </button>
                  </td>
                  <br />
                </tr>
              </tbody>
            </table>
          </div>
          <div class="col-12 pt-3">
            <el-button
              :loading="loading"
              :disabled="loading"
              type="primary"
              @click="onGoToInvoice"
            >
              <i class="fa fa-save"></i>
              <span class="ml-2">Guardar y Generar Comprobante</span>
            </el-button>
          </div>
        </div>
      </div>
    </div>
    <document-options
      :showDialog.sync="showDialogDocumentOptions"
      :recordId="documentNewId"
      :isContingency="false"
      :showClose="true"
    ></document-options>
  </div>
</template>

<script>
import moment from "moment";
import DocumentOptions from "@views/documents/partials/options.vue";
import { calculateRowItem } from "../../../../../../../resources/js/helpers/functions";
import { exchangeRate } from "../../../../../../../resources/js/mixins/functions";

export default {
  components: {
    DocumentOptions,
  },
  mixins: [exchangeRate],
  props: {
    rent: {
      type: Object,
      required: true,
    },
    customer: {
      type: Object,
      required: true,
    },
    token: {
      type: String,
      required: true,
    },
    room: {
      type: Object,
      required: true,
    },
    paymentMethodTypes: {
      type: Array,
      required: true,
    },
    paymentDestinations: {
      type: Array,
      required: true,
    },
    allSeries: {
      type: Array,
      required: true,
    },
    documentTypesInvoice: {
      type: Array,
      required: true,
    },
    warehouseId: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      title: "",
      arrears: 0,
      total: 0,
      loading: false,
      totalPaid: 0,
      totalDebt: 0,
      response: {},
      document: {
        payments: [],
      },
      errors: {},
      series: [],
      document_types: [],
      all_document_types: [],
      resource_documents: "documents",
      showDialogDocumentOptions: false,
      documentNewId: null,
      form_cash_document: {},
    };
  },
  async mounted() {
    this.initForm();
    this.initDocument();
    this.all_document_types = this.documentTypesInvoice;
    this.title = `Checkout: Habitación ${this.rent.room.name}`;
    this.total = this.room.item.total;
    this.document.items = this.rent.items.map((i) => i.item);
    this.onCalculateTotals();
    this.onCalculatePaidAndDebts();
    this.clickAddPayment();
    this.validateIdentityDocumentType();
    const date = moment().format("YYYY-MM-DD");
    await this.searchExchangeRateByDate(date).then((res) => {
      this.document.exchange_rate_sale = res;
    });
  },
  watch: {
    arrears(value) {
      if (isNaN(value)) {
        return;
      }
      if (value >= 0) {
        const total = parseFloat(this.room.item.total) + parseFloat(value);
        this.total = total;
        this.onCalculatePaidAndDebts();
      }
    },
  },
  methods: {
    validateIdentityDocumentType() {
      let identity_document_types = ["0", "1"];
      let customer = this.document.customer;
      if (
        identity_document_types.includes(customer.identity_document_type_id)
      ) {
        this.document_types = _.filter(this.all_document_types, { id: "03" });
      } else {
        this.document_types = this.all_document_types;
      }

      this.document.document_type_id =
        this.document_types.length > 0 ? this.document_types[0].id : null;
      this.changeDocumentType();
    },
    changeDateOfIssue() {
      this.document.date_of_due = this.document.date_of_issue;
    },
    changeDocumentType() {
      this.document.series_id = null;
      this.series = _.filter(this.allSeries, {
        document_type_id: this.document.document_type_id,
      });
      this.document.series_id =
        this.series.length > 0 ? this.series[0].id : null;
    },
    clickAddPayment() {
      const payment =
        this.document.payments.length == 0 ? this.document.total : 0;

      this.document.payments.push({
        id: null,
        document_id: null,
        date_of_payment: moment().format("YYYY-MM-DD"),
        payment_method_type_id: "01",
        payment_destination_id: null,
        reference: null,
        payment: payment,
      });
    },
    onExitPage() {
      window.location.href = "/hotels/reception";
    },
    validatePaymentDestination() {
      let error_by_item = 0;

      this.document.payments.forEach((item) => {
        if (item.payment_destination_id == null) error_by_item++;
      });

      return {
        error_by_item: error_by_item,
      };
    },
    initForm() {
      this.form_cash_document = {
        document_id: null,
        sale_note_id: null,
      };
    },
    async onGoToInvoice() {
      this.onUpdateItemsWithExtras();
      this.onCalculateTotals();
      let validate_payment_destination = this.validatePaymentDestination();

      if (validate_payment_destination.error_by_item > 0) {
        return this.$message.error("El destino del pago es obligatorio");
      }
      this.loading = true;
      this.$http
        .post(`/${this.resource_documents}`, this.document)
        .then((response) => {
          if (response.data.success) {
            this.documentNewId = response.data.data.id;
            this.form_cash_document.document_id = response.data.data.id;
            this.showDialogDocumentOptions = true;
            this.$emit("update:showDialog", false);
            this.saveCashDocument();

            const payloadFinalizedRent = {
              arrears: this.arrears,
            };
            this.loading = true;
            this.$http
              .post(
                `/hotels/reception/${this.rent.id}/rent/finalized`,
                payloadFinalizedRent
              )
              .then(() => {
                this.response = response.data;
              })
              .finally(() => (this.loading = false));
          } else {
            this.$message.error(response.data.message);
          }
        })
        .catch((error) => {
          if (error.response.status === 422) {
            this.errors = error.response.data;
          } else {
            this.$message.error(error.response.data.message);
          }
        })
        .finally(() => {
          this.loading = false;
        });
    },
    onUpdateItemsWithExtras() {
      this.document.items = this.document.items.map((it) => {
        if (it.item_id === this.room.item_id) {
          const name = `${this.room.item.item.description} x ${this.room.item.quantity} noche(s)`;
          it.item.description = name;
          it.item.full_description = name;
          it.name_product_pdf = name;
          it.quantity = 1;
          const newTotal =
            parseFloat(this.room.item.total) + parseFloat(this.arrears);
          it.input_unit_price_value = parseFloat(newTotal);
          it.item.unit_price = parseFloat(newTotal);
          it.unit_value = parseFloat(newTotal);
          const newItem = calculateRowItem(it, "PEN", 3);
          return newItem;
        }
        return it;
      });
    },
    saveCashDocument() {
      this.$http
        .post(`/cash/cash_document`, this.form_cash_document)
        .then((response) => {
          if (!response.data.success) {
            this.$message.error(response.data.message);
          }
        })
        .catch((error) => {
          this.axiosError(error);
        });
    },
    onCalculatePaidAndDebts() {
      this.totalPaid = this.rent.items
        .map((i) => {
          if (i.payment_status === "PAID") {
            return i.item.total;
          }
          return 0;
        })
        .reduce((a, b) => a + b, 0);
      const totalDebt = this.rent.items
        .map((i) => {
          if (i.payment_status === "DEBT") {
            return i.item.total;
          }
          return 0;
        })
        .reduce((a, b) => a + b, 0);
      this.totalDebt = totalDebt + parseFloat(this.arrears);
    },
    initDocument() {
      this.document = {
        customer_id: this.rent.customer_id,
        customer: this.rent.customer,
        document_type_id: null,
        series_id: null,
        establishment_id: this.warehouseId,
        number: "#",
        date_of_issue: moment().format("YYYY-MM-DD"),
        time_of_issue: moment().format("HH:mm:ss"),
        currency_type_id: "PEN",
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
        operation_type_id: "0101",
        date_of_due: moment().format("YYYY-MM-DD"),
        delivery_date: moment().format("YYYY-MM-DD"),
        items: [],
        charges: [],
        discounts: [],
        attributes: [],
        guides: [],
        additional_information: null,
        actions: {
          format_pdf: "a4",
        },
        dispatch_id: null,
        dispatch: null,
        is_receivable: false,
        payments: [],
        hotel: {},
      };
    },
    onGotoBack() {
      window.location.href = "/hotels/reception";
    },
    onCalculateTotals() {
      let total_exportation = 0;
      let total_taxed = 0;
      let total_exonerated = 0;
      let total_unaffected = 0;
      let total_free = 0;
      let total_igv = 0;
      let total_value = 0;
      let total = 0;
      let total_plastic_bag_taxes = 0;
      let total_discount = 0;
      let total_charge = 0;
      this.document.items.forEach((row) => {
        total_discount += parseFloat(row.total_discount);
        total_charge += parseFloat(row.total_charge);

        if (row.affectation_igv_type_id === "10") {
          total_taxed += parseFloat(row.total_value);
        }
        if (["10", "20", "30", "40"].indexOf(row.affectation_igv_type_id) < 0) {
          total_free += parseFloat(row.total_value);
        }
        if (
          ["10", "20", "30", "40"].indexOf(row.affectation_igv_type_id) > -1
        ) {
          total_igv += parseFloat(row.total_igv);
          total += parseFloat(row.total);
        }
        total_value += parseFloat(row.total_value);
        total_plastic_bag_taxes += parseFloat(row.total_plastic_bag_taxes);

        if (["13", "14", "15"].includes(row.affectation_igv_type_id)) {
          let unit_value =
            row.total_value / row.quantity / (1 + row.percentage_igv / 100);
          let total_value_partial = unit_value * row.quantity;
          row.total_taxes = row.total_value - total_value_partial;
          row.total_igv = row.total_value - total_value_partial;
          row.total_base_igv = total_value_partial;
          total_value -= row.total_value;
        }
      });

      this.document.total_exportation = _.round(total_exportation, 2);
      this.document.total_taxed = _.round(total_taxed, 2);
      this.document.total_exonerated = _.round(total_exonerated, 2);
      this.document.total_unaffected = _.round(total_unaffected, 2);
      this.document.total_free = _.round(total_free, 2);
      this.document.total_igv = _.round(total_igv, 2);
      this.document.total_value = _.round(total_value, 2);
      this.document.total_taxes = _.round(total_igv, 2);
      this.document.total_plastic_bag_taxes = _.round(
        total_plastic_bag_taxes,
        2
      );
      this.document.total = _.round(
        total + this.document.total_plastic_bag_taxes,
        2
      );
    },
  },
};
</script>

<template>
  <div>
    <el-dialog
      :title="titleDialog"
      :visible="showDialog"
      @open="create"
      width="30%"
      :close-on-click-modal="false"
      :close-on-press-escape="false"
      :show-close="false"
    >
      <div class="row" v-show="!showGenerate">
        <div class="col-lg-4 col-md-4 col-sm-4 text-center font-weight-bold">
          <p>Imprimir A4</p>
          <button
            type="button"
            class="btn btn-lg btn-info waves-effect waves-light"
            @click="clickToPrint('a4')"
          >
            <i class="fa fa-file-alt"></i>
          </button>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 text-center font-weight-bold">
          <p>Imprimir A5</p>
          <button
            type="button"
            class="btn btn-lg btn-info waves-effect waves-light"
            @click="clickToPrint('a5')"
          >
            <i class="fa fa-file-alt"></i>
          </button>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 text-center font-weight-bold">
          <p>Imprimir Ticket</p>
          <button
            type="button"
            class="btn btn-lg btn-info waves-effect waves-light"
            @click="clickToPrint('ticket')"
          >
            <i class="fa fa-receipt"></i>
          </button>
        </div>
      </div>
      <br />
      <div class="row" v-show="!showGenerate">
        <div class="col-md-12">
          <el-input v-model="customer_email">
            <el-button
              slot="append"
              icon="el-icon-message"
              @click="clickSendEmail"
              :loading="loading"
              >Enviar</el-button
            >
          </el-input>
        </div>
      </div>
      <br />
      <div class="row" v-if="typeUser == 'admin'">
        <div class="col-md-9" v-show="!showGenerate">
          <div class="form-group">
            <el-checkbox v-model="generate"
              >Generar comprobante electrónico</el-checkbox
            >
          </div>
        </div>
      </div>
      <div class="row" v-if="generate">
        <div class="col-lg-12 pb-2">
          <div class="form-group">
            <label class="control-label font-weight-bold text-info">
              Cliente
            </label>
            <el-select
              v-model="document.customer_id"
              filterable
              remote
              class="border-left rounded-left border-info"
              popper-class="el-select-customers"
              placeholder="Escriba el nombre o número de documento del cliente"
              :remote-method="searchRemoteCustomers"
              @change="changeCustomer"
              :loading="loading_search"
            >
              <el-option
                v-for="option in customers"
                :key="option.id"
                :value="option.id"
                :label="option.description"
              ></el-option>
            </el-select>
            <small
              class="form-control-feedback"
              v-if="errors.customer_id"
              v-text="errors.customer_id[0]"
            ></small>
          </div>
        </div>

        <div class="col-lg-8">
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
        <div class="col-lg-4">
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

        <div class="col-lg-6">
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

        <div class="col-lg-6">
          <div
            class="form-group"
            :class="{ 'has-danger': errors.date_of_issue }"
          >
            <!--<label class="control-label">Fecha de emisión</label>-->
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
        <br />
        <div class="col-lg-4">
          <div class="form-group" v-show="document.document_type_id == '03'">
            <el-checkbox
              v-model="document.is_receivable"
              class="font-weight-bold"
              >¿Es venta por cobrar?</el-checkbox
            >
          </div>
        </div>
        <br />
        <div class="col-lg-12" v-show="is_document_type_invoice">
          <table>
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
                        v-for="option in payment_method_types"
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
                        v-for="option in payment_destinations"
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
      </div>

      <span slot="footer" class="dialog-footer">
        <template v-if="showClose">
          <el-button @click="clickClose">Cerrar</el-button>
          <el-button
            class="submit"
            type="primary"
            @click="submit"
            :loading="loading_submit"
            v-if="generate"
            >Generar</el-button
          >
        </template>
        <template v-else>
          <el-button
            class="submit"
            type="primary"
            plain
            @click="submit"
            :loading="loading_submit"
            v-if="generate"
            >Generar comprobante</el-button
          >
          <el-button @click="clickFinalize" v-else>Ir al listado</el-button>
          <el-button type="primary" @click="clickNewOrderNote"
            >Nuevo pedido</el-button
          >
        </template>
      </span>
    </el-dialog>

    <document-options
      :showDialog.sync="showDialogDocumentOptions"
      :recordId="documentNewId"
      :isContingency="false"
      :showClose="true"
    ></document-options>

    <sale-note-options
      :showDialog.sync="showDialogSaleNoteOptions"
      :recordId="documentNewId"
      :showClose="true"
    ></sale-note-options>
  </div>
</template>

<script>
import DocumentOptions from "@views/documents/partials/options.vue";
import SaleNoteOptions from "@views/sale_notes/partials/options.vue";
import { calculateRowItem } from "../../../helpers/functions";
import { exchangeRate } from "../../../mixins/functions";
import moment from "moment";

export default {
  components: { DocumentOptions, SaleNoteOptions },
  mixins: [exchangeRate],
  props: [
    "showDialog",
    "recordId",
    "showClose",
    "showGenerate",
    "type",
    "typeUser",
  ],
  data() {
    return {
      customer_email: "",
      titleDialog: null,
      loading: false,
      resource: "dispatches",
      resource_documents: "documents",
      errors: {},
      form: {},
      document: {},
      document_types: [],
      all_document_types: [],
      all_series: [],
      series: [],
      customers: [],
      generate: false,
      loading_submit: false,
      showDialogDocumentOptions: false,
      showDialogSaleNoteOptions: false,
      documentNewId: null,
      is_document_type_invoice: true,
      loading_search: false,
      payment_destinations: [],
      form_cash_document: {},
      payment_method_types: [],
      items: [],
      affectation_igv_types: [],
      affectation_igv_type: null,
      currencyTypeIdActive: "PEN",
      exchangeRateSale: 1,
    };
  },
  created() {
    this.initForm();
    this.initDocument();
  },
  methods: {
    clickCancel(index) {
      this.document.payments.splice(index, 1);
    },
    async clickAddPayment() {
      let payment =
        this.document.payments.length == 0 ? this.form.dispatch.total : 0;

      await this.document.payments.push({
        id: null,
        document_id: null,
        date_of_payment: moment().format("YYYY-MM-DD"),
        payment_method_type_id: "01",
        payment_destination_id: null,
        reference: null,
        payment: payment,
      });
    },
    initForm() {
      this.generate = this.showGenerate ? true : false;
      this.errors = {};
      this.form = {
        id: null,
        external_id: null,
        identifier: null,
        date_of_issue: null,
      };

      this.form_cash_document = {
        document_id: null,
        sale_note_id: null,
      };
    },
    async getCustomer() {
      await this.$http
        .get(
          `/${this.resource_documents}/search/customer/${this.form.dispatch.customer_id}`
        )
        .then((response) => {
          this.customers = response.data.customers;
          this.document.customer_id = this.form.dispatch.customer_id;
          this.changeCustomer();
        });
    },
    async changeCustomer() {
      await this.validateIdentityDocumentType();
    },
    searchRemoteCustomers(input) {
      if (input.length > 0) {
        this.loading_search = true;
        let parameters = `input=${input}&document_type_id=${this.form.document_type_id}&operation_type_id=${this.form.operation_type_id}`;

        this.$http
          .get(`/${this.resource}/search/customers?${parameters}`)
          .then((response) => {
            this.customers = response.data.customers;
            this.loading_search = false;
          });
      }
    },
    initDocument() {
      this.document = {
        document_type_id: null,
        series_id: null,
        establishment_id: null,
        number: "#",
        date_of_issue: moment().format("YYYY-MM-DD"),
        time_of_issue: null,
        customer_id: null,
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
        operation_type_id: null,
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
    changeDateOfIssue() {
      this.document.date_of_due = this.document.date_of_issue;
    },
    resetDocument() {
      this.generate = this.showGenerate ? true : false;
      this.initDocument();
      this.document.document_type_id =
        this.document_types.length > 0 ? this.document_types[0].id : null;
      this.changeDocumentType();
    },
    async validatePaymentDestination() {
      let error_by_item = 0;

      await this.document.payments.forEach((item) => {
        if (item.payment_destination_id == null) error_by_item++;
      });

      return {
        error_by_item: error_by_item,
      };
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

      this.setTotalDefaultPayment();
    },
    setTotalDefaultPayment() {
      if (this.document.payments.length > 0) {
        this.document.payments[0].payment = this.document.total;
      }
    },
    async submit() {
      this.assignDocument();
      this.onCalculateTotals();

      let validate_payment_destination = await this.validatePaymentDestination();

      if (validate_payment_destination.error_by_item > 0) {
        return this.$message.error("El destino del pago es obligatorio");
      }

      this.loading_submit = true;
      if (this.document.document_type_id === "80") {
        this.document.prefix = "NV";
        this.resource_documents = "sale-notes";
      } else {
        this.document.prefix = null;
        this.resource_documents = "documents";
      }

      this.$http
        .post(`/${this.resource_documents}`, this.document)
        .then((response) => {
          if (response.data.success) {
            this.documentNewId = response.data.data.id;
            if (this.document.document_type_id === "80") {
              this.form_cash_document.sale_note_id = response.data.data.id;
              this.showDialogSaleNoteOptions = true;
            } else {
              this.form_cash_document.document_id = response.data.data.id;
              this.showDialogDocumentOptions = true;
            }
            this.clickClose();
            this.saveCashDocument();

            this.$eventHub.$emit("reloadData");
            this.onUpdateDispatchWithDocumentId(response.data.data.id);
            this.resetDocument();
            this.document.customer_id = this.form.dispatch.customer_id;
            this.changeCustomer();
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
        .then(() => {
          this.loading_submit = false;
        });
    },
    onUpdateDispatchWithDocumentId(documentId) {
        this.loading_submit = true;
        const payload = {
            document_id: documentId
        }
        this.$http.post(`dispatches/record/${this.recordId}/set-document-id`, payload).finally(() => this.loading_submit = true)
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
          this.axiosError(error)
        });
    },
    onGetItems(item) {
      const it = {
        IdLoteSelected: null,
        affectation_igv_type: this.affectation_igv_type,
        affectation_igv_type_id: "10",
        attributes: [],
        charges: [],
        discounts: [],
        document_item_id: null,
        has_igv: item.has_igv,
        has_isc: false,
        has_plastic_bag_taxes: false,
        input_unit_price_value: item.sale_unit_price,
        item: item,
        item_id: item.id,
        item_unit_type_id: null,
        item_unit_types: [],
        lots_group: [],
        percentage_isc: 0,
        quantity: item.quantity,
        suggested_price: 0,
        system_isc_type_id: null,
        unit_price: item.sale_unit_price,
        unit_price_value: item.sale_unit_price,
        warehouse_id: null,
      };
      return calculateRowItem(
        it,
        this.currencyTypeIdActive,
        this.exchangeRateSale
      );
    },
    assignDocument() {
      let q = this.form.dispatch;
      this.document.establishment_id = q.establishment_id;
      this.document.time_of_issue = moment().format("HH:mm:ss");
      this.document.purchase_order = null;
      this.document.total_prepayment = q.total_prepayment;
      this.document.total_charge = q.total_charge;
      this.document.total_discount = q.total_discount;
      this.document.total_exportation = q.total_exportation;
      this.document.total_free = q.total_free;
      this.document.total_taxed = q.total_taxed;
      this.document.total_unaffected = q.total_unaffected;
      this.document.total_exonerated = q.total_exonerated;
      this.document.total_igv = q.total_igv;
      this.document.total_base_isc = q.total_base_isc;
      this.document.total_isc = q.total_isc;
      this.document.total_base_other_taxes = q.total_base_other_taxes;
      this.document.total_other_taxes = q.total_other_taxes;
      this.document.total_taxes = q.total_taxes;
      this.document.total_value = q.total_value;
      this.document.total = q.total;
      this.document.operation_type_id = "0101";
      this.document.items = q.items;
      this.document.charges = q.charges;
      this.document.discounts = q.discounts;
      this.document.attributes = [];
      this.document.guides = q.guides;
      this.document.additional_information = null;
      this.document.actions = {
        format_pdf: "a4",
      };
      this.document.dispatch_id = this.form.dispatch.id;
      this.document.items = this.items;
    },
    async create() {
      const response = await this.$http.get(
        `/${this.resource}/record/${this.recordId}/tables`
      );
      const data = response.data;
      this.all_document_types = await data.document_types_invoice;
      this.all_series = await data.series;
      this.payment_destinations = await data.payment_destinations;
      this.payment_method_types = await data.payment_method_types;
      this.affectation_igv_types = await data.affectation_igv_types;
      this.affectation_igv_type = await data.affectation_igv_types
        .filter((a) => a.id == "10")
        .reduce((a) => a);
      this.form.dispatch = await data.dispatch;

      const items = await data.items.map((i) => {
        const it = this.form.dispatch.items
          .filter((ite) => ite.item_id == i.id)
          .reduce((ite) => ite);
        i.quantity = it.quantity;
        i.unit_price = i.sale_unit_price;
        return i;
      });

      this.items = items.map((item) => this.onGetItems(item));
      await this.getCustomer();
      await this.validateIdentityDocumentType();
      const date = moment(this.form.dispatch.date_of_issue).format(
        "YYYY-MM-DD"
      );
      await this.searchExchangeRateByDate(date).then((res) => {
        this.document.exchange_rate_sale = res;
      });
      this.document.items = this.items;
      this.titleDialog = `Guía ${this.form.dispatch.series}-${this.form.dispatch.number}: Crear comprobante`;

      await this.clickAddPayment();
      this.onCalculateTotals();
    },
    changeDocumentType() {
      // this.filterSeries()
      this.document.is_receivable = false;
      this.series = [];
      if (this.document.document_type_id !== "nv") {
        this.filterSeries();
        this.is_document_type_invoice = true;
      } else {
        this.is_document_type_invoice = false;
      }
    },
    async validateIdentityDocumentType() {
      let identity_document_types = ["0", "1"];
      let customer = _.find(this.customers, { id: this.document.customer_id });
      if (!customer) {
        return;
      }
      if (
        identity_document_types.includes(customer.identity_document_type_id)
      ) {
        this.document_types = _.filter(this.all_document_types, { id: "03" });
      } else {
        this.document_types = this.all_document_types;
      }

      this.document.document_type_id =
        this.document_types.length > 0 ? this.document_types[0].id : null;
      await this.changeDocumentType();
    },
    filterSeries() {
      this.document.series_id = null;
      this.series = _.filter(this.all_series, {
        document_type_id: this.document.document_type_id,
      });
      this.document.series_id =
        this.series.length > 0 ? this.series[0].id : null;
    },
    clickFinalize() {
      location.href = `/${this.resource}`;
    },
    clickNewOrderNote() {
      this.clickClose();
    },
    clickClose() {
      this.$emit("update:showDialog", false);
      this.initForm();
      this.resetDocument();
    },
    clickToPrint(format) {
      window.open(
        `/${this.resource}/print/${this.form.external_id}/${format}`,
        "_blank"
      );
    },
    clickSendEmail() {
      this.loading = true;
      this.$http
        .post(`/${this.resource}/email`, {
          customer_email: this.customer_email,
          id: this.form.id,
          customer_id: this.form.dispatch.customer_id,
        })
        .then((response) => {
          if (response.data.success) {
            this.$message.success("El correo fue enviado satisfactoriamente");
          } else {
            this.$message.error("Error al enviar el correo");
          }
        })
        .catch((error) => {
          this.$message.error("Error al enviar el correo");
        })
        .then(() => {
          this.loading = false;
        });
    },
  },
};
</script>

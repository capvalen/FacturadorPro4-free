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
        <div class="row justify-content-between">
          <div class="col-12">
            <div class="text-right">
              <el-button type="primary" @click="onOpenModalProducts">
                <i class="fa fa-plus"></i>
                <span class="ml-2">Agregar Producto</span>
              </el-button>
            </div>
            <table class="table">
              <thead>
                <tr>
                  <th>Producto</th>
                  <th class="text-center">Cant.</th>
                  <th class="text-center">Precio</th>
                  <th class="text-right">Importe</th>
                  <th class="text-center">Estado del pago</th>
                  <th class="text-right"></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="p in form.products" :key="p.item_id">
                  <td>{{ p.item.description }}</td>
                  <td class="text-center">{{ p.quantity | toDecimals }}</td>
                  <td class="text-center">
                    {{ p.input_unit_price_value | toDecimals }}
                  </td>
                  <td class="text-right">{{ p.total | toDecimals }}</td>
                  <td class="text-center">
                    <div style="max-width: 150px" class="d-inline-block">
                      <el-select
                        v-model="p.payment_status"
                        placeholder="Proceso de pago"
                      >
                        <el-option label="Cancelado" value="PAID"></el-option>
                        <el-option
                          label="Cargar a habitación"
                          value="DEBT"
                        ></el-option>
                      </el-select>
                      <div
                        v-if="errors.payment_status"
                        class="form-control-feedback"
                      >
                        {{ errors.payment_status[0] }}
                      </div>
                    </div>
                  </td>
                  <td>
                    <el-button @click="onDeleteProduct(p)" type="danger">
                      <i class="fa fa-trash"></i>
                    </el-button>
                  </td>
                </tr>
              </tbody>
              <tfoot v-if="form.products.length > 0">
                <tr>
                  <td colspan="3" class="text-right">SUBTOTAL</td>
                  <td class="text-right">
                    {{ this.form.subtotal | toDecimals }}
                  </td>
                  <td></td>
                  <td></td>
                </tr>
                <tr>
                  <td colspan="3" class="text-right">IGV</td>
                  <td class="text-right">
                    {{ this.form.igv | toDecimals }}
                  </td>
                  <td></td>
                  <td></td>
                </tr>
                <tr>
                  <td colspan="3" class="text-right">TOTAL</td>
                  <td class="text-right">
                    {{ this.form.total | toDecimals }}
                  </td>
                  <td></td>
                  <td></td>
                </tr>
                <tr>
                  <td colspan="5"></td>
                  <td>
                    <el-button
                      type="primary"
                      class="btn-block"
                      @click="onSubmit"
                      :loading="loading"
                      :disabled="loading"
                    >
                      <i class="fa fa-save"></i>
                      <span class="ml-2">Guardar</span>
                    </el-button>
                  </td>
                </tr>
              </tfoot>
            </table>
            <div v-if="errors.products" class="form-control-feedback">
              {{ errors.products[0] }}
            </div>
          </div>
        </div>
      </div>
    </div>
    <document-form-item
      :showDialog.sync="showDialogAddItem"
      :recordItem="recordItem"
      :isEditItemNote="false"
      operation-type-id="0101"
      currency-type-id-active="PEN"
      :exchange-rate-sale="0"
      :typeUser="typeUser"
      :configuration="configuration"
      :editNameProduct="configuration.edit_name_product"
      @add="onAddItem"
    ></document-form-item>
  </div>
</template>

<script>
import DocumentFormItem from "../../../../../../../resources/js/views/tenant/documents/partials/item.vue";

export default {
  components: {
    DocumentFormItem,
  },
  props: {
    rent: {
      type: Object,
      required: true,
    },
    products: {
      type: Array,
      required: false,
      default: [],
    },
    configuration: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      title: "",
      showDialogAddItem: false,
      recordItem: null,
      form: {
        products: [],
        subtotal: 0,
        total: 0,
        igv: 0,
      },
      errors: {},
      typeUser: "admin",
      loading: false,
    };
  },
  mounted() {
    if (this.products) {
      const products = this.products.map((p) => {
        p.item.payment_status = p.payment_status;
        return p.item;
      });
      this.form.products = products;
      this.onCalculateTotals();
    }
  },
  created() {
    this.title = `Habitación ${this.rent.room.name} - Agregar productos`;
  },
  methods: {
    onSubmit() {
      this.loading = true;
      this.$http
        .post(
          `/hotels/reception/${this.rent.id}/rent/products/store`,
          this.form
        )
        .then((response) => {
          window.location.href = "/hotels/reception";
          this.$message({
            message: response.data.message,
            type: "success",
          });
        })
        .catch((error) => this.axiosError(error))
        .finally(() => (this.loading = false));
    },
    onDeleteProduct(product) {
      this.$confirm(
        "¿estás seguro de eliminar el producto seleccionado?",
        "Cuidado",
        {
          confirmButtonText: "Si",
          cancelButtonText: "No",
          type: "warning",
        }
      )
        .then(() => {
          this.form.products = this.form.products.filter(
            (p) => p.item_id !== product.item_id
          );
          this.onCalculateTotals();
        })
        .catch(() => {});
    },
    onOpenModalProducts() {
      this.showDialogAddItem = true;
    },
    onCalculateTotals() {
      this.form.subtotal = this.form.products
        .map((p) => p.total_value)
        .reduce((a, p) => a + p, 0);
      this.form.igv = this.form.products
        .map((p) => p.total_igv)
        .reduce((a, p) => a + p, 0);
      this.form.total = this.form.subtotal + this.form.igv;
    },
    onAddItem(product) {
      const newProduct = {
        payment_status: "PAID",
        affectation_igv_type_id: product.affectation_igv_type_id,
        attributes: product.attributes,
        charges: product.charges,
        discounts: product.discounts,
        item_id: product.item_id,
        name_product_pdf: product.name_product_pdf,
        percentage_igv: product.percentage_igv,
        percentage_isc: product.percentage_isc,
        percentage_other_taxes: product.percentage_other_taxes,
        price_type_id: product.price_type_id,
        quantity: product.quantity,
        system_isc_type_id: product.system_isc_type_id,
        total: product.total,
        total_base_igv: product.total_base_igv,
        total_base_isc: product.total_base_isc,
        total_base_other_taxes: product.total_base_other_taxes,
        total_charge: product.total_charge,
        total_discount: product.total_discount,
        total_igv: product.total_igv,
        total_isc: product.total_isc,
        total_other_taxes: product.total_other_taxes,
        total_plastic_bag_taxes: product.total_plastic_bag_taxes,
        total_taxes: product.total_taxes,
        total_value: product.total_value,
        unit_price: product.unit_price,
        unit_value: product.unit_value,
        warehouse_id: product.warehouse_id,
        input_unit_price_value: product.input_unit_price_value,
        item: {
          description: product.item.description,
          item_type_id: product.item.item_type_id,
          internal_id: product.item.internal_id,
          item_code: product.item.item_code,
          item_code_gs1: product.item.item_code_gs1,
          unit_type_id: product.item.unit_type_id,
          presentation: product.item.presentation,
          amount_plastic_bag_taxes: product.item.amount_plastic_bag_taxes,
          is_set: product.item.is_set,
          lots: product.item.lots,
          IdLoteSelected: product.item.IdLoteSelected,
        },
      };
      const repeteads = this.form.products.filter(
        (p) => p.item_id === newProduct.item_id
      );
      if (repeteads.length > 0) {
        this.form.products = this.form.products.map((p) => {
          if (p.item_id === newProduct.item_id) {
            return newProduct;
          }
          return p;
        });
      } else {
        this.form.products.push(newProduct);
      }
      this.onCalculateTotals();
    },
    onGotoBack() {
      window.location.href = "/hotels/reception";
    },
  },
};
</script>

<template>
  <el-dialog
    title="Trasladar multiples productos entre almacenes"
    width="80%"
    :visible="show"
    @open="create"
    :close-on-click-modal="false"
    :close-on-press-escape="false"
    append-to-body
    :show-close="false"
  >
    <div class="row">
      <div class="form-group col-6 col-md-3">
        <el-select v-model="form.warehouse_id" placeholder="Almacén">
          <el-option
            v-for="option in warehouses"
            :key="option.id"
            :value="option.id"
            :label="option.description"
          ></el-option>
        </el-select>
      </div>
      <div class="form-group col-6 col-md-2">
        <el-input
          v-model="form.quantity"
          placeholder="Cantidad a trasladar"
          type="number"
        ></el-input>
      </div>
      <div class="form-group col-6 col-md-5">
        <el-input
          v-model="form.reason"
          maxlength="100"
          placeholder="Razón"
        ></el-input>
      </div>
      <div class="form-group col-6 col-md-2">
        <el-button type="success" @click="onApplyAll" class="btn-block"
          >Aplicar a todos</el-button
        >
      </div>
    </div>
    <br />
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th width="200">Producto</th>
            <th>Almacén local</th>
            <th>Can. actual</th>
            <th>Almacén final</th>
            <th>Can. a trasladar</th>
            <th>Motivo de traslado</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="row in records" :key="row.id">
            <td>{{ row.item_description }}</td>
            <td>{{ row.warehouse_description }}</td>
            <td>{{ row.quantity | toDecimals }}</td>
            <td>
              <el-select v-model="row.warehouse_new_id">
                <el-option
                  v-for="option in warehouses"
                  :key="option.id"
                  :value="option.id"
                  :label="option.description"
                ></el-option>
              </el-select>
            </td>
            <td>
              <el-input v-model="row.quantity_move" type="number"></el-input>
            </td>
            <td>
              <el-input v-model="row.detail" maxlength="100"></el-input>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="text-center">
      <el-button type="primary" @click="onSubmit">Guardar</el-button>
      <el-button @click="onClose">Cancelar</el-button>
    </div>
  </el-dialog>
</template>
<script>
export default {
  props: {
    show: {
      type: Boolean,
      required: false,
      default: false,
    },
    products: {
      type: Array,
      required: false,
      default: [],
    },
  },
  data() {
    return {
      errors: {},
      resource: "inventory",
      form: {
        warehouse_id: "",
        quantity: 0,
        reason: "",
      },
      warehouses: [],
      records: [],
      loading: false,
    };
  },
  created() {
    this.$http.get(`/${this.resource}/tables`).then((response) => {
      this.warehouses = response.data.warehouses;
    });
  },
  methods: {
    onApplyAll() {
      this.records = this.records.map((r) => {
        r.warehouse_new_id = this.form.warehouse_id;
        r.quantity_move = this.form.quantity || 1;
        r.detail = this.form.reason || "";
        return r;
      });
    },
    onSubmit() {
      this.loading = true;
      const data = {
        items: this.records,
      };
      this.$http
        .post(`/${this.resource}/move-multilple`, data)
        .then((response) => {
          this.$message({
            message: response.data.message,
            type: "success",
          });
          this.onClose();
        })
        .catch((error) => this.axiosError(error))
        .finally(() => (this.loading = false));
    },
    onClose() {
      this.$emit("update:show", false);
    },
    create() {
      const params = {
        ids: this.products.map((p) => p.id),
      };
      this.$http
        .get(`/${this.resource}/record/multiple`, { params })
        .then((response) => {
          this.records = response.data.data;
        });
    },
  },
};
</script>

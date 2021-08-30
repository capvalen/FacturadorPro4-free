<template>
  <div class="card mb-0 pt-2 pt-md-0">
    <div class="card-header bg-info">
      <h3 class="my-0">Nuevo Traslado</h3>
    </div>
    <div class="tab-content">
      <form autocomplete="off" @submit.prevent="submit">
        <div class="form-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Almacén Inicial</label>
                <el-select v-model="form.warehouse_id" @change="changeWarehouseInit">
                  <el-option
                    v-for="option in warehouses"
                    :key="option.id"
                    :value="option.id"
                    :label="option.description"
                  ></el-option>
                </el-select>
                <small
                  class="form-control-feedback"
                  v-if="errors.warehouse_id"
                  v-text="errors.warehouse_id[0]"
                ></small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group" :class="{'has-danger': errors.warehouse_destination_id}">
                <label class="control-label">Almacén Final</label>
                <el-select v-model="form.warehouse_destination_id">
                  <el-option
                    :disabled="option.id == form.warehouse_id"
                    v-for="option in warehouses"
                    :key="option.id"
                    :value="option.id"
                    :label="option.description"
                  ></el-option>
                </el-select>
                <small
                  class="form-control-feedback"
                  v-if="errors.warehouse_destination_id"
                  v-text="errors.warehouse_destination_id[0]"
                ></small>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group" :class="{'has-danger': errors.description}">
                <label class="control-label">Motivo de Traslado</label>
                <el-input type="textarea" :rows="3" v-model="form.description"></el-input>
                <small
                  class="form-control-feedback"
                  v-if="errors.description"
                  v-text="errors.description[0]"
                ></small>
              </div>
            </div>
          </div>
          <br />
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Producto</label>
                <!-- <el-input v-model="form.item_description" :readonly="true"></el-input> -->
                <el-select
                  @change="changeItem"
                  v-model="form_add.item_id"
                  filterable
                  popper-class="el-select-document_type"
                  class="border-left rounded-left border-info"
                >
                  <el-option
                    v-for="option in items"
                    :key="option.id"
                    :value="option.id"
                    :label="option.description"
                  ></el-option>
                </el-select>

                <a
                  v-if="form_add.item_id  && form_add.series_enabled"
                  href="#"
                  class="text-center font-weight-bold text-info"
                  @click.prevent="clickLotcodeOutput"
                >[&#10004; Seleccionar series]</a>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label class="control-label">Cantidad Actual</label>
                <el-input v-model="form_add.stock" :readonly="true"></el-input>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label class="control-label">Cantidad a Trasladar</label>
                <el-input type="number" v-model="form_add.quantity"></el-input>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <el-button
                  :disabled="form_add.item_id == null"
                  style="margin-top:10%;"
                  @click.prevent="clickAddItem"
                  type="primary"
                  :loading="loading_item"
                >Agregar Producto</el-button>
              </div>
            </div>
          </div>
          <br />
          <div class="row">
            <div class="col-lg-6 col-md-6">
              <table class="table" width="100%">
                <thead>
                  <tr width="100%">
                    <th>#</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(row, index) in form.items" :key="index" width="100%">
                    <td>{{index + 1}}</td>
                    <td>{{row.description}}</td>
                    <td>{{row.quantity}}</td>
                    <td class="series-table-actions text-center">
                      <button
                        type="button"
                        class="btn waves-effect waves-light btn-xs btn-danger"
                        @click.prevent="clickCancel(index)"
                      >
                        <i class="fa fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="form-actions text-right mt-4">
          <el-button @click.prevent="close()">Cancelar</el-button>
          <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
        </div>
      </form>
    </div>

    <output-lots-form
      :showDialog.sync="showDialogLotsOutput"
      :lots="form_add.lots"
      @addRowOutputLot="addRowOutputLot"
    ></output-lots-form>
  </div>
</template>

<script>
import OutputLotsForm from "./partials/lots.vue";

export default {
  props: [],
  components: { OutputLotsForm },
  data() {
    return {
      loading_item: false,
      loading_submit: false,
      titleDialog: null,
      showDialogLotsOutput: false,
      resource: "transfers",
      errors: {},
      form: {},
      warehouses: [],
      items: [],
      form_add: {}
    };
  },
  async created() {
    await this.$http.get(`/${this.resource}/tables`).then(response => {
      this.warehouses = response.data.warehouses;
      this.items = response.data.items;
    });

    await this.initForm();
    this.initFormAdd();
  },
  methods: {
    changeWarehouseInit() {
      this.form.warehouse_destination_id = null;
      this.form.items = [];

      this.$http
        .get(`/${this.resource}/items/${this.form.warehouse_id}`)
        .then(response => {
          this.items = response.data.items;
        });
    },
    addRowOutputLot(lots) {
      let row = this.items.find(x => x.id == this.form_add.item_id);
      row.lots = lots;
    },
    clickCancel(index) {
      this.form.items.splice(index, 1);
    },
    async changeItem() {
      this.loading_item = true;
      await this.$http
        .get(
          `/${this.resource}/stock/${this.form_add.item_id}/${this.form.warehouse_id}`
        )
        .then(response => {
          this.form_add.stock = response.data.stock;
          this.loading_item = false;
        });

      let row = this.items.find(x => x.id == this.form_add.item_id);
      this.form_add.lots = row.lots;
      this.form_add.lots_enabled = row.lots_enabled;
      this.form_add.series_enabled = row.series_enabled;

    },
    initFormAdd() {
      this.form_add = {
        item_id: null,
        stock: 0,
        quantity: 0,
        lots: [],
        lots_enabled: false,
        series_enabled: false
      };
    },
    clickAddItem() {
      /* if (!this.form_add.item_id) {
        return;
      }*/

      if (parseFloat(this.form_add.stock) < 1) {
        return;
      }

      if (this.form_add.quantity < 1) {
        return;
      }

      if (parseFloat(this.form_add.stock) < this.form_add.quantity) {
        return this.$message.error("El stock es menor a la cantidad de traslado.");
      }

      if(this.form_add.series_enabled)
      {
        let selected_lots = this.form_add.lots.filter(x => x.has_sale == true).length;
        if (this.form_add.quantity != selected_lots) {
          return  this.$message.error("La cantidad de series seleccionadas es diferente a la cantidad de traslado");
        }
      }

      let dup = this.form.items.find(x => x.id == this.form_add.item_id);
      if (dup) {
        return this.$message.error("Este producto ya esta agregado.");
      }

      let row = this.items.find(x => x.id == this.form_add.item_id);
      this.form.items.push({
        id: row.id,
        description: row.description,
        quantity: this.form_add.quantity,
        lots: this.form_add.lots
      });

      this.initFormAdd();
    },

    clickLotcodeOutput() {
      this.showDialogLotsOutput = true;
    },
    initForm() {
      this.errors = {};
      this.form = {
        warehouse_id: null,
        warehouse_destination_id: null,
        description: null,
        items: []
      };
    },
    async submit() {
      if (this.form.items.length == 0) {
        return this.$message.error("Debe agregar productos.");
      }

      this.loading_submit = true;
      await this.$http
        .post(`/${this.resource}`, this.form)
        .then(response => {
          if (response.data.success) {
            this.$message.success(response.data.message);
            this.close();
          } else {
            this.$message.error(response.data.message);
          }
        })
        .catch(error => {
          if (error.response.status === 422) {
            this.errors = error.response.data;
          } else {
            console.log(error);
          }
        })
        .then(() => {
          this.loading_submit = false;
        });
    },
    close() {
        location.href = '/transfers'
    }
  }
};
</script>

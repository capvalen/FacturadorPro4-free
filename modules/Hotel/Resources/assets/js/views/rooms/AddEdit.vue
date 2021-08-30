<template>
  <el-dialog
    :title="title"
    :visible="visible"
    @close="onClose"
    @open="onCreate"
    width="400px"
  >
    <form autocomplete="off" @submit.prevent="onSubmit">
      <div class="form-body">
        <div class="form-group">
          <label for="name">Nombre de la habitación</label>
          <input
            type="text"
            id="name"
            class="form-control"
            v-model="form.name"
            :class="{ 'is-invalid': errors.name }"
          />
          <div v-if="errors.name" class="invalid-feedback">
            {{ errors.name[0] }}
          </div>
        </div>
        <div class="form-group">
          <label for="category">Categoría</label>
          <select
            type="text"
            id="category"
            class="form-control"
            v-model="form.hotel_category_id"
            :class="{ 'is-invalid': errors.hotel_category_id }"
          >
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
              {{ cat.description }}
            </option>
          </select>
          <div v-if="errors.hotel_category_id" class="invalid-feedback">
            {{ errors.hotel_category_id[0] }}
          </div>
        </div>
        <div class="form-group">
          <label for="floor">Piso</label>
          <select
            type="text"
            id="floor"
            class="form-control"
            v-model="form.hotel_floor_id"
            :class="{ 'is-invalid': errors.hotel_floor_id }"
          >
            <option v-for="flo in floors" :key="flo.id" :value="flo.id">
              {{ flo.description }}
            </option>
          </select>
          <div v-if="errors.hotel_floor_id" class="invalid-feedback">
            {{ errors.hotel_floor_id[0] }}
          </div>
        </div>
        <div class="form-group">
          <label for="description">Detalles</label>
          <textarea
            rows="3"
            type="text"
            id="description"
            class="form-control"
            v-model="form.description"
            :class="{ 'is-invalid': errors.description }"
          >
          </textarea>
          <div v-if="errors.description" class="invalid-feedback">
            {{ errors.description[0] }}
          </div>
        </div>
        <div class="form-group">
          <label>Mostrar habitación</label>
          <el-switch v-model="form.active"></el-switch>
        </div>
        <div class="row text-center">
          <div class="col-6">
            <el-button
              native-type="submit"
              :disabled="loading"
              type="primary"
              class="btn-block"
              :loading="loading"
              >Guardar</el-button
            >
          </div>
          <div class="col-6">
            <el-button class="btn-block" @click="onClose">Cancelar</el-button>
          </div>
        </div>
      </div>
    </form>
  </el-dialog>
</template>

<script>
export default {
  props: {
    visible: {
      type: Boolean,
      required: true,
      default: false,
    },
    room: {
      type: Object,
      required: false,
      default: {},
    },
    categories: {
      type: Array,
      required: true,
    },
    floors: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      form: {},
      title: "",
      errors: {},
      loading: false,
    };
  },
  methods: {
    onUpdate() {
      this.loading = true;
      this.$http
        .put(`/hotels/rooms/${this.room.id}/update`, this.form)
        .then((response) => {
          this.$emit("onUpdateItem", response.data.data);
          this.onClose();
        })
        .finally(() => {
          this.loading = false;
          this.errors = {};
        })
        .catch((error) => {
          this.axiosError(error);
        });
    },
    onStore() {
      this.loading = true;
      const payload = {
        account_id: null,
        attributes: [],
        brand_id: null,
        calculate_quantity: false,
        category_id: null,
        currency_type_id: "PEN",
        date_of_due: null,
        description: "Habitación " + this.form.name,
        has_igv: true,
        has_isc: false,
        has_perception: false,
        has_plastic_bag_taxes: false,
        id: null,
        image: null,
        image_url: null,
        internal_id: null,
        is_set: false,
        item_code: null,
        item_code_gs1: null,
        item_type_id: "01",
        item_unit_types: [],
        line: null,
        lot_code: null,
        lots: [],
        lots_enabled: false,
        name: "Habitación " + this.form.name,
        percentage_isc: 0,
        percentage_of_profit: 0,
        percentage_perception: 0,
        purchase_affectation_igv_type_id: "10",
        purchase_has_igv: false,
        purchase_unit_price: 0,
        sale_affectation_igv_type_id: "10",
        sale_unit_price: "20",
        second_name: "Habitación " + this.form.name,
        series_enabled: false,
        stock: 0,
        stock_min: 1,
        suggested_price: 0,
        system_isc_type_id: null,
        temp_path: null,
        unit_type_id: "ZZ",
        web_platform_id: null,
      };
      this.$http
        .post("/items", payload)
        .then((response) => {
          this.form.item_id = response.data.id;
          this.$http
            .post("/hotels/rooms/store", this.form)
            .then((response) => {
              this.$emit("onAddItem", response.data.data);
              this.onClose();
            })
            .finally(() => {
              this.loading = false;
              this.errors = {};
            })
            .catch((error) => {
              this.axiosError(error);
            });
        })
        .catch((error) => this.axiosError(error))
        .finally(() => (this.loading = false));
    },
    onSubmit() {
      if (this.room) {
        this.onUpdate();
      } else {
        this.onStore();
      }
    },
    onClose() {
      this.$emit("update:visible", false);
    },
    onCreate() {
      if (this.room) {
        this.form = this.room;
        this.title = "Editar piso";
      } else {
        this.title = "Crear piso";
        this.form = {
          active: true,
        };
      }
    },
  },
};
</script>

<template>
  <el-dialog
    :title="title"
    :visible="visible"
    @close="onClose"
    @open="onCreate"
    width="800px"
  >
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>Tarifa</th>
            <th>Precio</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="r in roomRates" :key="r.id">
            <td>{{ r.rate.description }}</td>
            <td>{{ r.price | toDecimals }}</td>
            <td class="text-center">
              <el-button @click="onDeleteRate(r)" type="danger">
                <i class="fa fa-trash"></i>
              </el-button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <el-card v-if="addMode">
      <span slot="header" class="clearfix">Agregar tarifa</span>
      <form class="row" @submit.prevent="onAddRate">
        <div class="form-group col-4">
          <select
            id="rate"
            class="form-control"
            required
            v-model="form.hotel_rate_id"
            :class="{ 'is-invalid': errors.hotel_rate_id }"
          >
            <option value="">Selecciona una tarifa</option>
            <option v-for="r in rates" :key="r.id" :value="r.id">
              {{ r.description }}
            </option>
          </select>
          <div v-if="errors.hotel_rate_id" class="invalid-feedback">
            {{ errors.hotel_rate_id[0] }}
          </div>
        </div>
        <div class="form-group col-4">
          <input
            type="number"
            id="price"
            class="form-control"
            placeholder="Ingresa un precio"
            required
            v-model="form.price"
            :class="{ 'is-invalid': errors.price }"
          />
          <div v-if="errors.price" class="invalid-feedback">
            {{ errors.price[0] }}
          </div>
        </div>
        <div class="col-4">
          <div class="row">
            <div class="col-6">
              <el-button
                type="primary"
                native-type="submit"
                class="btn-block"
                :disabled="loading"
              >
                <i class="fa fa-save"></i>
              </el-button>
            </div>
            <div class="col-6">
              <el-button
                type="danger"
                class="btn-block"
                :disabled="loading"
                @click="addMode = false"
              >
                <i class="fa fa-times"></i>
              </el-button>
            </div>
          </div>
        </div>
      </form>
    </el-card>
    <div class="text-center" v-if="!addMode">
      <el-button type="primary" @click="addMode = true">
        <i class="fa fa-plus"></i>
        <span class="ml-2">Agregar tarifa</span>
      </el-button>
    </div>
  </el-dialog>
</template>

<script>
export default {
  props: {
    visible: {
      type: Boolean,
      required: true,
    },
    room: {
      type: Object,
      required: false,
    },
  },
  data() {
    return {
      rates: [],
      title: "",
      loading: false,
      errors: {},
      form: {
        hotel_rate_id: "",
        price: 0,
      },
      roomRates: [],
      addMode: false,
    };
  },
  methods: {
    onDeleteRate(rate) {
      this.$confirm(
        `¿estás seguro de eliminar la tarifa seleccionada: ${rate.rate.description}?`,
        "Atención",
        {
          confirmButtonText: "Si, eliminar",
          cancelButtonText: "No, cerrar",
          type: "warning",
        }
      )
        .then(() => {
          this.$http
            .delete(`/hotels/rooms/${this.room.id}/rates/${rate.id}/delete`)
            .then((response) => {
              this.$message({
                type: "success",
                message: response.data.message,
              });
              this.roomRates = this.roomRates.filter((r) => r.id !== rate.id);
              this.$emit("onDeleteRate", rate.id);
            })
            .catch((error) => {
              this.axiosError(error);
            });
        })
        .catch();
    },
    onFetchData() {
      this.loading = true;
      this.$http
        .get(`/hotels/rooms/${this.room.id}/rates`)
        .then((response) => {
          this.roomRates = response.data.room_rates;
        })
        .finally(() => {
          this.loading = false;
        });
    },
    onAddRate() {
      this.loading = true;
      this.form.hotel_room_id = this.room.id;
      this.$http
        .post(`/hotels/rooms/${this.room.id}/rates/store`, this.form)
        .then((response) => {
          this.roomRates.push(response.data.room_rate);
          this.form = {
            hotel_rate_id: "",
            price: 0,
          };
          this.$emit("onAddRoomRate", response.data.room_rate);
        })
        .catch((error) => {
          this.axiosError(error);
        })
        .finally(() => {
          this.loading = false;
        });
    },
    onClose() {
      this.$emit("update:visible", false);
    },
    onCreate() {
      this.title = `TARIFAS DE LA HABITACIÓN ${this.room.name}`;
      this.onFetchData();
      if (this.rates.length === 0) {
        this.onFetchRates();
      }
    },
    onFetchRates() {
      this.$http.get("/hotels/rooms/tables").then((response) => {
        this.rates = response.data.rates;
      });
    },
  },
};
</script>

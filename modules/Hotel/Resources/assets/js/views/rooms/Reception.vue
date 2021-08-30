<template>
  <div>
    <div class="page-header pr-0">
      <h2>
        <a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a>
      </h2>
      <ol class="breadcrumbs">
        <li class="active"><span>VISTA GENERAL RECEPCIÓN</span></li>
      </ol>
    </div>
    <div class="card mb-0">
      <div class="card-header bg-info">
        <h3 class="my-0">Vista general recepción</h3>
      </div>
      <div class="card-body">
        <div class="d-flex justify-content-lg-between">
          <div style="max-width: 120px">
            <el-select
              v-model="hotel_floor_id"
              placeholder="Piso"
              :disabled="loading"
              clearable
            >
              <el-option
                v-for="f in floors"
                :key="f.id"
                :value="f.id"
                :label="f.description"
              >
              </el-option>
            </el-select>
          </div>
          <el-button-group>
            <el-button
              v-for="st in roomStatus"
              :key="st"
              class="btn btn-sm"
              size="mini"
              :class="onGetColorStatus(st)"
              @click="onFilterByStatus(st)"
              :disabled="loading"
              >{{ st }}</el-button
            >
          </el-button-group>
        </div>
        <hr />
        <div class="row">
          <div class="col-6 col-md-3 mb-4" v-for="ro in items" :key="ro.id">
            <el-card
              :class="onGetColorStatus(ro.status)"
              style="min-height: 160px"
            >
              <div
                slot="header"
                class="d-flex align-items-center justify-content-between"
              >
                <span>{{ ro.status }}: {{ ro.name }}</span>
                <template v-if="ro.status === 'LIMPIEZA'">
                  <el-button
                    style="margin-left: auto"
                    type="primary"
                    title="Ir al checkout"
                    :loading="loading"
                    :disabled="loading"
                    @click="onFinalizeClean(ro)"
                  >
                    <i class="fa fa-broom"></i>
                  </el-button>
                </template>
                <template v-if="ro.status === 'OCUPADO'">
                  <el-button
                    style="margin-left: auto"
                    type="primary"
                    title="Ir al checkout"
                    @click="onGoToCheckout(ro)"
                  >
                    <i class="fa fa-arrow-circle-right"></i>
                  </el-button>
                  <el-button
                    style="margin-left: 0.5rem"
                    type="primary"
                    title="Agregar productos"
                    @click="onGoToAddProducts(ro)"
                  >
                    <i class="fa fa-plus-circle"></i>
                  </el-button>
                </template>
                <el-button
                  v-if="ro.status === 'DISPONIBLE'"
                  type="primary"
                  @click="onToRent(ro)"
                >
                  <i class="fa fa-arrow-circle-left"></i>
                </el-button>
              </div>
              <div
                class="d-flex justify-content-center align-items-center"
                v-if="ro.status === 'DISPONIBLE'"
              >
                <i class="fa fa-bed fa-2x mt-2"></i>
                <span class="h3 ml-3">{{ ro.name }}</span>
              </div>
              <div
                class="d-flex justify-content-center align-items-center"
                v-if="ro.status === 'OCUPADO'"
              >
                <i class="fa fa-user-tie fa-2x"></i>
                <span class="h6 ml-3">{{ ro.rent.customer.name }}</span>
              </div>
            </el-card>
          </div>
        </div>
      </div>
    </div>
    <ModalRoomRates
      :room="room"
      :visible.sync="openModalRoomRates"
      @onAddRoomRate="onAddRoomRate"
      @onDeleteRate="onDeleteRate"
    ></ModalRoomRates>
  </div>
</template>

<script>
import ModalRoomRates from "./RoomRates";

export default {
  components: {
    ModalRoomRates,
  },
  props: {
    roomStatus: {
      type: Array,
      required: true,
    },
    floors: {
      type: Array,
      required: true,
    },
    rooms: {
      type: Array,
      required: true,
      default: [],
    },
  },
  data() {
    return {
      hotel_floor_id: "",
      loading: false,
      items: [],
      room: null,
      openModalRoomRates: false,
    };
  },
  mounted() {
    this.items = this.rooms;
  },
  watch: {
    hotel_floor_id() {
      this.onFilterByStatus();
    },
  },
  methods: {
    onFinalizeClean(room) {
      const text = `Está a punto de terminar la limpieza de la habitación ${room.name}`;
      this.$confirm(text, "Atención", {
        confirmButtonText: "Si",
        cancelButtonText: "No",
        type: "warning",
      })
        .then(() => {
          this.loading = true;
          const payload = {
            status: "DISPONIBLE",
          };
          this.$http
            .post(`/hotels/rooms/${room.id}/change-status`, payload)
            .then((response) => {
              room.status = "DISPONIBLE";
              this.items = this.items.map((r) => {
                if (r.id === room.id) {
                  return room;
                }
                return r;
              });
              this.$message({
                type: "success",
                message: response.data.message,
              });
            })
            .finally(() => (this.loading = false));
        })
        .catch();
    },
    onGoToCheckout(room) {
      window.location.href = `/hotels/reception/${room.rent.id}/rent/checkout`;
    },
    onGoToAddProducts(room) {
      window.location.href = `/hotels/reception/${room.rent.id}/rent/products`;
    },
    onDeleteRate(rateId) {
      this.room.rates = this.room.rates.filter((r) => r.id !== rateId);
    },
    onAddRoomRate(rate) {
      this.room.rates.push(rate);
    },
    onToRent(room) {
      if (room.rates.length > 0) {
        window.location.href = `/hotels/reception/${room.id}/rent`;
      } else {
        this.room = room;
        this.openModalRoomRates = true;
      }
    },
    onFilterByStatus(status = "") {
      this.loading = true;
      const params = {
        status,
        hotel_floor_id: this.hotel_floor_id,
      };
      this.$http
        .get("/hotels/reception", { params })
        .then((response) => {
          this.items = response.data.rooms;
        })
        .finally(() => {
          this.loading = false;
        });
    },
    onGetColorStatus(status) {
      if (status === "DISPONIBLE") {
        return "btn-success";
      } else if (status === "MANTENIMIENTO") {
        return "btn-warning";
      } else if (status === "OCUPADO") {
        return "btn-danger";
      } else if (status === "LIMPIEZA") {
        return "btn-info";
      }
      return "";
    },
  },
};
</script>

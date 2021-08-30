<template>
  <div>
    <div class="page-header pr-0">
      <h2>
        <a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a>
      </h2>
      <ol class="breadcrumbs">
        <li class="active"><span>REGISTRO DE HABITACIONES</span></li>
      </ol>
      <div class="right-wrapper pull-right">
        <div class="btn-group flex-wrap">
          <button
            type="button"
            class="btn btn-custom btn-sm mt-2 mr-2"
            @click="onCreate"
          >
            <i class="fa fa-plus-circle"></i> Nuevo
          </button>
        </div>
      </div>
    </div>
    <div class="card mb-0">
      <div class="card-header bg-info">
        <h3 class="my-0">Listado de habitaciones</h3>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-12 col-md-2 mb-3">
            <div class="form-group">
              <select
                class="form-control"
                v-model="filter.status"
                @change="onFilter"
              >
                <option value="">Filtrar por estado</option>
                <option v-for="st in roomStatus" :key="st" :value="st">
                  {{ st }}
                </option>
              </select>
            </div>
          </div>
          <div class="col-12 col-md-2 mb-3">
            <div class="form-group">
              <select
                class="form-control"
                v-model="filter.hotel_floor_id"
                @change="onFilter"
              >
                <option value="">Filtrar por piso</option>
                <option v-for="fl in floors" :key="fl.id" :value="fl.id">
                  {{ fl.description }}
                </option>
              </select>
            </div>
          </div>
          <div class="col-12 col-md-2 mb-3">
            <div class="form-group">
              <select
                class="form-control"
                v-model="filter.hotel_category_id"
                @change="onFilter"
              >
                <option value="">Filtrar por categoría</option>
                <option v-for="ca in categories" :key="ca.id" :value="ca.id">
                  {{ ca.description }}
                </option>
              </select>
            </div>
          </div>
          <div class="col-12 col-md-2 mb-3">
            <form class="form-group" @submit.prevent="onFilter">
              <div class="input-group mb-3">
                <input
                  type="text"
                  class="form-control"
                  placeholder="Filtrar por nombre"
                  v-model="filter.name"
                />
                <div class="input-group-append">
                  <button
                    class="btn btn-outline-secondary"
                    type="submit"
                    style="border-color: #ced4da"
                  >
                    <i class="fa fa-search"></i>
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th></th>
                <th>Habitación</th>
                <th>Categoría</th>
                <th>Piso</th>
                <th>Tarifas</th>
                <th>Estado</th>
                <th></th>
                <th>Visible</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="item in items"
                :key="item.id"
                :class="{ 'table-info': !item.active }"
              >
                <td class="text-right">{{ item.id }}</td>
                <td>{{ item.name }}</td>
                <td>{{ item.category.description }}</td>
                <td>{{ item.floor.description }}</td>
                <td>
                  <el-button @click="onShowMyRates(item)">
                    <i class="fa fa-money-bill-alt"></i>
                  </el-button>
                </td>
                <td>{{ item.status }}</td>
                <td class="text-center">
                  <el-button
                    type="warning"
                    v-if="item.status === 'DISPONIBLE'"
                    @click="onChangeStatus(item, 'MANTENIMIENTO')"
                    :disabled="loading"
                  >
                    <i class="fa fa-tools"></i>
                    <span class="ml-2">MANTENIMIENTO</span>
                  </el-button>
                  <el-button
                    type="info"
                    v-if="item.status === 'MANTENIMIENTO'"
                    @click="onChangeStatus(item, 'DISPONIBLE')"
                    :disabled="loading"
                  >
                    <i class="fa fa-check"></i>
                    <span class="ml-2">HABILITAR</span>
                  </el-button>
                </td>
                <td class="text-center">
                  <span v-if="item.active">Si</span>
                  <span v-else>No</span>
                </td>
                <td class="text-center">
                  <el-button
                    type="success"
                    @click="onEdit(item)"
                    :disabled="loading"
                  >
                    <i class="fa fa-edit"></i>
                  </el-button>
                  <el-button
                    type="danger"
                    @click="onDelete(item)"
                    :disabled="loading"
                  >
                    <i class="fa fa-trash"></i>
                  </el-button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="text-center">
          <el-pagination
            :page-size="paginator.per_page"
            :page-count="paginator.size"
            layout="prev, pager, next"
            :total="paginator.total"
            :current-page.sync="filter.page"
            @current-change="onFilter"
          >
          </el-pagination>
        </div>
      </div>
    </div>
    <ModalAddEdit
      :visible.sync="openModalAddEdit"
      @onAddItem="onAddItem"
      @onUpdateItem="onUpdateItem"
      :room="room"
      :categories="categories"
      :floors="floors"
    ></ModalAddEdit>
    <ModalRoomRates
      :room="room"
      :visible.sync="openModalRoomRates"
    ></ModalRoomRates>
  </div>
</template>

<script>
import ModalAddEdit from "./AddEdit";
import ModalRoomRates from "./RoomRates";

export default {
  props: {
    roomStatus: {
      type: Array,
      required: true,
    },
    rooms: {
      type: Object,
      required: true,
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
  components: {
    ModalAddEdit,
    ModalRoomRates,
  },
  data() {
    return {
      items: [],
      room: null,
      openModalAddEdit: false,
      loading: false,
      openModalRoomRates: false,
      filter: {
        name: "",
        hotel_category_id: "",
        hotel_floor_id: "",
        status: "",
        page: 1,
      },
      paginator: {
        size: 1,
        total: 1,
        per_page: 1
      },
    };
  },
  mounted() {
    this.items = this.rooms.data;
    this.paginator.size = this.rooms.last_page;
    this.paginator.total = this.rooms.total;
    this.paginator.per_page = this.rooms.per_page;
  },
  methods: {
    onFilter() {
      this.loading = true;
      const params = this.filter;
      this.$http
        .get("/hotels/rooms", { params })
        .then((response) => {
          this.items = response.data.rooms.data;
          this.paginator.size = response.data.rooms.last_page;
          this.paginator.total = response.data.rooms.total;
          this.paginator.per_page = response.data.rooms.per_page;
        })
        .finally(() => {
          this.loading = false;
        });
    },
    onChangeStatus(room, status) {
      this.loading = true;

      const payload = {
        status,
      };

      this.$http
        .post(`/hotels/rooms/${room.id}/change-status`, payload)
        .then((response) => {
          room.status = status;
          this.$message({
            message: response.data.message,
            type: "success",
          });
        })
        .finally(() => {
          this.loading = false;
        });
    },
    onShowMyRates(item) {
      this.room = { ...item };
      this.openModalRoomRates = true;
    },
    onDelete(item) {
      this.$confirm(
        `¿estás seguro de eliminar al elemento ${item.name}?`,
        "Atención",
        {
          confirmButtonText: "Si, continuar",
          cancelButtonText: "No, cerrar",
          type: "warning",
        }
      )
        .then(() => {
          this.$http
            .delete(`/hotels/rooms/${item.id}/delete`)
            .then((response) => {
              this.$message({
                type: "success",
                message: response.data.message,
              });
              this.items = this.items.filter((i) => i.id !== item.id);
            })
            .catch((error) => {
              this.axiosError(error);
            });
        })
        .catch();
    },
    onEdit(item) {
      this.room = { ...item };
      this.openModalAddEdit = true;
    },
    onUpdateItem(data) {
      this.items = this.items.map((i) => {
        if (i.id === data.id) {
          return data;
        }
        return i;
      });
    },
    onAddItem(data) {
      this.items.unshift(data);
    },
    onCreate() {
      this.room = null;
      this.openModalAddEdit = true;
    },
  },
};
</script>

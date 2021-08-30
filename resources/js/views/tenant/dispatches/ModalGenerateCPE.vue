<template>
  <div>
    <el-dialog
      title="Generar comprobante desde múltiples guías"
      :visible="show"
      @open="onOpened"
      :close-on-click-modal="false"
      :close-on-press-escape="false"
      @close="onClose"
    >
      <div class="row">
        <div class="col-3">
          <el-select
            v-model="filter.type"
            @click="onFetchClients"
            :disabled="loading"
          >
            <el-option
              key="document"
              value="document"
              label="# de documento"
            ></el-option>
            <el-option key="name" value="name" label="Nombres"></el-option>
          </el-select>
        </div>
        <div class="col-9 form-group">
          <el-select
            v-model="form.client_id"
            filterable
            remote
            reserve-keyword
            placeholder="Ingrese uno más caracteres"
            :remote-method="onFindClients"
            :loading="loading"
            @change="onFindDispatches"
          >
            <el-option
              v-for="item in clients"
              :key="item.id"
              :label="item.name"
              :value="item.id"
            >
            </el-option>
          </el-select>
        </div>
      </div>
      <div class="table-responsive pt-5" v-if="dispatches">
        <span>Seleccione una o más guías para poder continuar</span>
        <div
          v-if="errors.dispatches_id"
          class="alert alert-warning"
          role="alert"
        >
          {{ errors.dispatches_id[0] }}
        </div>
        <table class="table table-hover table-stripe">
          <thead>
            <tr>
              <th></th>
              <th>Guía</th>
              <th>Fecha de emisión</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="dis in dispatches" :key="dis.id">
              <td>
                <el-switch
                  v-model="dis.selected"
                  @change="onFillSelectedDispatches"
                ></el-switch>
              </td>
              <td>
                <span>{{ dis.series }}</span
                >-
                <span>{{ dis.number | pad(0, 3) }}</span>
              </td>
              <td>{{ dis.date_of_issue | toDate }}</td>
            </tr>
          </tbody>
        </table>
        <div class="text-center">
          <el-button
            v-if="form.selecteds.length > 0"
            type="primary"
            :disabled="loading"
            @click="onFetchDispatchItems"
            >Generar CPE</el-button
          >
          <el-button :disabled="loading" @click="onClose">Cerrar</el-button>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>
export default {
  props: {
    show: {
      required: true,
      type: Boolean,
    },
  },
  data() {
    return {
      title: "",
      loading: false,
      clients: [],
      filter: {
        type: "name",
        name: null,
      },
      form: {
        client_id: null,
        selecteds: [],
      },
      dispatches: [],
      errors: {},
    };
  },
  methods: {
    onFetchDispatchItems() {
      if (this.form.selecteds.length === 0) {
        this.$message({
          message: "Seleccione una o más guías por favor",
          type: "warning",
        });
        return;
      }
      this.loading = true;
      const data = {
        dispatches_id: this.form.selecteds,
      };
      this.$http
        .post("/dispatches/items", data)
        .then((response) => {
          const dispatches = [];
          this.dispatches.map((d) => {
            if (d.selected) {
              dispatches.push(`${d.series}-${d.number}`);
            }
          });
          const items = response.data.data;
          const client = this.clients.find((c) => c.id === this.form.client_id);
          localStorage.setItem("client", JSON.stringify(client));
          localStorage.setItem("items", JSON.stringify(items));
          localStorage.setItem("dispatches", JSON.stringify(dispatches));
          this.onClose();
          window.location.href = "/documents/create";
        })
        .catch((error) => this.axiosError(error))
        .finally(() => (this.loading = false));
    },
    onFillSelectedDispatches() {
      this.form.selecteds = [];
      this.dispatches.map((d) => {
        if (d.selected) {
          this.form.selecteds.push(d.id);
        }
      });
    },
    onFindDispatches() {
      this.form.selecteds = [];
      this.loading = true;
      this.$http
        .get(`/dispatches/client/${this.form.client_id}`)
        .then((response) => {
          this.dispatches = response.data.data.map((d) => {
            d.selected = false;
            return d;
          });
        })
        .finally(() => (this.loading = false));
    },
    onFindClients(query) {
      this.filter.name = query;
      this.onFetchClients();
    },
    onFetchClients() {
      this.loading = true;
      this.dispatches = [];
      this.form.selecteds = [];
      const params = this.filter;
      this.$http
        .get("/customers/list", { params })
        .then((response) => {
          this.clients = response.data.data;
        })
        .finally(() => (this.loading = false));
    },
    onOpened() {
      this.filter.type = "name";
      this.filter.name = null;
      this.form.client_id = null;
      this.onFetchClients();
    },
    onClose() {
      this.dispatches = [];
      this.$emit("update:show", false);
    },
  },
};
</script>

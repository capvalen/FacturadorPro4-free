<template>
  <div>
    <header class="page-header">
      <h2>
        <a href="/dashboard">
          <i class="fa fa-list-alt"></i>
        </a>
      </h2>
      <ol class="breadcrumbs">
        <li class="active">
          <span>Dashboard</span>
        </li>
      </ol>
    </header>
    <div class="row">
      <div class="col-lg-8">
        <div class="card">
          <div class="card-body p-0">
            <div class="row">
              <div class="col-lg-12">
                <div class="chart-data-selector ready pl-3 pr-4 pt-4">
                  <div class="chart-data-selector-items">
                    <chart-line :data="dataChartLine" v-if="loaded"></chart-line>
                  </div>
                </div>
              </div>
            </div>

            <div class="row px-4 mt-2 pb-3">
              <div class="col-2 font-weight-bold text-primary">{{year}}</div>
              <div class="col-10 font-weight-semibold text-right">Comprobantes generados por mes</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="row">
          <div class="col-md-12">
            <section class="card card-horizontal">
              <header class="card-header bg-success">
                <div class="card-header-icon">
                  <i class="fas fa-users"></i>
                </div>
              </header>
              <div class="card-body p-4 text-center">
                <p class="font-weight-semibold mb-0 mx-4">Total Clientes</p>
                <h2 class="font-weight-semibold mt-0">{{ records.length }}</h2>
                <div class="summary-footer">
                  <a class="text-muted text-uppercase" href="#client-list">Ver todos</a>
                </div>
              </div>
            </section>
          </div>
          <div class="col-md-12 mb-0">
            <section class="card card-horizontal">
              <header class="card-header bg-info">
                <div class="card-header-icon">
                  <i class="fas fa-file-alt"></i>
                </div>
              </header>
              <div class="card-body p-4 text-center">
                <p class="font-weight-semibold mb-0 mt-3">Total Comprobantes</p>
                <h2 class="font-weight-semibold mt-0 mb-3">{{ total_documents }}</h2>
              </div>
            </section>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-3">
        <section class="card card-featured-left card-featured-primary mb-4">
          <div class="card-body">
            <div class="widget-summary widget-summary-md">
              <div class="widget-summary-col widget-summary-col-icon">
                <div class="summary-icon text-secondary">
                  <div class="progress1 mx-auto" :data-value='discUsed'>
                    <span class="progress1-left">
                      <span class="progress1-bar border-primary"></span>
                    </span>
                    <span class="progress1-right">
                      <span class="progress1-bar border-primary"></span>
                    </span>
                    <div class="progress1-value w-100 h-100 rounded-circle d-flex align-items-center justify-content-center">
                      <div class="font-weight-bold">{{discUsed}}<small class="small"></small></div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="widget-summary-col">
                <div class="summary">
                  <h4 class="title"><!-- Disco <br> Duro --></h4>
                  <div class="info">
                    <strong class="amount">Disco Duro</strong><br>
                    <!-- <span class="text-warning" v-if="discUsed == 0">no se pudo obtener</span> -->
                  </div>
                </div>
                <div class="summary-footer d-block">
                  <a href="https://docs.google.com/document/d/1hpEQUs9OFha_35yyLb1cMKeluD-dEku5lQsQ3TJFib8/edit" target="BLANK" class="text-muted text-uppercase">Incrementar</a>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
      <div class="col-md-3">
        <section class="card card-featured-left card-featured-primary mb-4">
          <div class="card-body">
            <div class="widget-summary widget-summary-md">
              <div class="widget-summary-col widget-summary-col-icon">
                <div class="summary-icon text-secondary">
                  <div class="progress1 mx-auto" :data-value='iUsed'>
                    <span class="progress1-left">
                      <span class="progress1-bar border-primary"></span>
                    </span>
                    <span class="progress1-right">
                      <span class="progress1-bar border-primary"></span>
                    </span>
                    <div class="progress1-value w-100 h-100 rounded-circle d-flex align-items-center justify-content-center">
                      <div class="font-weight-bold">{{iUsed}}<small class="small"></small></div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="widget-summary-col">
                <div class="summary">
                  <h4 class="title"><!-- Disco <br> Duro --></h4>
                  <div class="info">
                    <strong class="amount">Inodes</strong>
                    <!-- <span class="text-primary">(14 unread)</span> -->
                  </div>
                </div>
                <div class="summary-footer d-block">
                  <a href="https://drive.google.com/open?id=1foPKDI3V3Z9uKTjRc2SPSoztVSOBevPAluT2BqFbfxA" target="BLANK" class="text-muted text-uppercase">Limpiar</a>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
      <div class="col-md-3">
        <section class="card card-featured-left card-featured-primary mb-4">
          <div class="card-body">
            <div class="widget-summary widget-summary-md">
              <div class="widget-summary-col widget-summary-col-icon">
                <div class="summary-icon text-secondary">
                  <div class="progress1 mx-auto" data-value='100'>
                    <span class="progress1-left">
                      <span class="progress1-bar border-tertiary"></span>
                    </span>
                    <span class="progress1-right">
                      <span class="progress1-bar border-tertiary"></span>
                    </span>
                    <div class="progress1-value w-100 h-100 rounded-circle d-flex align-items-center justify-content-center">
                      <div class="font-weight-bold">{{storageSize}}<small class="small"></small></div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="widget-summary-col">
                <div class="summary">
                  <h4 class="title"><!-- Disco <br> Duro --></h4>
                  <div class="info">
                    <strong class="amount">Archivos <br> Generados</strong>
                    <!-- <span class="text-primary">(14 unread)</span> -->
                  </div>
                </div>
                <div class="summary-footer">
                  <!-- <a class="text-muted text-uppercase">(view all)</a> -->
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
      <div class="col-md-3">
        <section class="card card-featured-left card-featured-primary mb-4">
          <div class="card-body">
            <div class="widget-summary widget-summary-md">
              <div class="widget-summary-col widget-summary-col-icon">
                <div class="summary-icon" style="background-color: #292961">
                  <i class="fab fa-gitlab"></i>
                </div>
              </div>
              <div class="widget-summary-col">
                <div class="summary">
                  <h4 class="title"><!-- Disco <br> Duro --></h4>
                  <div class="info">
                    <strong class="amount">Versión</strong><br>
                    <span class="text-primary">{{version}}</span>
                  </div>
                </div>
                <div class="summary-footer">
                  <!-- <a class="text-muted text-uppercase">(view all)</a> -->
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>

    <div class="card" id="client-list">
      <div class="card-header bg-info">Listado de Clientes</div>
      <div class="card-body">
        <div class="row">
          <div class="col">
            <button
              type="button"
              class="btn btn-custom btn-sm mt-2 mr-2 mb-3"
              @click.prevent="clickCreate()"
            >
              <i class="fa fa-plus-circle"></i> Nuevo
            </button>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>#</th>
                <th>Hostname</th>
                <th>Nombre</th>
                <th>RUC</th>
                <th>Plan</th>
                <th>Correo</th>
                <th>Entorno</th>
                <th class="text-center">Total de Comprobantes</th>
                  <th class="text-right">Inicio Ciclo Facturacion</th>
                  <th class="text-center">Comprobantes Ciclo Facturacion</th>
                  <th class="text-center">Usuarios</th>
                <th class="text-center">F.Creación</th>

                <th class="text-center">Bloquear cuenta</th>

                <th class="text-right">Limitar Doc.</th>
                <th class="text-center">Limitar Usuarios</th>
                <th class="text-right">Acciones</th>
                <th class="text-right">Pagos</th>
                <th class="text-right">E. Cuenta</th>
                <th class="text-right">Editar</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(row, index) in records">
                <td>{{ index + 1 }}</td>
                <td>{{ row.hostname }}</td>
                <td>{{ row.name }}</td>
                <td>{{ row.number }}</td>
                <td>{{ row.plan }}</td>
                <td>{{ row.email }}</td>
                <td>
                  <span v-if="row.soap_type == '01'" class="badge badge-default">Demo</span>
                  <span v-if="row.soap_type == '02'" class="badge badge-success">Producción</span>
                  <span v-if="row.soap_type == '03'" class="badge badge-info">Interno</span>
                </td>
                <td class="text-center">
                    <label>
                        <strong>{{ row.count_doc }}</strong>
                    </label>
                </td>
                  <td>
                      <template v-if="row.start_billing_cycle">
                          <span></span>
                          <span>{{row.start_billing_cycle}}</span>
                      </template>
                      <template v-else>
                          <el-date-picker
                              @change="setStartBillingCycle($event, row.id)"
                              v-model="row.select_date_billing"
                              value-format="yyyy-MM-dd"
                              type="date"
                              placeholder="..."
                          ></el-date-picker>
                      </template>
                  </td>
                  <td class="text-center">
                      <strong>
                          {{ row.count_doc_month ? row.count_doc_month : 0 }} /
                          <template v-if="row.max_documents == 0">
                              <i class="fas fa-infinity"></i>
                          </template>
                          <template v-else>
                              <strong>{{ row.max_documents }}</strong>
                          </template>
                      </strong>
                  </td>

                  <td class="text-center">
                  <template v-if="row.max_users !== 0 && row.count_user > row.max_users">
                    <el-popover
                      placement="top-start"
                      width="220"
                      trigger="hover"
                      :content="text_limit_users"
                    >
                      <label slot="reference" class="text-danger">
                        <strong>{{ row.count_user }}</strong>
                      </label>
                    </el-popover>
                  </template>
                  <template v-else>
                    <label>
                      <strong>{{ row.count_user }}</strong>
                    </label>
                  </template>
                  /
                  <template v-if="row.max_users == 0">
                    <i class="fas fa-infinity"></i>
                  </template>
                  <template v-else>
                    <strong>{{ row.max_users }}</strong>
                  </template>
                </td>
                <td class="text-center">{{ row.created_at }}</td>

                <td class="text-center">
                  <template v-if="!row.locked">
                    <el-switch
                      style="display: block"
                      v-model="row.locked_tenant"
                      @change="changeLockedTenant(row)"
                    ></el-switch>
                  </template>
                </td>

                <td class="text-center">
                  <el-switch
                    style="display: block"
                    v-model="row.locked_emission"
                    @change="changeLockedEmission(row)"
                  ></el-switch>
                </td>

                <td class="text-center">
                  <el-switch
                    style="display: block"
                    v-model="row.locked_users"
                    @change="changeLockedUser(row)"
                  ></el-switch>
                </td>

                <td class="text-right">
                  <template v-if="!row.locked">
                    <el-tooltip content="Se ingresa con el RUC" placement="top">
                      <button
                        type="button"
                        class="btn waves-effect waves-light btn-xs btn-info m-1__2"
                        @click.prevent="clickPassword(row.id)"
                      >Resetear clave</button>
                    </el-tooltip>
                    <button
                      v-if="deletePermission == true"
                      type="button"
                      class="btn waves-effect waves-light btn-xs btn-danger m-1__2"
                      @click.prevent="clickDelete(row.id)"
                    >Eliminar</button>
                  </template>
                </td>
                <td class="text-right">
                  <button
                    type="button"
                    class="btn waves-effect waves-light btn-xs btn-warning m-1__2"
                    @click.prevent="clickPayments(row.id)"
                  >Pagos</button>
                </td>
                <td class="text-right">
                  <button
                    type="button"
                    class="btn waves-effect waves-light btn-xs btn-primary m-1__2"
                    @click.prevent="clickAccountStatus(row.id)"
                  >E. Cuenta</button>
                </td>
                <td class="text-right">
                  <button
                    type="button"
                    class="btn waves-effect waves-light btn-xs btn-primary m-1__2"
                    @click.prevent="clickEdit(row.id)"
                  >Editar</button>
                </td>


              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <system-clients-form :showDialog.sync="showDialog" :recordId="recordId"></system-clients-form>

    <!--<system-clients-form-edit :showDialog.sync="showDialogEdit"
    :recordId="recordId"></system-clients-form-edit>-->

    <client-payments :showDialog.sync="showDialogPayments" :clientId="recordId"></client-payments>

    <account-status :showDialog.sync="showDialogAccountStatus" :clientId="recordId"></account-status>
  </div>
</template>

<script>
import CompaniesForm from "./form.vue";
//   import CompaniesFormEdit from './form_edit.vue'
import { deletable } from "../../../mixins/deletable";
import { changeable } from "../../../mixins/changeable";
import ChartLine from "./charts/Line";
import ClientPayments from "./partials/payments.vue";
import AccountStatus from "./partials/account_status.vue";

export default {
  mixins: [deletable, changeable],
  props: ['deletePermission', 'discUsed', 'iUsed', 'storageSize', 'version'],
  components: { CompaniesForm, ChartLine, ClientPayments, AccountStatus },
  data() {
    return {
      selectBillingDate: "",
      showDialogEdit: false,
      showDialog: false,
      showDialogPayments: false,
      showDialogAccountStatus: false,
      resource: "clients",
      recordId: null,
      records: [],
      text_limit_doc: null,
      text_limit_users: null,
      loaded: false,
      year: moment().format('YYYY'),
      total_documents: 0,
      dataChartLine: {
        labels: null,
        datasets: [
          {
            // label: 'Data One',
            // backgroundColor: '#f87979',
            data: null
          }
        ]
      }
    };
  },
  async mounted() {
    this.loaded = false;
    await this.$http.get(`/${this.resource}/charts`).then(response => {
      let line = response.data.line;
      this.dataChartLine.labels = line.labels;
      this.dataChartLine.datasets[0].data = line.data;
      this.total_documents = response.data.total_documents;
      // console.log(response.data)
      // this.records = response.data.data
    });
    this.loaded = true;
  },
  created() {
    this.$eventHub.$on("reloadData", () => {
      this.getData();
    });
    this.getData();

    this.text_limit_doc = "El límite de comprobantes fue superado";
    this.text_limit_users = "El límite de usuarios fue superado";
  },
  methods: {
    changeLockedTenant(row) {
      this.$http
        .post(`${this.resource}/locked_tenant`, row)
        .then(response => {
          if (response.data.success) {
            this.$message.success(response.data.message);
            this.$eventHub.$emit("reloadData");
          } else {
            this.$message.error(response.data.message);
          }
        })
        .catch(error => {
          if (error.response.status === 500) {
            this.$message.error(error.response.data.message);
          } else {
            console.log(error.response);
          }
        })
        .then(() => {});
    },

    changeLockedUser(row) {
      this.$http
        .post(`${this.resource}/locked_user`, row)
        .then(response => {
          if (response.data.success) {
            this.$message.success(response.data.message);
            this.$eventHub.$emit("reloadData");
          } else {
            this.$message.error(response.data.message);
          }
        })
        .catch(error => {
          if (error.response.status === 500) {
            this.$message.error(error.response.data.message);
          } else {
            console.log(error.response);
          }
        })
        .then(() => {});
    },

    setStartBillingCycle(event, id) {
      this.$http
        .post(`${this.resource}/set_billing_cycle`, {
          id: id,
          start_billing_cycle: event
        })
        .then(response => {
          if (response.data.success) {
            this.$message.success(response.data.message);
          } else {
            this.$message.error(response.data.message);
          }
        })
        .catch(error => {
          if (error.response.status === 500) {
            this.$message.error(error.response.data.message);
          } else {
            console.log(error.response);
          }
        })
        .then(() => {
          this.$eventHub.$emit("reloadData");
        });
    },
    changeLockedEmission(row) {
      this.$http
        .post(`${this.resource}/locked_emission`, row)
        .then(response => {
          if (response.data.success) {
            this.$message.success(response.data.message);
            this.$eventHub.$emit("reloadData");
          } else {
            this.$message.error(response.data.message);
          }
        })
        .catch(error => {
          if (error.response.status === 500) {
            this.$message.error(error.response.data.message);
          } else {
            console.log(error.response);
          }
        })
        .then(() => {});
    },
    getData() {
      this.$http.get(`/${this.resource}/records`).then(response => {
        this.records = response.data.data;
      });
    },
    clickCreate(recordId = null) {
      this.recordId = recordId;
      this.showDialog = true;
    },
    clickPayments(recordId = null) {
      this.recordId = recordId;
      this.showDialogPayments = true;
    },
    clickAccountStatus(recordId = null) {
      this.recordId = recordId;
      this.showDialogAccountStatus = true;
    },
    clickPassword(id) {
      this.change(`/${this.resource}/password/${id}`);
    },
    clickDelete(id) {
      this.destroy(`/${this.resource}/${id}`).then(() =>
        this.$eventHub.$emit("reloadData")
      );
    },
    clickEdit(recordId) {
      this.recordId = recordId;
      this.showDialog = true;
    }
  }
};
</script>

<template>
  <div class="row text-center">
    <div class="col-6 col-md-2" v-if="company.certificate_due">
      <div class="card card-dashboard">
        <div
          class="card-body border border-success"
          :class="{
            'border-danger': isDueWarning
          }"
        >
          <div class="card-title">Fec venc del <br />Certificado</div>
          <span class="text-success font-weight-bold" :class="{
            'text-danger': isDueWarning
          }">{{ company.certificate_due }}</span>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-2">
      <div class="card card-dashboard">
        <div class="card-body border">
          <div class="card-title">Cantidad <br />CPE Emitidos</div>
          <span class="font-weight-bold">{{ total_cpe }}</span>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-2">
      <div class="card card-dashboard border">
        <div class="card-body">
          <div class="card-title">Monto total <br />comprobantes</div>
          <span class="font-weight-bold">{{ document_total_global }}</span>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-2">
      <div class="card card-dashboard">
        <div class="card-body border">
          <div class="card-title">Monto total notas <br />de ventas</div>
          <span class="font-weight-bold">{{ sale_note_total_global }}</span>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-2">
      <div class="card card-dashboard">
        <div class="card-body border">
          <div class="card-title">Monto total <br />general</div>
          <span class="font-weight-bold">{{ total | toDecimals }}</span>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-2" v-if="utilities.totals">
      <div class="card card-dashboard">
        <div class="card-body border">
          <div class="card-title">Utitlidad <br />neta</div>
          <span class="font-weight-bold">{{ utilities.totals.utility }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import moment from "moment";

export default {
  props: ["company", 'utilities'],
  data() {
    return {
      document_total_global: 0,
      total_cpe: 0,
      sale_note_total_global: 0,
      total: 0,
    };
  },
  mounted() {
    this.onFetchData();
  },
  computed: {
    isDueWarning() {
      if (this.company.certificate_due) {
        const dueDate = moment(this.company.certificate_due);

        const now = moment();
        const diffInDays = dueDate.diff(now, 'days')
        return diffInDays <= 15;
      }
      return false;
    },
  },
  methods: {
    onFetchData() {
      this.$http.get("/dashboard/global-data").then((response) => {
        const data = response.data;
        this.document_total_global = data.document_total_global;
        this.total_cpe = data.total_cpe;
        this.sale_note_total_global = data.sale_note_total_global;
        this.total =
          parseFloat(this.document_total_global) +
          parseFloat(this.sale_note_total_global);
      });
    },
  },
};
</script>
<style>
.card-green {
  background-color: green;
  color: white;
}
.is-due-warning {
  background-color: red;
}
.card-green .card-title {
  color: white;
}
</style>

<template>
  <div class="card">
    <div class="card-header bg-info">
      <h3 class="my-0">Listado de Pagos</h3>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col"></div>
      </div>
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr width="100%">
              <th width="5%">#</th>
              <th>Fecha de pago</th>
              <th>Fecha real de pago</th>
              <th>Comentario</th>
              <!-- <th>Referencia Metodo Pago</th> -->
              <th class="text-center">Monto</th>
              <th class="text-center">Estado</th>
              <th class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(row, index) in records">
              <td>{{ index + 1 }}</td>
              <td>{{ row.date_of_payment }}</td>
              <td>{{ row.date_of_payment_real }}</td>
              <td>{{ row.comentario }}</td>
             <!-- <td>{{ row.reference_payment }}</td> -->
              <td class="text-center">{{ row.payment }}</td>
              <td class="text-center">{{ row.state_description }}</td>
              <td class="text-center">
                <button
                  v-if="!row.state"
                  type="button"
                  class="btn waves-effect waves-light btn-xs btn-info"
                  @click.prevent="clickPayment(row.id, row.payment)"
                >Pagar</button>
                <!--<span v-else>Pagado</span> -->
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>


<script>

export default {
  data() {
    return {
      showDialog: false,
      resource: "cuenta",
      recordId: null,
      records: [],
      culqi_: {}
    };
  },
  created() {
    this.getData();
  },
  mounted() {

  },
  methods: {
    demo() {

    },
    getData() {
      this.$http.get(`/${this.resource}/payment_records`).then(response => {
        this.records = response.data.data;
      });
    },
    clickPayment(id, payment) {
      window.execCulqi(id, payment)

    }
  }
};
</script>

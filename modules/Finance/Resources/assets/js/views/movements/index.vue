<template>
  <div class="card mb-0 pt-2 pt-md-0">
    <div class="card-header bg-info">
      <h3 class="my-0">Movimientos de ingresos y egresos</h3>
    </div>
    <div class="card mb-0">
      <div class="card-body">
        <data-table :resource="resource">
          <tr slot="heading">
            <th class="">#</th>
            <th class="">Fecha</th>
            <th class="">Adquiriente</th>
            <th class="">Documento/Transacción</th>
            <th class="">
              Detalle
              <el-tooltip
                class="item"
                effect="dark"
                content="Aplica a Ingresos/Gastos"
                placement="top-start"
              >
                <i class="fa fa-info-circle"></i>
              </el-tooltip>
            </th>
            <th class="">Moneda</th>
            <th class="">Tipo</th>
            <th class="">Ingresos</th>
            <th class="">Gastos</th>
            <th class="">Saldo</th>
          </tr>
          <tr slot-scope="{ index, row }">
            <td>{{ index }}</td>
            <td>{{ row.date_of_payment }}</td>
            <td>
              {{ row.person_name }}<br /><small
                v-text="row.person_number"
              ></small>
            </td>
            <td>
              {{ row.number_full }}<br />
              <small v-text="row.document_type_description"></small>
            </td>
            <td>
              <template v-for="(item, index) in row.items">
                <label :key="index">- {{ item.description }}<br /></label>
              </template>
            </td>
            <td>{{ row.currency_type_id }}</td>
            <td>{{ row.instance_type_description }}</td>
            <td>
              <label v-show="row.input > 0 || row.input != '-'">S/ </label
              >{{ row.input }}
            </td>
            <td>
              <label v-show="row.output > 0 || row.output != '-'">S/ </label
              >{{ row.output }}
            </td>
            <td>
              <label v-show="row.balance > 0 || row.balance != '-'">S/ </label
              >{{ row.balance }}
            </td>
          </tr>
        </data-table>
      </div>
    </div>
  </div>
</template>

<script>
import DataTable from "../../components/DataTableMovement.vue";

export default {
  components: { DataTable },
  data() {
    return {
      resource: "finances/movements",
      form: {},
    };
  },
};
</script>

<template>
  <div>
    <div class="page-header pr-0">
      <h2>
        <a href="/dashboard">
          <i class="fas fa-tachometer-alt"></i>
        </a>
      </h2>
      <ol class="breadcrumbs">
        <li class="active">
          <span>{{ title }}</span>
        </li>
      </ol>
      <div class="right-wrapper pull-right">
        <a href="#" @click.prevent="clickCreate()" class="btn btn-custom btn-sm mt-2 mr-2">
          <i class="fa fa-plus-circle"></i> Nuevo
        </a>
      </div>
    </div>
    <div class="card mb-0">
      <div class="card-header bg-info">
        <h3 class="my-0">Listado de {{ title }}</h3>
      </div>
      <div class="card-body">
        <data-table :resource="resource">
          <tr slot="heading">
            <th>#</th>
            <th>Fecha</th>
            <th>Almacen Inicial</th>

            <th>Almacen Destino</th>
            <th>Detalle</th>
            <th>Detalle Productos</th>
            <th>Cantidad Total Productos</th>
            <!--<th class="text-right">Acciones</th> -->
          </tr>
          <tr></tr>
          <tr slot-scope="{ index, row }">
            <td>{{ index }}</td>
            <td>{{ row.created_at }}</td>

            <td>{{ row.warehouse }}</td>
            <td>{{ row.warehouse_destination }}</td>
            <td>{{ row.description}}</td>
            <td>
              <el-popover placement="right" width="400" trigger="click">
                <el-table :data="row.inventory">
                  <el-table-column width="260" property="description" label="Producto"></el-table-column>

                  <el-table-column width="100" property="quantity" label="Cantidad"></el-table-column>

                  <el-table-column fixed="right" label="Series" width="120">
                    <template slot-scope="scope">
                      <el-popover placement="right" width="150" trigger="click">
                        <el-table :data="scope.row.lots" width="80">
                          <el-table-column prop="series" label="Series" width="180"></el-table-column>
                        </el-table>
                        <el-button slot="reference" icon="el-icon-zoom-in"></el-button>
                      </el-popover>
                    </template>
                  </el-table-column>
                </el-table>
                <el-button slot="reference" icon="el-icon-zoom-in"></el-button>
              </el-popover>
            </td>
            <td>{{ row.quantity }}</td>
            <!--<td class="text-right">
                             <button type="button" class="btn waves-effect waves-light btn-xs btn-info"
                                    @click.prevent="clickCreate(row.id)">Editar</button>
                            <button type="button" class="btn waves-effect waves-light btn-xs btn-danger"
                                    @click.prevent="clickDelete(row.id)">Eliminar</button>
            </td>-->
          </tr>
        </data-table>
      </div>

      <inventories-form :showDialog.sync="showDialog" :recordId="recordId"></inventories-form>
    </div>
  </div>
</template>

<script>
import DataTable from "../../../../../../resources/js/components/DataTableTransfers.vue";
import { deletable } from "../../../../../../resources/js/mixins/deletable";
import InventoriesForm from "./form.vue";

export default {
  components: { DataTable, InventoriesForm },
  mixins: [deletable],
  data() {
    return {
      title: null,
      showDialog: false,
      resource: "transfers",
      recordId: null,
      typeTransaction: null
    };
  },
  created() {
    this.title = "Traslados";
  },
  methods: {
    clickCreate(recordId = null) {
      location.href = `/${this.resource}/create`;
      //this.recordId = recordId
      //this.showDialog = true
    },
    clickDelete(id) {
      this.destroy(`/${this.resource}/${id}`).then(() =>
        this.$eventHub.$emit("reloadData")
      );
    }
  }
};
</script>

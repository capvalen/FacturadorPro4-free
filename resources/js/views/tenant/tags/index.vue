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
          <span>Tags</span>
        </li>
      </ol>
      <div class="right-wrapper pull-right">
        <template>
          <!-- v-if="typeUser === 'admin'" -->
          <!-- <button type="button" class="btn btn-custom btn-sm  mt-2 mr-2" @click.prevent="clickImport()"><i class="fa fa-upload"></i> Importar</button>-->
          <button
            type="button"
            class="btn btn-custom btn-sm mt-2 mr-2"
            @click.prevent="clickCreate()"
          >
            <i class="fa fa-plus-circle"></i> Nuevo
          </button>
        </template>
      </div>
    </div>
    <div class="card mb-0">
      <div class="card-header bg-info">
        <h3 class="my-0">Listado de Tags Tienda Virtual</h3>
      </div>
      <div class="card-body">
        <data-table :resource="resource">
          <tr slot="heading" width="100%">
            <th>#</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th class="text-right">Acciones</th>
          </tr>
          <tr></tr>
          <tr slot-scope="{ index, row }">
            <td>{{ index }}</td>
            <td>{{ row.name }}</td>
            <td>{{ row.description }}</td>
            <td class="text-right">
              <template>
                <!-- v-if="typeUser === 'admin'" -->
                <button
                  type="button"
                  class="btn waves-effect waves-light btn-xs btn-info"
                  @click.prevent="clickCreate(row.id)"
                >Editar</button>
                <button
                  type="button"
                  class="btn waves-effect waves-light btn-xs btn-danger"
                  @click.prevent="clickDelete(row.id)"
                >Eliminar</button>
              </template>
            </td>
          </tr>
        </data-table>
      </div>

      <tags-form :showDialog.sync="showDialog" :recordId="recordId"></tags-form>

    </div>
  </div>
</template>
<script>
import TagsForm from "./form.vue";
// import ItemsImport from './import.vue'
import DataTable from "../../../components/DataTable.vue";
import { deletable } from "../../../mixins/deletable";

export default {
  props: [], //'typeUser'
  mixins: [deletable],
  components: { TagsForm, DataTable  }, //ItemsImport
  data() {
    return {
      showDialog: false,
      showImportDialog: false,
  
      showImageDetail: false,
      resource: "tags",
      recordId: null,
    };
  },
  created() {},
  methods: {

    clickCreate(recordId = null) {
      this.recordId = recordId;
      this.showDialog = true;
    },
    clickImport() {
      this.showImportDialog = true;
    },
    clickDelete(id) {
      this.destroy(`/${this.resource}/${id}`).then(() =>
        this.$eventHub.$emit("reloadData")
      );
    }
  }
};
</script>

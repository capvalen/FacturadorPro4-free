<template>
  <el-dialog
    :title="title"
    :visible="visible"
    @close="onClose"
    @open="onCreate"
    width="400px"
  >
    <form autocomplete="off" @submit.prevent="onSubmit">
      <div class="form-body">
        <div class="form-group">
          <label for="name">Nombre</label>
          <input
            type="text"
            id="name"
            class="form-control"
            v-model="form.name"
            :class="{ 'is-invalid': errors.name }"
          />
          <div v-if="errors.name" class="invalid-feedback">
            {{ errors.name[0] }}
          </div>
        </div>
        <div class="form-group">
          <label for="description">Descripción</label>
          <input
            type="text"
            id="description"
            class="form-control"
            v-model="form.description"
            :class="{ 'is-invalid': errors.description }"
          />
          <div v-if="errors.description" class="invalid-feedback">
            {{ errors.description[0] }}
          </div>
        </div>
        <div class="form-group">
          <label>Mostrar tipo de documento</label>
          <el-switch v-model="form.active"></el-switch>
        </div>
        <div class="row text-center">
          <div class="col-6">
            <el-button
              native-type="submit"
              :disabled="loading"
              type="primary"
              class="btn-block"
              :loading="loading"
              >Guardar</el-button
            >
          </div>
          <div class="col-6">
            <el-button class="btn-block" @click="onClose">Cancelar</el-button>
          </div>
        </div>
      </div>
    </form>
  </el-dialog>
</template>

<script>
export default {
  props: {
    visible: {
      type: Boolean,
      required: true,
      default: false,
    },
    document: {
      type: Object,
      required: false,
      default: {},
    },
  },
  data() {
    return {
      form: {},
      title: "",
      errors: {},
      loading: false,
      basePath: "/documentary-procedure/documents",
    };
  },
  methods: {
    onUpdate() {
      this.loading = true;
      this.$http
        .put(`${this.basePath}/${this.document.id}/update`, this.form)
        .then((response) => {
          this.$message({
            message: response.data.message,
            type: "success",
          });
          this.$emit("onUpdateItem", response.data.data);
          this.onClose();
        })
        .finally(() => {
          this.loading = false;
          this.errors = {};
        })
        .catch((error) => {
          this.axiosError(error);
        });
    },
    onStore() {
      this.loading = true;
      this.$http
        .post(`${this.basePath}/store`, this.form)
        .then((response) => {
          this.$message({
            message: response.data.message,
            type: "success",
          });
          this.$emit("onAddItem", response.data.data);
          this.onClose();
        })
        .finally(() => {
          this.loading = false;
          this.errors = {};
        })
        .catch((error) => {
          this.axiosError(error);
        });
    },
    onSubmit() {
      if (this.document) {
        this.onUpdate();
      } else {
        this.onStore();
      }
    },
    onClose() {
      this.$emit("update:visible", false);
    },
    onCreate() {
      if (this.document) {
        this.form = this.document;
        this.title = "Editar tipo de documento";
      } else {
        this.title = "Crear tipo de documento";
        this.form = {
          active: true,
        };
      }
    },
  },
};
</script>

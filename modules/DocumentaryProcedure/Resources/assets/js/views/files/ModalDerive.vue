<template>
  <el-dialog
    :title="title"
    :visible="visible"
    @close="onClose"
    @open="onOpened"
    width="500px"
    :close-on-click-modal="false"
  >
    <form autocomplete="off" @submit.prevent="onSubmit">
      <div class="form-body">
        <div class="row">
          <div class="form-group col-6">
            <label>Oficina</label>
            <el-select v-model="form.documentary_office_id">
              <el-option
                v-for="item in offices"
                :key="item.id"
                :value="item.id"
                :label="item.name"
              ></el-option>
            </el-select>
          </div>
          <div class="form-group col-6">
            <label>Acción</label>
            <el-select v-model="form.documentary_action_id">
              <el-option
                v-for="item in actions"
                :key="item.id"
                :value="item.id"
                :label="item.name"
              ></el-option>
            </el-select>
          </div>
          <div class="form-group col-12">
            <label>Observación</label>
            <el-input type="textarea" v-model="form.observation"></el-input>
          </div>
          <div class="col-12 text-center">
            <el-button type="primary" native-type="submit">Guardar</el-button>
            <el-button @click="onClose">Cerrar</el-button>
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
    },
    file: {
      type: Object,
      required: false,
    },
  },
  data() {
    return {
      form: {},
      loading: false,
      offices: [],
      actions: [],
      title: "",
    };
  },
  mounted() {
    this.loading = true;
    this.$http
      .get("/documentary-procedure/files/tables")
      .then((response) => {
        const data = response.data.data;
        this.offices = data.offices;
        this.actions = data.actions;
      })
      .catch((error) => this.axiosError(error))
      .finally(() => (this.loading = false));
  },
  methods: {
    onSubmit() {
      this.loading = true;
      this.$http
        .post(
          `/documentary-procedure/files/${this.file.id}/add-office`,
          this.form
        )
        .then((response) => {
          this.$message({
            message: response.data.message,
            type: "success",
          });
          this.$emit("onAddOffice", response.data.data);
          this.onClose();
        });
    },
    onOpened() {
      this.title = `Derivar expediente: ${this.file.subject}`;
    },
    onClose() {
      this.$emit("update:visible", false);
    },
  },
};
</script>

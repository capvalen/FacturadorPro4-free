<template>
  <div>
    <div class="page-header pr-0">
      <h2>
        <a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a>
      </h2>
      <ol class="breadcrumbs">
        <li class="active"><span>Configuración del login</span></li>
      </ol>
    </div>
    <template v-if="user.type === 'admin'">
      <div class="card mb-0">
        <div class="card-header bg-info">
          <h3 class="my-0">Configuración de la página de inicio de sesión</h3>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-md-4 form-group">
              <ImageBgUpload :config="configuration.login"></ImageBgUpload>
            </div>
            <div class="col-12 col-md-4">
              <div class="form-group">
                <label>Posición del formulario</label>
                <el-select v-model="form.position_form">
                  <el-option
                    key="left"
                    value="left"
                    label="Izquierda"
                  ></el-option>
                  <el-option
                    key="right"
                    value="right"
                    label="Derecha"
                  ></el-option>
                </el-select>
                <small class="form-control-feedback" v-if="errors.position_form" v-text="errors.position_form[0]"></small>
              </div>
              <div class="form-group">
                <label>Mostrar logo en el formulario</label>
                <el-switch v-model="form.show_logo_in_form"></el-switch>
              </div>
              <div class="form-group">
                <label>Posición del logo de la empresa</label>
                <el-select v-model="form.position_logo">
                  <el-option
                    key="top-left"
                    value="top-left"
                    label="Superior izquierda"
                  ></el-option>
                  <el-option
                    key="bottom-left"
                    value="bottom-left"
                    label="Inferior izquierda"
                  ></el-option>
                  <el-option
                    key="top-right"
                    value="top-right"
                    label="Superior derecha"
                  ></el-option>
                  <el-option
                    key="bottom-right"
                    value="bottom-right"
                    label="Inferior derecha"
                  ></el-option>
                </el-select>
                <small class="form-control-feedback" v-if="errors.position_logo" v-text="errors.position_logo[0]"></small>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="form-group">
                <label>Mostrar botones de redes sociales</label>
                <el-switch v-model="form.show_socials"></el-switch>
              </div>
              <div class="form-group">
                <label>Facebook</label>
                <el-input v-model="form.facebook"></el-input>
              </div>
              <div class="form-group">
                <label>Twitter</label>
                <el-input v-model="form.twitter"></el-input>
              </div>
              <div class="form-group">
                <label>Instagram</label>
                <el-input v-model="form.instagram"></el-input>
              </div>
              <div class="form-group">
                <label>Linkedin</label>
                <el-input v-model="form.linkedin"></el-input>
              </div>
              <el-button
                :loading="loading"
                :disabled="loading"
                class="btn-block mt-3"
                @click="onSubmit"
                type="primary"
                >GUARDAR</el-button
              >
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script>
import ImageBgUpload from "./UploadBgImage";

export default {
  components: {
    ImageBgUpload,
  },
  props: {
    user: {
      type: Object,
      required: true,
    },
    configuration: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      form: {},
      loading: false,
      errors: {}
    };
  },
  mounted() {
      this.form = this.configuration.login;
  },
  methods: {
    onSubmit() {
      delete this.form.type;
      delete this.form.image;
      this.loading = true;
      this.$http
        .post("login-page/update", this.form)
        .then((response) => {
          this.$message({
            message: response.data.message,
            type: "success",
          });
        })
        .catch((error) => this.axiosError(error))
        .finally(() => (this.loading = false));
    },
  },
};
</script>

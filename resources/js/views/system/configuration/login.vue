
 <template>
  <div class="card">
    <div class="card-header bg-info">
      <h3 class="my-0">Configuración global para el login de los clientes</h3>
    </div>
    <div class="card-body">
      <div class="card mb-0">
        <div class="row">
          <div class="col-12 form-group mb-4">
            <label>Usar configuración global</label>
            <el-switch v-model="form.use_login_global"></el-switch>
          </div>
          <template v-if="form.use_login_global">
            <div class="col-12 col-md-6 form-group">
              <ImageBgUpload type="bg" :config="configuration.login"></ImageBgUpload>
              <br>
              <ImageBgUpload type="logo" :config="configuration.login"></ImageBgUpload>
            </div>
            <div class="col-12 col-md-6">
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
                <small
                  class="form-control-feedback"
                  v-if="errors.position_form"
                  v-text="errors.position_form[0]"
                ></small>
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
                  <el-option
                    key="none"
                    value="none"
                    label="Ocultar"
                  ></el-option>
                </el-select>
                <small
                  class="form-control-feedback"
                  v-if="errors.position_logo"
                  v-text="errors.position_logo[0]"
                ></small>
              </div>
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
            </div>
          </template>
          <div class="text-right mt-3 col-12">
            <el-button
              :loading="loading"
              :disabled="loading"
              @click="onSubmit"
              type="primary"
              >GUARDAR</el-button
            >
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import ImageBgUpload from "./UploadBgImage";

export default {
  components: {
    ImageBgUpload,
  },
  props: ["configuration"],
  data() {
    return {
      loading: false,
      form: {
        position_form: "",
        show_logo_in_form: "",
        position_logo: "",
        show_socials: "",
        use_login_global: false,
        facebook: '',
        twitter: '',
        instagram: '',
        linkedin: '',
      },
      errors: {},
    };
  },
  mounted() {
    this.form.position_form = this.configuration.login.position_form;
    this.form.show_logo_in_form = this.configuration.login.show_logo_in_form;
    this.form.position_logo = this.configuration.login.position_logo;
    this.form.show_socials = this.configuration.login.show_socials;
    this.form.use_login_global = this.configuration.use_login_global;
    this.form.facebook = this.configuration.login.facebook;
    this.form.twitter = this.configuration.login.twitter;
    this.form.instagram = this.configuration.login.instagram;
    this.form.linkedin = this.configuration.login.linkedin;
  },
  methods: {
    initForm() {
      this.errors = {};
      this.form = {};
    },
    onSubmit() {
      delete this.form.type;
      delete this.form.image;
      this.loading = true;
      this.$http
        .post("configurations/login", this.form)
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



 <template>
  <div class="col-lg-6 col-md-12">
    <div class="card">
      <div class="card-header bg-info">
        <h3 class="my-0">Logo</h3>
      </div>
      <div class="card-body">
        <form autocomplete="off" @submit.prevent="submit">
          <div class="form-body">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group" :class="{'has-danger': errors.token_public_culqui}">
                  <label class="control-label">Logo</label>
                  <el-input v-model="form.logo_store" :readonly="true">
                    <el-upload
                      slot="append"
                      :headers="headers"
                      :data="{'type': 'logo_store'}"
                      action="/ecommerce/uploads"
                      :show-file-list="false"
                      :on-success="successUpload"
                    >
                      <el-button type="primary" icon="el-icon-upload"></el-button>
                    </el-upload>
                  </el-input>
                  <div class="sub-title text-danger">
                    <small>Se recomienda resoluciones 700x300</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="form-actions text-right pt-2">
            <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>




<script>
export default {
  data() {
    return {
      headers: headers_token,
      loading_submit: false,
      resource: "ecommerce",
      errors: {},
      form: {}
    };
  },
  async created() {
    await this.initForm();

    await this.$http.get(`/${this.resource}/record`).then(response => {
      if (response.data !== "") {
        let data = response.data.data;
        this.form.id = data.id;
        this.form.logo_store = data.logo;
      }
    });
  },
  methods: {
    successUpload(response, file, fileList) {
      if (response.success) {
        this.$message.success(response.message);
        this.form[response.type] = response.name;
      } else {
        this.$message({ message: "Error al subir el archivo", type: "error" });
      }
    },
    initForm() {
      this.errors = {};
      this.form = {
        id: null,
        logo_store: null
      };
    },
    submit() {
      this.loading_submit = true;
      this.$http
        .post(`/${this.resource}/configuration_culqui`, this.form)
        .then(response => {
          if (response.data.success) {
            this.$message.success(response.data.message);
          } else {
            this.$message.error(response.data.message);
          }
        })
        .catch(error => {
          if (error.response.status === 422) {
            this.errors = error.response.data;
          } else {
            console.log(error);
          }
        })
        .then(() => {
          this.loading_submit = false;
        });
    },
    submit_paypal() {}
  }
};
</script>


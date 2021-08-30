
 <template>
  <div class="col-lg-6 col-md-12">
    <div class="card">
      <div class="card-header bg-info">
        <h3 class="my-0">Culqi</h3>
      </div>
      <div class="card-body">
        <form autocomplete="off" @submit.prevent="submit">
          <div class="form-body">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group" :class="{'has-danger': errors.token_public_culqui}">
                  <label class="control-label">
                    Token Público
                    <el-tooltip placement="right-start">
                      <div slot="content">
                        Token Público.
                        <a href="#" @click="openCulqi">Culqi</a>
                      </div>
                      <i class="fa fa-info-circle"></i>
                    </el-tooltip>
                  </label>
                  <el-input v-model="form.token_public_culqui"></el-input>
                  <small
                    class="form-control-feedback"
                    v-if="errors.token_public_culqui"
                    v-text="errors.token_public_culqui[0]"
                  ></small>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group" :class="{'has-danger': errors.token_private_culqui}">
                  <label class="control-label">Token Privado  <el-tooltip placement="right-start">
                      <div slot="content">
                        Token Privado.
                        <a href="#" @click="openCulqi">Culqi</a>
                      </div>
                      <i class="fa fa-info-circle"></i>
                    </el-tooltip></label>
                  <el-input v-model="form.token_private_culqui"></el-input>
                  <small
                    class="form-control-feedback"
                    v-if="errors.token_private_culqui"
                    v-text="errors.token_private_culqui[0]"
                  ></small>
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
      loading_submit: false,
      // headers: headers_token,
      resource: "ecommerce",
      errors: {},
      form: {},
      soap_sends: [],
      soap_types: []
    };
  },
  async created() {
    await this.initForm();

    await this.$http.get(`/${this.resource}/record`).then(response => {
      if (response.data !== "") {
        let data = response.data.data;
        this.form.id = data.id;
        this.form.token_public_culqui = data.token_public_culqui;
        this.form.token_private_culqui = data.token_private_culqui;
      }
    });
  },
  methods: {
    openCulqi() {
      window.open("https://www.culqi.com");
    },
    initForm() {
      this.errors = {};
      this.form = {
        id: null,
        token_public_culqui: "",
        token_private_culqui: ""
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


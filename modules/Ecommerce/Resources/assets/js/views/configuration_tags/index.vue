
 <template>
  <div class="col-lg-6 col-md-12">
    <div class="card">
      <div class="card-header bg-info">
        <h3 class="my-0">Tags menú</h3>
      </div>
      <div class="card-body">
        <form autocomplete="off" @submit.prevent="submit">
          <div class="form-body">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group" :class="{'has-danger': errors.tag_shipping}">
                  <label class="control-label">Tag Envío</label>
                  <el-input v-model="form.tag_shipping"></el-input>
                  <small
                    class="form-control-feedback"
                    v-if="errors.tag_shipping"
                    v-text="errors.tag_shipping[0]"
                  ></small>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group" :class="{'has-danger': errors.tag_dollar}">
                  <label class="control-label">Tag moneda</label>
                  <el-input v-model="form.tag_dollar"></el-input>
                  <small
                    class="form-control-feedback"
                    v-if="errors.tag_dollar"
                    v-text="errors.tag_dollar[0]"
                  ></small>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group" :class="{'has-danger': errors.tag_support}">
                  <label class="control-label">Tag soporte</label>
                  <el-input v-model="form.tag_support"></el-input>
                  <small
                    class="form-control-feedback"
                    v-if="errors.tag_support"
                    v-text="errors.tag_support[0]"
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
        this.form.tag_dollar = data.tag_dollar;
        this.form.tag_shipping = data.tag_shipping;
        this.form.tag_support = data.tag_support;
      }
    });
  },
  methods: {
    initForm() {
      this.errors = {};
      this.form = {
        id: null,
        tag_support: "",
        tag_dollar: "",
        tag_shipping: ""
      };
    },
    submit() {
      this.loading_submit = true;
      this.$http
        .post(`/${this.resource}/configuration_tags`, this.form)
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
    }
  }
};
</script>


<template>
  <div class="col-lg-6 col-md-12 0">
    <div class="card">
      <div class="card-header bg-info">
        <h3 class="my-0">Paypal</h3>
      </div>
      <div class="card-body">
        <form autocomplete="off" @submit.prevent="submit">
          <div class="form-body">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group" :class="{'has-danger': errors.script_paypal}">
                  <label class="control-label">
                    Script Paypal
                    <el-tooltip placement="right-start">
                      <div slot="content">
                        Codigo Html Formulario Paypal.
                        <a href="#" @click="openPaypal">Paypal</a>
                      </div>
                      <i class="fa fa-info-circle"></i>
                    </el-tooltip>
                  </label>
                  <br />
                  <el-input type="textarea" :rows="4" v-model="form.script_paypal"></el-input>
                  <small
                    class="form-control-feedback"
                    v-if="errors.script_paypal"
                    v-text="errors.script_paypal[0]"
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
        this.form.script_paypal = data.script_paypal;
      }
    });
  },
  methods: {
    openPaypal() {
      window.open(
        "https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/buy-now-step-1/#open-the-paypal-button-creation-page"
      );
    },
    initForm() {
      this.errors = {};
      this.form = {
        id: null,
        script_paypal: ""
      };
    },
    submit() {
      this.loading_submit = true;
      this.$http
        .post(`/${this.resource}/configuration_paypal`, this.form)
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




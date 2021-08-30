<template>
  <div class="col-lg-6 col-md-12 pt-2 pt-md-0">
    <div class="card">
      <div class="card-header bg-info">
        <h3 class="my-0">Informacion de Contacto</h3>
      </div>
      <div class="card-body">
        <form autocomplete="off" @submit.prevent="submit">
          <div class="form-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group" :class="{'has-danger': errors.information_contact_email}">
                  <label class="control-label">Email</label>
                  <el-input v-model="form.information_contact_email"></el-input>
                  <small
                    class="form-control-feedback"
                    v-if="errors.information_contact_email"
                    v-text="errors.information_contact_email[0]"
                  ></small>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group" :class="{'has-danger': errors.information_contact_name}">
                  <label class="control-label">
                    Nombre
                    <span class="text-danger">*</span>
                  </label>
                  <el-input v-model="form.information_contact_name"></el-input>
                  <small
                    class="form-control-feedback"
                    v-if="errors.information_contact_name"
                    v-text="errors.information_contact_name[0]"
                  ></small>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group" :class="{'has-danger': errors.information_contact_phone}">
                  <label class="control-label">
                    Telefono
                    <span class="text-danger">*</span>
                  </label>
                  <el-input v-model="form.information_contact_phone"></el-input>
                  <small
                    class="form-control-feedback"
                    v-if="errors.information_contact_phone"
                    v-text="errors.information_contact_phone[0]"
                  ></small>
                </div>
              </div>
               <div class="col-md-6">
                <div class="form-group" :class="{'has-danger': errors.information_contact_address}">
                  <label class="control-label">
                    Dirección
                    <span class="text-danger">*</span>
                  </label>
                  <el-input v-model="form.information_contact_address"></el-input>
                  <small
                    class="form-control-feedback"
                    v-if="errors.information_contact_address"
                    v-text="errors.information_contact_address[0]"
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
        this.form = response.data.data;
      }
    });
  },
  methods: {
    initForm() {
      this.errors = {};
      this.form = {
        id: null,
        information_contact_email: "",
        information_contact_name: null,
        information_contact_phone: null
      };
    },
    submit() {
      this.loading_submit = true;
      this.$http
        .post(`/${this.resource}/configuration`, this.form)
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

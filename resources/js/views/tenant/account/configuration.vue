<template>
  <div class="card col-md-5">
    <div class="card-header bg-info">
      <h3 class="my-0">Tipo de Plan de la Empresa</h3>
    </div>
    <div class="card-body">
      <form autocomplete="off" > <!-- @submit.prevent="submit" -->
        <div class="form-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Plan</label>
                <el-select  @change="alertPlan" v-model="form.plan_id">
                  <el-option
                    v-for="(option, index) in plans"
                    :key="index"
                    :value="option.id"
                    :label="option.name"
                  ></el-option>
                </el-select>
              </div>
            </div>
          </div>
        </div>
        <div class="form-actions text-right pt-2">
          <!-- <el-button type="primary" native-type="submit" :loading="loading_submit">Actualizar</el-button> -->
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      loading_submit: false,
      resource: "cuenta",
      form: {},
      configuration: {},
      plans:[]
    
    };
  },
  async created() {
    await this.initForm();
    await this.$http.get(`/${this.resource}/tables`).then(response => {
      this.plans = response.data.plans;
      this.configuration = response.data.configuration
      if(this.configuration.plan)
      {
          this.form.plan_id = this.configuration.plan.id
      }

    });
   
  },
  methods: {
    alertPlan()
    {

       this.$confirm('Para cambiar su Plan, Comuniquese con el Administrador', 'Warning', {
          confirmButtonText: 'OK',
          cancelButtonText: 'Cancel',
          type: 'warning',
          showCancelButton: false
        }).then(() => {
         
        }).catch(() => {
                   
        });

    },
    initForm() {
      this.errors = {};
      this.form = {
        plan_id: null,
      };
    },
    /*submit() {
      this.loading_submit = true;
      this.$http
        .post(`/${this.resource}/update_plan`, this.form)
        .then(response => {
          if (response.data.success) {
            this.$message.success(response.data.message);
          } else {
            this.$message.error(response.data.message);
          }
        })
        .catch(error => {
          if (error.response.status === 422) {
            this.errors = error.response.data.errors;
          } else {
            console.log(error);
          }
        })
        .then(() => {
          this.loading_submit = false;
        });
    }*/
   
  }
};
</script>

<template>
  <div>
    <el-dialog
      :title="titleDialog"
      :visible="showDialog"
      @open="create"
      width="30%"
      :close-on-click-modal="false"
      :close-on-press-escape="false"
      :show-close="false"
    >
      <div class="row" >
        <div class="col-lg-12 col-md-12 col-sm-12 text-center font-weight-bold">
          <p>Imprimir A4</p>
          <button
            type="button"
            class="btn btn-lg btn-info waves-effect waves-light"
            @click="clickToPrint('a4')"
          >
            <i class="fa fa-file-alt"></i>
          </button>
        </div>  
      </div>
      <br />
      <div class="row" >
        <div class="col-md-12">
          <el-input v-model="customer_email">
            <el-button
              slot="append"
              icon="el-icon-message"
              @click="clickSendEmail"
              :loading="loading"
            >Enviar</el-button>
          </el-input>
          <!--<small class="form-control-feedback" v-if="errors.customer_email" v-text="errors.customer_email[0]"></small> -->
        </div>
      </div>
      <br /> 

      <span slot="footer" class="dialog-footer">
        <template v-if="showClose">
          <el-button @click="clickClose">Cerrar</el-button>
          <el-button
            class="submit"
            type="primary"
            @click="submit"
            :loading="loading_submit"
            v-if="generate"
          >Generar</el-button>
        </template>
        <template v-else>
          <el-button
            class="submit"
            type="primary"
            plain
            @click="submit"
            :loading="loading_submit"
            v-if="generate"
          >Generar comprobante</el-button>
          <el-button @click="clickFinalize" v-else>Ir al listado</el-button>
          <el-button type="primary" @click="clickNewOrderNote">{{this.type !== "edit" ? 'Nueva oportunidad venta':'Continuar'}}</el-button>
        </template>
      </span>
    </el-dialog>
 
  </div>
</template>

<script> 

export default {

  props: ["showDialog", "recordId", "showClose", "showGenerate", "type", 'typeUser'],
  data() {
    return {
      customer_email: "",
      titleDialog: null,
      loading: false,
      resource: "sale-opportunities",
      errors: {},
      form: {},
      document: {},
      document_types: [],
      all_document_types: [],
      all_series: [],
      series: [],
      customers: [],
      generate: false,
      loading_submit: false,
      documentNewId: null,
      is_document_type_invoice: true,
      loading_search: false,
      payment_destinations:  [],
      payment_method_types: []
    };
  },
  created() {
    this.initForm();
  },
  methods: {
    initForm() {
      this.errors = {};
      this.form = {
        id: null,
        number_full: null,
        external_id: null,
        date_of_issue: null,
        sale_opportunity: null
      };
    },
    async create() { 
      await this.$http
        .get(`/${this.resource}/record/${this.recordId}`)
        .then(response => {
          this.form = response.data.data;
          // this.validateIdentityDocumentType()
          let type = this.type == "edit" ? "actualizada" : "registrada";
          this.titleDialog = `O. Venta ${type}: ` + this.form.number_full;
        });
    },
    clickFinalize() {
      location.href = `/${this.resource}`;
    },
    clickNewOrderNote() {
      this.clickClose();
    },
    clickClose() {
      this.$emit("update:showDialog", false);
      this.initForm();
    },
    clickToPrint(format) {
      window.open(
        `/${this.resource}/print/${this.form.external_id}/${format}`,
        "_blank"
      );
    },
    clickSendEmail() {
      this.loading = true;
      this.$http
        .post(`/${this.resource}/email`, {
          customer_email: this.customer_email,
          id: this.form.id,
          customer_id: this.form.sale_opportunity.customer_id
        })
        .then(response => {
          if (response.data.success) {
            this.$message.success("El correo fue enviado satisfactoriamente");
          } else {
            this.$message.error("Error al enviar el correo");
          }
        })
        .catch(error => {
          this.$message.error("Error al enviar el correo");
        })
        .then(() => {
          this.loading = false;
        });
    }
  }
};
</script>

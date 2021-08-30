<template>
  <div>
    <el-dialog
      :title="title"
      :visible="visible"
      @close="onClose"
      @open="onCreate"
      width="600px"
    >
      <form autocomplete="off" @submit.prevent="onSubmit">
        <div class="form-body">
          <div class="form-group row">
            <div class="col-6">
              <label>Código <span class="text-danger">*</span></label>
              <el-input v-model="nextId" type="text" readonly></el-input>
            </div>
            <div
              class="form-group col-6"
              :class="{ 'has-danger': errors.documentary_document_id }"
            >
              <label for="document_type">Tipo de documento <span class="text-danger">*</span></label>
              <el-select
                v-model="form.documentary_document_id"
                @change="onGetDocumentNumber"
              >
                <el-option
                  v-for="item in documentTypes"
                  :key="item.id"
                  :label="item.name"
                  :value="item.id"
                ></el-option>
              </el-select>
              <small
                class="form-control-feedback"
                v-if="errors.documentary_document_id"
                v-text="errors.documentary_document_id[0]"
              ></small>
            </div>
            <div
              class="form-group col-6"
              :class="{ 'has-danger': errors.number }"
            >
              <label>Número de documento <span class="text-danger">*</span></label>
              <el-input v-model="form.number">
                <template slot="append">-{{ currentYear }}</template>
              </el-input>
              <small
                class="form-control-feedback"
                v-if="errors.number"
                v-text="errors.number[0]"
              ></small>
            </div>
            <div
              class="form-group col-6"
              :class="{ 'has-danger': errors.number }"
            >
              <label for="invoice">Folio <span class="text-danger">*</span></label>
              <el-input v-model="form.invoice"></el-input>
              <small
                class="form-control-feedback"
                v-if="errors.number"
                v-text="errors.number[0]"
              ></small>
            </div>
            <div
              class="form-group col-6"
              :class="{ 'has-danger': errors.date_register }"
            >
              <label for="invoice">Fecha de registro <span class="text-danger">*</span></label>
              <el-date-picker
                v-model="form.date_register"
                type="date"
                placeholder="Selecciona una fecha"
              >
              </el-date-picker>
              <small
                class="form-control-feedback"
                v-if="errors.date_register"
                v-text="errors.date_register[0]"
              ></small>
            </div>
            <div
              class="form-group col-6"
              :class="{ 'has-danger': errors.time_register }"
            >
              <label for="invoice">Hora de registro <span class="text-danger">*</span></label>
              <el-input v-model="form.time_register" placeholder="13:30:00">
              </el-input>
              <small
                class="form-control-feedback"
                v-if="errors.time_register"
                v-text="errors.time_register[0]"
              ></small>
            </div>
          </div>
          <div class="form-group" :class="{ 'has-danger': errors.person_id }">
            <label class="control-label font-weight-bold text-info">
              Remitente <span class="text-danger">*</span>
              <a href="#" @click.prevent="showDialogNewPerson = true"
                >[+ Nuevo]</a
              >
            </label>
            <el-select
              v-model="form.person_id"
              filterable
              remote
              class="border-left rounded-left border-info"
              popper-class="el-select-customers"
              placeholder="Escriba el nombre o número de documento del cliente"
              :remote-method="searchRemoteCustomers"
              :loading="loading"
              @change="changeCustomer"
            >
              <el-option
                v-for="option in customers"
                :key="option.id"
                :value="option.id"
                :label="option.description"
              ></el-option>
            </el-select>
            <small
              class="form-control-feedback"
              v-if="errors.person_id"
              v-text="errors.person_id[0]"
            ></small>
          </div>
          <div class="form-group" :class="{ 'has-danger': errors.subject }">
            <label>Asunto <span class="text-danger">*</span></label>
            <el-input type="text" v-model="form.subject"></el-input>
            <div v-if="errors.subject" class="invalid-feedback">
              {{ errors.subject[0] }}
            </div>
          </div>
          <div class="form-group" :class="{ 'has-danger': errors.subject }">
            <label for="file">Archivo adjunto <span class="text-danger">*</span></label>
            <el-button @click="onSearchFile">Buscar archivo</el-button>
            <input
              type="file"
              ref="inputFile"
              class="hidden"
              @change="onSelectFile"
            />
            <span v-if="filename">{{ filename }}</span>
            <div v-if="errors.subject" class="invalid-feedback">
              {{ errors.subject[0] }}
            </div>
          </div>
          <div
            class="form-group"
            :class="{ 'has-danger': errors.documentary_process_id }"
          >
            <label for="process">Proceso <span class="text-danger">*</span></label>
            <el-select v-model="form.documentary_process_id">
              <el-option
                v-for="item in processes"
                :key="item.id"
                :label="item.name"
                :value="item.id"
              ></el-option>
            </el-select>
            <div v-if="errors.documentary_process_id" class="invalid-feedback">
              {{ errors.documentary_process_id[0] }}
            </div>
          </div>
          <div class="form-group">
              <OfficesRows :offices="offices" :actions="actions" :form.sync="form"></OfficesRows>
          </div>
          <div class="row text-center">
            <div class="col-6">
              <el-button
                native-type="submit"
                :disabled="loading"
                type="primary"
                class="btn-block"
                :loading="loading"
                >Guardar</el-button
              >
            </div>
            <div class="col-6">
              <el-button class="btn-block" @click="onClose">Cancelar</el-button>
            </div>
          </div>
        </div>
      </form>
    </el-dialog>
    <person-form
      :showDialog.sync="showDialogNewPerson"
      type="customers"
      :external="true"
      :input_person="input_person"
      :document_type_id="form.document_type_id"
    ></person-form>
  </div>
</template>

<script>
import moment from "moment";
import PersonForm from "../../../../../../../resources/js/views/tenant/persons/form.vue";
import OfficesRows from './Offices.vue';

export default {
  components: {
    PersonForm,
    OfficesRows
  },
  props: {
    visible: {
      type: Boolean,
      required: true,
      default: false,
    },
    file: {
      type: Object,
      required: false,
      default: {},
    },
  },
  data() {
    return {
      input_person: null,
      form: {},
      title: "",
      errors: {},
      loading: false,
      basePath: "/documentary-procedure/files",
      showDialogNewPerson: false,
      errors: {},
      customers: [],
      documentTypes: [],
      processes: [],
      nextId: "",
      currentYear: 0,
      attachFile: null,
      filename: "",
      offices: [],
      actions: []
    };
  },
  mounted() {
      this.onInitializeForm();
  },
  methods: {
    onSearchFile() {
      this.$refs.inputFile.click();
    },
    onSelectFile(event) {
      const files = event.target.files;
      if (files.length > 0) {
        this.attachFile = files[0];
        this.filename = this.attachFile.name;
      } else {
        this.attachFile = null;
        this.filename = "";
      }
    },
    onGenerateData() {
      const data = new FormData();
      data.append("documentary_document_id", this.form.documentary_document_id);
      data.append("documentary_process_id", this.form.documentary_process_id);
      data.append("number", this.form.number);
      data.append("year", this.currentYear);
      data.append("invoice", this.form.invoice);
      data.append("date_register", this.form.date_register);
      data.append("time_register", this.form.time_register);
      data.append("person_id", this.form.person_id);
      if (this.form.sender) {
        data.append("person", JSON.stringify(this.form.sender));
      }
      if (this.form.offices) {
        data.append("offices", JSON.stringify(this.form.offices));
      }
      data.append("subject", this.form.subject || "");
      data.append("observation", this.form.observation || "");
      if (this.attachFile) {
        data.append("file", this.attachFile);
      }
      return data;
    },
    changeCustomer() {
      const customer = this.customers
        .filter((c) => this.form.person_id == c.id)
        .reduce((c) => c);
      this.form.sender = {
        name: customer.name,
        address: customer.address,
        number: customer.number,
        identity_document_type_id: customer.identity_document_type_id,
      };
    },
    searchRemoteCustomers(input) {
      if (input.length > 0) {
        this.loading = true;
        let parameters = `input=${input}&document_type_id=&operation_type_id=`;

        this.$http
          .get(`/documents/search/customers?${parameters}`)
          .then((response) => {
            this.customers = response.data.customers;
          })
          .catch((error) => this.axiosError(error))
          .finally(() => (this.loading = false));
      }
    },
    onUpdate(data) {
      this.loading = true;
      this.$http
        .post(`${this.basePath}/${this.file.id}/update`, data)
        .then((response) => {
          this.$message({
            message: response.data.message,
            type: "success",
          });
          this.$emit("onUpdateItem", response.data.data);
          this.onClose();
        })
        .finally(() => {
          this.loading = false;
          this.errors = {};
        })
        .catch((error) => {
          this.axiosError(error);
        });
    },
    onStore(data) {
      this.loading = true;
      this.$http
        .post(`${this.basePath}/store`, data)
        .then((response) => {
          this.$message({
            message: response.data.message,
            type: "success",
          });
          this.$emit("onAddItem", response.data.data);
          this.onClose();
        })
        .finally(() => {
          this.loading = false;
          this.errors = {};
        })
        .catch((error) => {
          this.axiosError(error);
        });
    },
    onSubmit() {
      const data = this.onGenerateData();
      if (this.file) {
        this.onUpdate(data);
      } else {
        this.onStore(data);
      }
    },
    onClose() {
      this.$emit("update:visible", false);
    },
    async onGetDocumentNumber() {
      const params = {
        document_id: this.form.documentary_document_id,
      };
      this.loading = true;
      await this.$http
        .get(`${this.basePath}/document-number`, { params })
        .then((response) => {
          const data = response.data.data;
          this.form.number = data.number;
        })
        .catch((error) => this.axiosError(error))
        .finally(() => (this.loading = false));
    },
    async onGetDataForNewFile() {
      this.loading = true;
      await this.$http
        .get(`${this.basePath}/create`)
        .then((response) => {
          const data = response.data.data;
          this.nextId = data.next_id;
          this.currentYear = data.current_year;
        })
        .catch((error) => this.axiosError(error))
        .finally(() => (this.loading = false));
    },
    onGetFilenameFromPath(path) {
      if (path) {
        const parts = path.split("/");
        if (parts.length === 4) {
          return parts[3];
        }
      }
      return "";
    },
    onInitializeForm() {
      const date = moment();
      this.form = {
        date_register: date.format("YYYY-MM-DD"),
        time_register: date.format("H:mm:ss"),
        offices: []
      };
      this.filename = "";
      this.attachFile = null;
    },
    async onCreate() {
      if (this.file) {
        this.form = this.file;
        this.title = "Editar expediente";
        this.filename = this.onGetFilenameFromPath(this.form.attached_file);
        this.nextId = this.form.id;
        this.number = this.form.number;
        this.currentYear = this.form.year;
        this.attachFile = null;
      } else {
        this.title = "Crear expediente";
        this.onInitializeForm();
        await this.onGetDataForNewFile();
      }
      this.loading = true;
      await this.$http
        .get(`${this.basePath}/tables`)
        .then((response) => {
          const data = response.data.data;
          this.customers = data.customers;
          this.documentTypes = data.document_types;
          this.processes = data.processes;
          this.offices = data.offices;
          this.actions = data.actions;
        })
        .catch((error) => this.axiosError(error))
        .finally(() => (this.loading = false));
    },
  },
};
</script>

<template>
  <el-dialog :title="titleDialog" :visible="showDialog" @close="close" @open="create">
    <form autocomplete="off" @submit.prevent="submit">
      <div class="form-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group" :class="{'has-danger': errors.name}">
              <label class="control-label">Nombre</label>
              <el-input v-model="form.name"></el-input>
              <small class="form-control-feedback" v-if="errors.name" v-text="errors.name[0]"></small>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group" :class="{'has-danger': errors.description}">
              <label class="control-label">Descripcion</label>
              <el-input v-model="form.description"></el-input>
              <small
                class="form-control-feedback"
                v-if="errors.description"
                v-text="errors.description[0]"
              ></small>
            </div>
          </div>
        </div>
        <br />
        <div class="row">
          <div class="col-md-3">
            <div class="form-group" :class="{'has-danger': errors.image}">
              <label class="control-label">
                Imágen
                <span class="text-danger"></span>
                <div class="sub-title text-danger">
                  <small>Se requiere resoluciones 1024x720</small>
                </div>
              </label>
              <el-upload
                class="avatar-uploader"
                :data="{'type': 'promotions'}"
                :headers="headers"
                :action="`/${resource}/upload`"
                :show-file-list="false"
                :on-success="onSuccess"
              >
                <img v-if="form.image_url" :src="form.image_url" class="avatar" />
                <i v-else class="el-icon-plus avatar-uploader-icon"></i>
              </el-upload>
              <small class="form-control-feedback" v-if="errors.image" v-text="errors.image[0]"></small>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group" :class="{'has-danger': errors.item_id}">
              <label class="control-label">Link a Producto</label>
              <el-select v-model="form.item_id" dusk="item_id">
                <el-option
                  v-for="option in items"
                  :key="option.id"
                  :value="option.id"
                  :label="option.name"
                ></el-option>
              </el-select>
              <small
                class="form-control-feedback"
                v-if="errors.item_id"
                v-text="errors.item_id[0]"
              ></small>
            </div>
          </div>
        </div>
      </div>
      <div class="form-actions text-right mt-4">
        <el-button @click.prevent="close()">Cancelar</el-button>
        <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
      </div>
    </form>
  </el-dialog>
</template>

<script>
// import {serviceNumber} from '../../../mixins/functions'

export default {
  // mixins: [serviceNumber],
  props: ["showDialog", "recordId", "external"],
  data() {
    return {
      items: [],
      headers: headers_token,
      loading_submit: false,
      titleDialog: null,
      resource: "promotions",
      errors: {},
      form: {},
      countries: [],
      all_departments: [],
      all_provinces: [],
      all_districts: [],
      provinces: [],
      districts: [],
      identity_document_types: []
    };
  },
  created() {
    this.initForm();
    this.$http.get(`/${this.resource}/tables`).then(response => {
      this.items = response.data.items;
    });
  },
  computed: {},
  methods: {
    initForm() {
      this.errors = {};
      this.form = {
        name: null,
        description: null,
        image: null,
        image_url: null,
        temp_path: null
      };
    },
    create() {
      this.titleDialog = this.recordId ? "Editar Promocion" : "Nueva Promocion";
      if (this.recordId) {
        this.$http
          .get(`/${this.resource}/record/${this.recordId}`)
          .then(response => {
            this.form = response.data.data;
          });
      }
    },

    submit() {
      this.loading_submit = true;
      this.$http
        .post(`/${this.resource}`, this.form)
        .then(response => {
          if (response.data.success) {
            this.$message.success(response.data.message);

            this.$eventHub.$emit("reloadData");

            this.close();
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
    close() {
      this.$emit("update:showDialog", false);
      this.initForm();
    },
    onSuccess(response, file, fileList) {
      if (response.success) {
        this.form.image = response.data.filename;
        this.form.image_url = response.data.temp_image;
        this.form.temp_path = response.data.temp_path;
      } else {
        this.$message.error(response.message);
      }
    }
  }
};
</script>
<template>
  <el-dialog
    :title="titleDialog"
    :visible="showDialog"
    @close="close"
    @open="create"
  >
    <form autocomplete="off" @submit.prevent="submit">
      <div class="form-body">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group" :class="{ 'has-danger': errors.name }">
              <label class="control-label">Nombre</label>
              <el-input v-model="form.name"></el-input>
              <small
                class="form-control-feedback"
                v-if="errors.name"
                v-text="errors.name[0]"
              ></small>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group" :class="{ 'has-danger': errors.email }">
              <label class="control-label">Correo Electrónico</label>
              <el-input
                v-model="form.email"
                :disabled="form.id != null"
              ></el-input>
              <small
                class="form-control-feedback"
                v-if="errors.email"
                v-text="errors.email[0]"
              ></small>
            </div>
          </div>
          <div class="col-md-4">
            <div
              class="form-group"
              :class="{ 'has-danger': errors.establishment_id }"
            >
              <label class="control-label">Establecimiento</label>
              <el-select v-model="form.establishment_id" filterable>
                <el-option
                  v-for="option in establishments"
                  :key="option.id"
                  :value="option.id"
                  :label="option.description"
                ></el-option>
              </el-select>
              <small
                class="form-control-feedback"
                v-if="errors.establishment_id"
                v-text="errors.establishment_id[0]"
              ></small>
            </div>
          </div>
          <div class="col-md-12" v-show="form.id">
            <div class="form-group" :class="{ 'has-danger': errors.api_token }">
              <label class="control-label">Api Token</label>
              <el-input
                v-model="form.api_token"
                :readonly="form.id != null"
              ></el-input>
              <small
                class="form-control-feedback"
                v-if="errors.api_token"
                v-text="errors.api_token[0]"
              ></small>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group" :class="{ 'has-danger': errors.password }">
              <label class="control-label">Contraseña</label>
              <el-input v-model="form.password"></el-input>
              <small
                class="form-control-feedback"
                v-if="errors.password"
                v-text="errors.password[0]"
              ></small>
            </div>
          </div>
          <div class="col-md-4">
            <div
              class="form-group"
              :class="{ 'has-danger': errors.password_confirmation }"
            >
              <label class="control-label">Confirmar Contraseña</label>
              <el-input v-model="form.password_confirmation"></el-input>
              <small
                class="form-control-feedback"
                v-if="errors.password_confirmation"
                v-text="errors.password_confirmation[0]"
              ></small>
            </div>
          </div>
          <div class="col-md-4" v-if="typeUser != 'integrator'">
            <div class="form-group" :class="{ 'has-danger': errors.type }">
              <label class="control-label">Perfil</label>
              <el-select v-model="form.type" :disabled="form.id === 1">
                <el-option
                  v-for="option in types"
                  :key="option.type"
                  :value="option.type"
                  :label="option.description"
                ></el-option>
              </el-select>
              <small
                class="form-control-feedback"
                v-if="errors.type"
                v-text="errors.type[0]"
              ></small>
            </div>
          </div>
          <div class="col-md-8" v-if="typeUser != 'integrator'">
            <div class="form-comtrol">
              <label class="control-label">Permisos Módulos</label>
              <div class="form-group tree-container-admin">
                <el-tree
                  :data="modules"
                  show-checkbox
                  node-key="id"
                  ref="tree"
                  accordion
                  :check-strictly="true"
                  highlight-current
                  :props="defaultProps"
                  @check="FixChildren"
                >
                </el-tree>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="form-actions text-right mt-4">
        <el-button @click.prevent="close()">Cancelar</el-button>
        <el-button type="primary" native-type="submit" :loading="loading_submit"
          >Guardar</el-button
        >
      </div>
    </form>
  </el-dialog>
</template>

<script>
export default {
  props: ["showDialog", "recordId", "typeUser"],
  data() {
    return {
      defaultProps: {
        children: "childrens",
        label: "description",
      },
      loading_submit: false,
      titleDialog: null,
      resource: "users",
      errors: {},
      form: {},
      modules: [],
      datai: [],
      establishments: [],
      types: [],
      // define the default value
      value: null,
      // define options
      alwaysOpen: true,
      options: [],
    };
  },
    updated () {
        // Set default values ​​for multiple selection trees
        if(this.modules !== undefined && this.$refs.tree !== undefined) {
            // this.$refs.tree.setCheckedKeys(this.modules)
        }
    },
    async created() {
    await this.$http.get(`/${this.resource}/tables`).then((response) => {
      this.modules = response.data.modules;
      this.establishments = response.data.establishments;
      this.types = response.data.types;
    });
    await this.initForm();
  },
  methods: {
      FixChildren(currentObj, treeStatus) {
          if (currentObj !== undefined) {
              let selected = treeStatus.checkedKeys.indexOf(currentObj.id) // -1 is unchecked
              if (selected !== -1) {
                  this.SelectParent(currentObj)
                  this.FixSameValueToChild(currentObj, true)
              } else {
                  if (currentObj.childrens !== undefined && currentObj.childrens.length !== 0) {
                      this.FixSameValueToChild(currentObj, false)
                  }
              }
          }
      },
      FixSameValueToChild(treeList, isSelected) {
          if (treeList !== undefined) {
              this.$refs.tree.setChecked(treeList.id, isSelected)
              if( treeList.childrens !== undefined) {
                  for (let i = 0; i < treeList.childrens.length; i++) {
                      this.FixSameValueToChild(treeList.childrens[i], isSelected)
                  }
              }
          }
      },
      SelectParent(currentObj) {
          if(currentObj !== undefined) {
              let currentNode = this.$refs.tree.getNode(currentObj)
              if (currentNode.parent.key !== undefined) {
                  this.$refs.tree.setChecked(currentNode.parent, true)
                  this.SelectParent(currentNode.parent)
              }
          }
      },


      initForm() {
      this.errors = {};
      this.form = {
        id: null,
        name: null,
        email: null,
        api_token: null,
        establishment_id: null,
        password: null,
        password_confirmation: null,
        locked: false,
        type: null,
        modules: [],
        levels: [],
      };
    },
    create() {
      this.titleDialog = this.recordId ? "Editar Usuario" : "Nuevo Usuario";
      if (this.recordId) {
        this.$http
          .get(`/${this.resource}/record/${this.recordId}`)
          .then((response) => {
            this.form = response.data.data;

            this.$refs.tree.setCheckedKeys([]);
            const preSelecteds = [];
            const preSelectedsModules = this.form.modules;
            const preSelectedsLevels = this.form.levels;
            this.modules.map((m) => {
              if (preSelectedsModules.includes(m.id)) {
                preSelecteds.push(m.id);
              }
              m.childrens.map((c) => {
                const idArray = c.id.split("-");
                if (preSelectedsLevels.includes(parseInt(idArray[1]))) {
                  preSelecteds.push(c.id);
                }
              });
            });
            setTimeout(() => {
              this.$refs.tree.setCheckedKeys(preSelecteds);
            }, 1000);
          });
      } else {
        this.$http.get(`/${this.resource}/tables`).then((response) => {
          this.$refs.tree.setCheckedKeys([]);
          this.modules = response.data.modules;
          this.establishments = response.data.establishments;
          this.types = response.data.types;
        });
      }
    },
    submit() {
      const modulesAndLevelsSelecteds = this.$refs.tree.getCheckedNodes();
      const modules = [];
      modulesAndLevelsSelecteds.map((m) => {
        if (m.is_parent) {
          modules.push(m.id);
        }
      });
      const levels = [];
      modulesAndLevelsSelecteds.filter((l) => {
        if (!l.is_parent) {
          const idArray = l.id.split("-");
          levels.push(idArray[1]);
        }
      });
      this.form.modules = modules;
      this.form.levels = levels;

      if (modules.length < 1) {
        return this.$message.error("Debe seleccionar al menos un módulo");
      }
      this.loading_submit = true;
      this.$http
        .post(`/${this.resource}`, this.form)
        .then((response) => {
          if (response.data.success) {
            this.form.password = null;
            this.form.password_confirmation = null;
            this.$message.success(response.data.message);
            this.$eventHub.$emit("reloadData");
            this.close();
          } else {
            this.$message.error(response.data.message);
          }
        })
        .catch((error) => {
          if (error.response.status === 422) {
            this.errors = error.response.data;
          } else {
            this.$message.error(error.response.data.message);
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
  },
};
</script>

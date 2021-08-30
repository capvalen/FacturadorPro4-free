
 <template>
  <div class="col-lg-6 col-md-12">
    <div class="card">
      <div class="card-header bg-info">
        <h3 class="my-0">Producto en Oferta</h3>
      </div>
      <div class="card-body">
        <form autocomplete="off" @submit.prevent="submit">
          <div class="form-body">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group" :class="{'has-danger': errors.token_public_culqui}">
                  <label class="control-label">Producto</label>
                  <el-input v-model="form.producto_store" :readonly="true">
                    <el-upload
                      slot="append"
                      :headers="headers"
                      :data="{'type': 'producto_store'}"
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
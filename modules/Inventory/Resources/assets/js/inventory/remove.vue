<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @close="close" @open="create">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="control-label">Producto</label>
                            <el-input v-model="form.item_description" :readonly="true"></el-input>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Cantidad</label>
                            <el-input v-model="form.quantity"></el-input>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="control-label">Almacén Inicial</label>
                            <el-input v-model="form.warehouse_description" :readonly="true"></el-input>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Cantidad a retirar</label>
                            <el-input v-model="form.quantity_remove"></el-input>
                        </div>
                    </div>
                    <div class="col-md-4 mt-4" v-if="form.item_id && form.warehouse_id && form.series_enabled">
                        <!-- <el-button type="primary" native-type="submit" icon="el-icon-check">Elegir serie</el-button> -->
                        <a href="#"  class="text-center font-weight-bold text-info" @click.prevent="clickLotcodeOutput">[&#10004; Seleccionar series]</a>
                    </div>
                </div>
            </div>
            <div class="form-actions text-right mt-4">
                <el-button @click.prevent="close()">Cancelar</el-button>
                <el-button type="primary" native-type="submit" :loading="loading_submit">Aceptar</el-button>
            </div>
        </form>
        <output-lots-form
            :showDialog.sync="showDialogLotsOutput"
            :lots="form.lots"
            @addRowOutputLot="addRowOutputLot">
        </output-lots-form>
    </el-dialog>

</template>

<script>
    import OutputLotsForm from './partials/lots.vue'

    export default {
        components: {OutputLotsForm},
        props: ['showDialog', 'recordId'],
        data() {
            return {
                loading_submit: false,
                showDialogLotsOutput:false,
                titleDialog: null,
                resource: 'inventory',
                errors: {},
                form: {},
                items: [],
                warehouses: [],
            }
        },
        created() {
            this.initForm()
            this.$http.get(`/${this.resource}/tables`)
                .then(response => {
                    this.items = response.data.items
                    this.warehouses = response.data.warehouses
                })
        },
        methods: {
            addRowOutputLot(lots){
                this.form.lots = lots
            },
            clickLotcodeOutput(){
                this.showDialogLotsOutput = true
            },
            initForm() {
                this.errors = {}
                this.form = {
                    id: null,
                    item_id: null,
                    item_description: null,
                    warehouse_id: null,
                    warehouse_description: null,
                    quantity: null,
                    quantity_remove: 0,
                    lots_enabled:false,
                    lots:[]
                }
            },
            create() {
                this.titleDialog = 'Retirar producto de almacén'
                this.$http.get(`/${this.resource}/record/${this.recordId}`)
                    .then(response => {
                        this.form = response.data.data
                        this.form.lots = Object.values(response.data.data.lots)
                    })
            },
            async submit() {

                if(this.form.series_enabled){
                    let select_lots = await _.filter(this.form.lots, {'has_sale':true})
                    if(select_lots.length != this.form.quantity_remove){
                        return this.$message.error('La cantidad ingresada es diferente a las series seleccionadas');
                    }
                }

                this.loading_submit = true
                await this.$http.post(`/${this.resource}/remove`, this.form)
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success(response.data.message)
                            this.$eventHub.$emit('reloadData')
                            this.close()
                        } else {
                            this.$message.error(response.data.message)
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data.errors
                        } else {
                            console.log(error)
                        }
                    })
                    .then(() => {
                        this.loading_submit = false
                    })
            },
            close() {
                this.$emit('update:showDialog', false)
                this.initForm()
            },
        }
    }
</script>

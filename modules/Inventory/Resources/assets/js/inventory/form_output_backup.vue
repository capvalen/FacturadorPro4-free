
<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @close="close" @open="create">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group" :class="{'has-danger': errors.item_id}">
                            <label class="control-label">Producto</label>
                            <el-select v-model="form.item_id" filterable @change="changeItem">
                                <el-option v-for="option in items" :key="option.id" :value="option.id" :label="option.description"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.item_id" v-text="errors.item_id[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" :class="{'has-danger': errors.quantity}">
                            <label class="control-label">Cantidad</label>
                            <el-input v-model="form.quantity"></el-input>
                            <small class="form-control-feedback" v-if="errors.quantity" v-text="errors.quantity[0]"></small>

                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group" :class="{'has-danger': errors.warehouse_id}">
                            <label class="control-label">Almacén</label>
                            <el-select v-model="form.warehouse_id" filterable @change="changeItem">
                                <el-option v-for="option in warehouses" :key="option.id" :value="option.id" :label="option.description"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.warehouse_id" v-text="errors.warehouse_id[0]"></small>
                        </div>
                    </div>
                    <div style="padding-top: 3%;" class="col-md-2 col-sm-2" v-if="form.item_id && form.lots_enabled && form.lots_group.length > 0">
                        <a href="#"  class="text-center font-weight-bold text-info" @click.prevent="clickLotGroup">[&#10004; Seleccionar lote]</a>
                    </div>
                     <div style="padding-top: 3%;" class="col-md-3 col-sm-3" v-if="form.item_id && form.series_enabled">
                        <!-- <el-button type="primary" native-type="submit" icon="el-icon-check">Elegir serie</el-button> -->
                        <a href="#"  class="text-center font-weight-bold text-info" @click.prevent="clickSelectLots">[&#10004; Seleccionar series]</a>
                    </div>

                    <!--<div class="col-md-3" v-show="form.lots_enabled">
                        <div class="form-group" :class="{'has-danger': errors.date_of_due}">
                            <label class="control-label">Fec. Vencimiento</label>
                            <el-date-picker v-model="form.date_of_due" type="date" value-format="yyyy-MM-dd" :clearable="true"></el-date-picker>
                            <small class="form-control-feedback" v-if="errors.date_of_due" v-text="errors.date_of_due[0]"></small>
                        </div>
                    </div> -->

                    <!--<div style="padding-top: 3%" class="col-md-4" v-if="form.warehouse_id && form.series_enabled">

                        <a href="#"  class="text-center font-weight-bold text-info" @click.prevent="clickLotcode">[&#10004; Ingresar series]</a>
                    </div>  -->
                    <div class="col-md-8">
                        <div class="form-group" :class="{'has-danger': errors.inventory_transaction_id}">
                            <label class="control-label">Motivo traslado</label>
                            <el-select v-model="form.inventory_transaction_id" filterable>
                                <el-option v-for="option in inventory_transactions" :key="option.id" :value="option.id" :label="option.name"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.inventory_transaction_id" v-text="errors.inventory_transaction_id[0]"></small>
                        </div>
                    </div>

                </div>
            </div>
            <div class="form-actions text-right mt-4">
                <el-button @click.prevent="close()">Cancelar</el-button>
                <el-button type="primary" native-type="submit" :loading="loading_submit">Aceptar</el-button>
            </div>
        </form>


        <lots-group
            :quantity="form.quantity"
            :showDialog.sync="showDialogLots"
            :lots_group="form.lots_group"
            @addRowLotGroup="addRowLotGroup">
        </lots-group>

          <select-lots-form
            :showDialog.sync="showDialogSelectLots"
            :lots="form.lots"
            @addRowSelectLot="addRowSelectLot">
        </select-lots-form>

    </el-dialog>

</template>

<script>
  //  import InputLotsForm from '../../../../../../resources/js/views/tenant/items/partials/lots.vue'
   // import OutputLotsForm from './partials/lots.vue'
    import LotsGroup from './lots_group.vue'
     import SelectLotsForm from './lots.vue'


    export default {
        components: {LotsGroup, SelectLotsForm},
        props: ['showDialog', 'recordId'],
        data() {
            return {
                type: 'output',
               // lots: [],
                loading_submit: false,
                showDialogLots:false,
                showDialogSelectLots:false,
                titleDialog: null,
                resource: 'inventory',
                errors: {},
                form: {},
                items: [],
                warehouses: [],
                inventory_transactions: [],
            }
        },
        created() {
            this.initForm()
        },
        methods: {
            async changeItem(){

                    this.form.lots = []
                    let item = await _.find(this.items,{'id':this.form.item_id})
                    this.form.lots_enabled = item.lots_enabled
                    let lots = await _.filter(item.lots,{'warehouse_id':this.form.warehouse_id})
                   // console.log(item)
                    this.form.lots = lots
                    this.form.lots_enabled = item.lots_enabled
                    this.form.series_enabled = item.series_enabled
                    this.form.lots_group = item.lots_group


            },
            addRowOutputLot(lots){
                this.form.lots = lots
            },
            addRowLot(lots){
                this.form.lots = lots
            },
            clickLotcode(){
                this.showDialogLots = true
            },
            clickLotcodeOutput(){
                this.showDialogLotsOutput = true
            },
            initForm() {
                this.errors = {}
                this.form = {
                    id: null,
                    item_id: null,
                    warehouse_id: null,
                    inventory_transaction_id: null,
                    quantity: 0,
                    type: this.type,
                    lot_code:null,
                    lots_enabled:false,
                    series_enabled:false,
                    lots:[],
                    date_of_due: null,
                    IdLoteSelected:  null,
                    lots_group:  []

                }
            },
            async create() {

                this.titleDialog = 'Salida de producto del almacén'

                await this.$http.get(`/${this.resource}/tables/transaction/output`)
                    .then(response => {
                        this.items = response.data.items
                        this.warehouses = response.data.warehouses
                        this.inventory_transactions = response.data.inventory_transactions
                    })

            },
            async submit() {


                    if(this.form.lots.length>0 && this.form.series_enabled){

                        let select_lots = await _.filter(this.form.lots, {'has_sale':true})

                        if(select_lots.length != this.form.quantity){
                            return this.$message.error('La cantidad ingresada es diferente a las series seleccionadas');
                        }
                    }

                    if(this.form.lots_enabled){
                        if(!this.form.IdLoteSelected)
                            return this.$message.error('Debe seleccionar un lote.');
                    }


                this.loading_submit = true
                this.form.type = this.type
                // console.log(this.form)
                await this.$http.post(`/${this.resource}/transaction`, this.form)
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
                            this.errors = error.response.data
                            // console.log(error.response.data)
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
            clickLotGroup()
            {
                this.showDialogLots = true
            },
            addRowLotGroup(id)
            {
                this.form.IdLoteSelected =  id
            },
            async clickSelectLots(){
                this.showDialogSelectLots = true
            },
            addRowSelectLot(lots){
                this.form.lots = lots
            },
        }
    }
</script>

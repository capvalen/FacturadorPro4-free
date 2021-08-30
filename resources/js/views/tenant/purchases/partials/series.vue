<template>
    <el-dialog :title="titleDialog" width="40%"  :visible="showDialog"  @open="create"  :close-on-click-modal="false" :close-on-press-escape="false" append-to-body :show-close="false">

        <div class="form-body">
            <div class="row" >
                <div class="col-lg-12 col-md-12">
                    <table width="100%">
                        <thead>
                            <tr width="100%">
                                <th v-if="lots.length>0">Serie</th>
                                <th v-if="lots.length>0">Estado</th>
                                <th v-if="lots.length>0">Fecha</th>
                                <th width="15%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row, index) in lots" :key="index" width="100%" >
                                <td>
                                    <div class="form-group mb-2 mr-2"  >
                                        <el-input @blur="duplicateSerie(row.series, index)" v-model="row.series"></el-input>
                                    </div>
                                </td>
                                 <td>
                                    <div class="form-group mb-2 mr-2"  >
                                        <el-select  v-model="row.state">
                                            <el-option
                                                v-for="(option, index) in states"
                                                :key="index"
                                                :value="option"
                                                :label="option"
                                            ></el-option>
                                        </el-select>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-2 mr-2" >
                                        <el-date-picker v-model="row.date" type="date" value-format="yyyy-MM-dd" :clearable="false"></el-date-picker>
                                    </div>
                                </td>
                                <td class="series-table-actions text-center">
                                    <button  type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickCancel(index)">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                                <br>
                            </tr>
                        </tbody>
                    </table>


                </div>

            </div>
        </div>

        <div class="form-actions text-right pt-2">
            <el-button @click.prevent="clickCancelSubmit()">Cancelar</el-button>
            <el-button type="primary" @click="submit" >Guardar</el-button>
        </div>
    </el-dialog>
</template>

<script>
    export default {
        //props: ['showDialog', 'lots', 'stock'],
        data() {
            return {
                titleDialog: 'Series',
                loading: false,
                errors: {},
                form: {},
                states: ['Activo', 'Inactivo', 'Desactivado', 'Voz', 'M2m'],
                showDialog: false,
                stock:0,
                lots:[],
                indexItem: null
            }
        },
        async created() {

        },
        methods: {
            async duplicateSerie(data, index)
            {

                let duplicates = await _.filter(this.lots, {'series':data})
                if(duplicates.length > 1)
                {
                    this.lots[index].series = ''
                }
            },
            create(){

                if(this.lots.length == 0){
                    this.addMoreLots(this.stock)
                }else{
                    let quantity = this.stock - this.lots.length
                    if(quantity > 0){
                        this.addMoreLots(quantity)
                    }
                }

            },
            addMoreLots(number){

                for (let i = 0; i < number; i++) {
                    this.clickAddLot()
                }

            },
            deleteMoreLots(number){

                for (let i = 0; i < number; i++) {
                    this.lots.pop();
                    this.$emit('addRowLot', this.lots);
                }

            },
            async validateLots(){

                let error = 0

                await this.lots.forEach(element => {
                    if(element.series == null){
                        error++
                    }
                });

                if(error>0)
                    return {success:false, message:'El campo serie es obligatorio'}

                return {success:true}

            },
            async submit(){

                let val_lots = await this.validateLots()
                if(!val_lots.success)
                    return this.$message.error(val_lots.message);

                await this.$emit('addRowLot', {lots:this.lots, indexItem:this.indexItem});
                this.closeDialog()

            },
            clickAddLot() {

                if(this.lots.length >= this.stock)
                    return this.$message.error('La cantidad de registros es superior al stock o cantidad');

                this.lots.push({
                    id: null,
                    item_id: null,
                    series: null,
                    date:  moment().format('YYYY-MM-DD'),
                    state: 'Activo'

                });

                //this.$emit('addRowLot', this.lots);
            },

            close() {
                //this.$emit('update:showDialog', false)
                //this.$emit('addRowLot', this.lots);
            },
            clickCancel(index) {
                this.lots.splice(index, 1);
               // item.deleted = true
                //this.$emit('addRowLot', this.lots);
            },

            async clickCancelSubmit() {

                this.$emit('addRowLot', {lots:[], indexItem:this.indexItem});
                this.closeDialog()

            },
            close() {
            },
            openDialog(index, quantity,lots)
            {
                this.lots = lots
                this.stock = quantity
                this.indexItem = index
                this.showDialog = true
            },
            closeDialog()
            {
                this.showDialog = false
            }
        }
    }
</script>

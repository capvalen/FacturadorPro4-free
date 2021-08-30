<template>
    <el-dialog :title="titleDialog" width="50%"  :visible="showDialog"  @open="create" @opened="opened"  :close-on-click-modal="false" :close-on-press-escape="false" append-to-body :show-close="false">

        <div class="form-body">
            <div class="row" v-loading="loading">
                <div class="col-md-12" >
                    <el-button type="primary"  @click.prevent="clickAddLot()" >Agregar</el-button>
                </div>
                <div class="col-md-12 mt-2" >

                    <data-tables :data='lots'  :current-page.sync="currentPage" :table-props="tableProps" :pagination-props="{ pageSizes: [20] }" style="width: 100%" >
                    
                        <el-table-column
                            width="80"
                            prop="index"
                            label="#">
                            <template slot-scope="scope"> 
                                {{ scope.row.index + 1 }}
                            </template>
                        </el-table-column>

                        <el-table-column prop="series"  label="Series">
                            
                            <template slot-scope="scope">  
                                <el-input @blur="duplicateSerie(scope.row.series, scope.row.index)" v-model="scope.row.series"
                                    :ref="`ref_series_${scope.row.index}`"
                                    @keyup.enter.native="keyupEnterSeries(scope.row.series, scope.row.index)" ></el-input>
                            </template>

                        </el-table-column>

                        <el-table-column prop="state" label="Estado"   >
                            
                            <template slot-scope="scope">  
                                <el-select  v-model="scope.row.state">
                                    <el-option
                                        v-for="(option, index) in states"
                                        :key="index"
                                        :value="option"
                                        :label="option"
                                    ></el-option>
                                </el-select>
                            </template>

                        </el-table-column>

                        <el-table-column prop="date" label="Fecha" >
                            <template slot-scope="scope">  
                                <el-date-picker v-model="scope.row.date" type="date" value-format="yyyy-MM-dd" :clearable="false"></el-date-picker>
                            </template>
                        </el-table-column>
                        
                        <el-table-column
                            style="width: 10%"
                            width="80"
                            label="Acciones">
                            <template slot-scope="scope"> 
                                <el-button
                                    size="mini"
                                    type="danger"
                                    icon="el-icon-delete" 
                                    circle
                                    @click.prevent="clickCancel(scope.row.index)"></el-button>
                            </template>
                        </el-table-column>

                    </data-tables>
    

                </div>
            </div>
        </div>

        <div class="form-actions text-right pt-2 mt-3">
            <el-button @click.prevent="clickCancelSubmit()">Cancelar</el-button>
            <el-button type="primary" @click="submit" >Guardar</el-button>
        </div>
    </el-dialog>
</template>

<script>
    import { DataTables } from 'vue-data-tables'

    export default {
        components: {
            DataTables
        },
        props: ['showDialog', 'lots', 'stock','recordId'],
        data() {
            return {
                titleDialog: 'Series',
                loading: false,
                errors: {},
                form: {},
                states: ['Activo', 'Inactivo', 'Desactivado', 'Voz', 'M2m'],
                tableProps: {
                    border: true,
                },
                currentPage: 1,
                per_page: 20
            }
        },
        async created() {
 
        },
        methods: { 
            getMaxItems(index) {

                if(this.currentPage > 1){
                    index = index - this.per_page
                }
                
                return (this.per_page * (this.currentPage - 1)) + index + 1
            },
            async keyupEnterSeries(series, index){
                
                // console.log(series, index, this.getIndex())
                // console.log(this.$refs)

                if(index == this.getIndex() - 1){
                    return
                }

                try {
                    await this.changeFocus(index)
                    
                } 
                catch(e) {
                    
                    await this.nextPage()

                    await this.$nextTick(() => {
                        this.changeFocus(index)
                    })

                }
                
            },
            changeFocus(index){
                this.$refs[`ref_series_${index+1}`].$el.getElementsByTagName('input')[0].focus()
            },
            nextPage(){
                this.currentPage++
            },
            async duplicateSerie(data, index)
            {
                // console.log(data, index)
                if(data){

                    let duplicates = await _.filter(this.lots, {'series':data})
                    if(duplicates.length > 1)
                    {
                        this.$message.error('Ingresó una serie duplicada');
                        this.lots[index].series = ''
                    }
                    
                }
            },
            create(){
                this.loading = true
            },
            opened(){

                if(!this.recordId){

                    if(this.lots.length == 0){

                        this.addMoreLots(this.stock)

                    }else{

                        let quantity = this.stock - this.lots.length
                        if(quantity > 0){
                            this.addMoreLots(quantity)
                        }
                        // else{
                        //     this.deleteMoreLots(quantity)
                        // }
                    }

                }

                this.loading = false
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

                await this.$emit('addRowLot', this.lots);
                await this.$emit('update:showDialog', false)

            },
            async clickAddLot() {

                if(!this.recordId){
                    if(this.lots.length >= this.stock)
                        return this.$message.error('La cantidad de registros es superior al stock o cantidad');
                }

                // let _index = index ? index :  this.getIndex()

                await this.lots.push({
                    id: null,
                    item_id: null,
                    series: null,
                    date:  moment().format('YYYY-MM-DD'),
                    state: 'Activo',
                    index: this.getIndex(),

                });

                this.$emit('addRowLot', this.lots);
            },
            getIndex(){
                return this.lots.length
            },
            close() {
                this.$emit('update:showDialog', false)
                this.$emit('addRowLot', this.lots);
            },
            async clickCancel(index) {

                await this.lots.splice(index, 1);
                await this.renewIndexes()
                await this.$emit('addRowLot', this.lots);

            },
            async renewIndexes(){
                await this.lots.forEach((row, index)=>{
                    row.index = index
                })
            },
            async clickCancelSubmit() {
                this.$emit('addRowLot', []);
                await this.$emit('update:showDialog', false)
            },
            close() {
                this.$emit('update:showDialog', false)
            },
        }
    }
</script>

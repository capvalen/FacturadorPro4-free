<template>
    <div class="box m-custom" >
        <input name="item_selected" type="hidden" id="item_selected" ref="item_selected">
        <div class="box-body no-padding m-custom row" >
            
            <div class="col-md-8 m-custom" >
                <div>
                    <span>Producto</span>
                    <el-select @change="setProduct" v-model="product" filterable  :clearable="false" placeholder="Productos" class="m-custom">
                        <el-option v-for="item in data_products" :key="item.id" :label="getItemDescription(item)" :value="item.id" class="m-custom"></el-option>
                    </el-select>
                </div>
            </div>
            <div class="col-md-2">

                
                <div class="">
                    <span>Fecha inicio</span>                                              
                    <el-date-picker :inline=true v-model="d" type="date" name="d" placeholder="Inicio"></el-date-picker>
                </div>
            </div>
            <div class="col-md-2">
                <div class="">
                    <span>Fecha término</span>
                    <el-date-picker v-model="a"  :inline=true type="date" name="a" placeholder="Término"></el-date-picker>
                </div>
            </div>
            
        </div>
    </div>
</template>
<style>
.m-custom{
    margin-left:-6px !important
}
</style>
<script>
    import moment from 'moment'

    export default {
        props: {            
            'data_products': {
                required : true
            },
            'item_selected':{
                required : true
            },
            'data_d': {
                required: false,
                default: ''
            },
            'data_a': {
                required: false,
                default: ''
            },
        },
        data() {
            return {
                product: null,
                d: '',
                a: '',        
            }
        },
        created() {

            this.product = (this.item_selected) ? parseInt(this.item_selected) : null
            this.d = (this.data_d != '') ? moment(this.data_d) : '';
            this.a = (this.data_a != '') ? moment(this.data_a) : '';

        },
        methods:{ 
            getItemDescription(item){
                return (item.internal_id) ? `${item.internal_id} - ${item.description}` : item.description
            },
            setProduct()
            {
                this.$refs.item_selected.value = this.product
            }
        }
    }
</script>

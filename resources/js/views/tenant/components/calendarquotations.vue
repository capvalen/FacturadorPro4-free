<template>
    <div class="box ">
        <div class="box-body no-padding">
            <div class="el-form-item  col-xs-12">

                
                <div class="el-form-item__content">
                    <el-date-picker :inline=true v-model="d" type="date" name="d" placeholder="Inicio"></el-date-picker>
                </div>
            </div>
            <div class="el-form-item  col-xs-12">
                <div class="el-form-item__content">
                    <el-date-picker v-model="a" :inline=true type="date" name="a" placeholder="Término"></el-date-picker>
                </div>
            </div>
            <!--<div class="el-form-item  col-xs-12">
                <div class="el-form-item__content">
                    <el-select v-model="establishment2" name="establishment" clearable placeholder="Establecimiento">
                        <el-option v-for="establishment in establishments" :key="establishment.id" :label="establishment.description.toUpperCase()" :value="establishment.id"></el-option>
                    </el-select>
                </div>
            </div>
            <div class="el-form-item  col-xs-12">
                <div class="el-form-item__content">
                    <el-select v-model="document_type" name="document_type" clearable placeholder="Tipo de Documento">
                        <el-option v-for="item in document_types" :key="item.id" :label="item.description.toUpperCase()" :value="item.id"></el-option>
                    </el-select>
                </div>
            </div>-->
            
            <div class="el-form-item  col-xs-12">
                <div class="el-form-item__content">
                    <button class="btn btn-custom" type="submit"><i class="fa fa-search"></i> Buscar</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
           /*'document_types': {
                required: true
            },
            'establishments': {
                required: true
            },*/
            'data_d': {
                required: false,
                default: ''
            },
            'data_a': {
                required: false,
                default: ''
            },
            'td': {
                required: false,
                default: ''
            },
            /*'establishment': {
                required: false,
                default: ''
            },*/
        },
        data() {
            return {
                document_type: null,
                d: '',
                a: '',
                establishment2:null
            }
        },
        created() {
            /*this.document_type = (this.td != '') ? this.document_types.find(row => row.id == this.td).id : null; 
            this.establishment2 = (this.establishment != '') ? this.establishment : null;
            this.d = (this.data_d != '') ? moment(this.data_d) : '';
            this.a = (this.data_a != '') ? moment(this.data_a) : '';*/
        }
    }
</script>

<template>
    <div class="box ">
        <div class="box-body no-padding">
            <div class="el-form-item  col-xs-12">

                <div class="el-form-item  col-xs-12">
                    <div class="el-form-item__content">
                        <el-select v-model="warehouse2" name="warehouse" clearable placeholder="Almacén" filterable>
                            <el-option v-for="warehouse in warehouses" :key="warehouse.id" :label="warehouse.description.toUpperCase()" :value="warehouse.id" ></el-option>
                        </el-select>
                    </div>
                </div> 
                
                <div class="el-form-item  col-xs-12">
                    <div class="el-form-item__content">
                        <button class="btn btn-custom btn-sm" type="submit"><i class="fa fa-search"></i> Buscar</button>
                    </div>
                </div>
                </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: { 
            'warehouses': {
                required: true
            }, 
            'warehouse': {
                required: false,
                default: ''
            },
        },
        data() {
            return { 
                warehouse2:null
            }
        },
        created() {
            this.warehouse2 = (this.warehouse != '') ? this.warehouse : null;
        }
    }
</script>

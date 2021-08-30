<template> 
    <el-dialog :title="titleDialog" :visible="showDialog" width="30%"  :close-on-click-modal="false"      append-to-body top="7vh">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-12"  >
                        <el-input v-model="local_percentage_perception" ></el-input>
                         
                    </div>
                </div>
            </div>
            <div class="form-actions text-right pt-2">
                <el-button @click.prevent="close()">Cerrar</el-button>
                <el-button type="primary" @click.prevent="submit()">Guardar</el-button>
            </div>
        </form>
    </el-dialog>
</template>

<script>
 

    export default { 
        props:['showDialog', 'percentage_perception'],
        data() {
            return {
                showImportDialog: false,
                titleDialog: 'Porcentaje de percepción',
                local_percentage_perception:null

            }
        },
        created() {
            this.local_percentage_perception = this.percentage_perception
        }, 
        methods: {
            submit(){
                if(!this.local_percentage_perception) return this.$message.error('Ingrese un porcentaje');
                this.$eventHub.$emit('submitPercentagePerception', this.local_percentage_perception)
                this.$emit('update:showDialog', false)
                // this.close()
            },
            close() {
                this.local_percentage_perception = null
                this.$eventHub.$emit('submitPercentagePerception', this.local_percentage_perception)
                this.$emit('update:showDialog', false)
            },
        }
    }
</script>

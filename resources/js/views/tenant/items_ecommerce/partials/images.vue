<template> 
    <el-dialog :title="titleDialog" :visible="showDialog" width="30%"  :close-on-click-modal="false" @close="close"   append-to-body top="7vh">
        <div class="row">
             <div class="col-md-6">
                 <img :src="recordImages.image_url" alt="" >
             </div>
             <div class="col-md-6">
                 <img :src="recordImages.image_url_medium" alt="">
             </div>
             <div class="col-md-6">
                 <img :src="recordImages.image_url_small" alt="">
             </div>

              <el-button @click.prevent="close()">Cancelar</el-button>
        </div>
    </el-dialog>
</template>

<script>
 

    export default { 
        props: {
            showDialog: {
                type: Boolean,
                required: true,
                default: false
            },
            recordImages: {
                type: Object,
                required: true,
                default: {}
            }
        },
        data() {
            return {
                titleDialog: 'Imagenes',
            }
        },
        created() {
           
        }, 
        methods: {
            submit(){
               
            },
            close() {
                console.log('sd')
                this.$emit('update:showImageDetail', false)
            },
        }
    }
</script>

<template> 
    <el-dialog :title="title" class="text-left" :visible="showDialog" @close="close"   @open="create">
        <!-- <p class="text-center">* Se recomienda resoluciones 700x300.</p> -->
        <div class="text-center">
            <el-upload class="uploader" ref="upload" slot="append" :auto-upload="false" :headers="headers"  action="/documents/pay-constancy/upload" :show-file-list="false" :before-upload="beforeUpload" :on-success="successUpload" :on-change="preview">
                <img v-if="form.imageUrl" width="100%" :src="form.imageUrl" alt="">
                <i v-else class="el-icon-plus uploader-icon"></i>
            </el-upload>
        </div>
        <span slot="footer" class="dialog-footer">
            <el-button @click="clickCancel">Cancelar</el-button>
            <el-button @click="upload" class="submit" type="primary" :disabled="imageUrl == ''">Guardar</el-button>
        </span>
    </el-dialog> 
</template>

<script>
    export default {
        props: ['showDialog', 'path_img_detraction'],
        data() {
            return {
                headers: headers_token,
                dialogVisible: false,
                load: false,
                imageUrl: '',
                form:{},
                title:'Imágen - constancia de pago de detracción'
            }
        },
        created(){
            this.initForm()
            
            this.$eventHub.$on('eventInitForm', () => {
                this.initForm()
            })
        },
        computed: {
            src() {
                if (this.path_img_detraction != '') return this.path_img_detraction;
                
                return '/logo/700x300.jpg';
            }
        },
        methods: {
            create(){
                // console.log(this.path_img_detraction)
            },
            initForm(){
                this.form = { 
                    image: null,
                    imageUrl: null,
                    temp_path: null, 
                }

                this.imageUrl = null
            },
            beforeUpload(file) {
                const isIMG = ((file.type === 'image/jpeg') || (file.type === 'image/png') || (file.type === 'image/jpg'));
                const isLt2M = file.size / 1024 / 1024 < 2;
                
                if (!isIMG) this.$message.error('La imagen no es valida!');
                if (!isLt2M) this.$message.error('La imagen excede los 2MB!');
                
                return isIMG && isLt2M;
            },
            preview(file) {
                this.form.imageUrl = URL.createObjectURL(file.raw);
            },
            upload() {
                this.$refs.upload.submit();
            },
            async successUpload(response, file, fileList) {
                this.form.imageUrl = URL.createObjectURL(file.raw);
                // console.log(response)

                if (response.success) {
                    await this.$message.success('Imágen registrada temporalmente')
                    this.form.image = response.data.filename
                    this.form.imageUrl = response.data.temp_image
                    this.form.temp_path = response.data.temp_path
                    await this.$emit('addImageDetraction', this.form);
                    await this.$emit('update:showDialog', false)

                } else {
                    this.$message.error(response.message)
                }
                
                // this.$message({message:'Error al subir el archivo', type: 'error'});
                // this.imageUrl = '';
            },
            async clickCancel() {
                await this.initForm()
                await this.$emit('addImageDetraction', this.form);
                await this.$emit('update:showDialog', false)
            },
            close() {
                this.$emit('update:showDialog', false)
            }
        }
    }
</script>

<style lang="scss">
    .uploader .el-upload {
        border: 1px dashed #d9d9d9;
        border-radius: 6px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .uploader .el-upload:hover {
        border-color: #409EFF;
    }
    .uploader-icon {
        font-size: 28px;
        color: #8c939d;
        width: 178px;
        height: 178px;
        line-height: 178px;
        text-align: center;
    }
    
    .avatar {
        width: 178px;
        height: 178px;
        display: block;
    }
</style>

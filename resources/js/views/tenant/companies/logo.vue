<template>
    <div>
        <div :div_src="div_src" style="color: #CCC; cursor: pointer;" @click="dialogVisible = true">
            <img :src="src" class="img-fluid" style="max-height: 70px;">
        </div>
        <div class="">
            <el-dialog title="Logo" class="text-left" :visible.sync="dialogVisible">
                <p class="text-center">* Se recomienda resoluciones 700x300.</p>
                <div class="text-center">
                    <el-upload class="uploader" ref="upload" slot="append" :auto-upload="false" :headers="headers" :data="{'type': 'logo'}" action="/companies/uploads" :show-file-list="false" :before-upload="beforeUpload" :on-success="successUpload" :on-change="preview">
                        <img v-if="imageUrl" width="100%" :src="imageUrl" alt="">
                        <i v-else class="el-icon-plus uploader-icon"></i>
                    </el-upload>
                </div>
                <span slot="footer" class="dialog-footer">
                    <el-button @click="dialogVisible = false">Cerrar</el-button>
                    <el-button @click="upload" class="submit" type="primary" :disabled="imageUrl == ''">Aplicar</el-button>
                </span>
            </el-dialog>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['url', 'path_logo', 'position_class'],
        data() {
            return {
                headers: headers_token,
                dialogVisible: false,
                load: false,
                imageUrl: ''
            }
        },
        computed: {
            src() {
                if (this.path_logo != '') return this.path_logo;

                return '/logo/700x300.jpg';
            },
            div_src() {
                if (this.position_class != ''){
                    return this.position_class;
                } else {
                    this.position_class = 'text-center';
                    return this.position_class;
                }
            }
        },
        methods: {
            beforeUpload(file) {
                const isIMG = ((file.type === 'image/jpeg') || (file.type === 'image/png') || (file.type === 'image/jpg'));
                const isLt2M = file.size / 1024 / 1024 < 2;

                if (!isIMG) this.$message.error('La imagen no es valida!');
                if (!isLt2M) this.$message.error('La imagen excede los 2MB!');

                return isIMG && isLt2M;
            },
            preview(file) {
                this.imageUrl = URL.createObjectURL(file.raw);
            },
            upload() {
                this.$refs.upload.submit();
            },
            successUpload(response, file, fileList) {
                this.imageUrl = URL.createObjectURL(file.raw);

                if (response.success) {
                    this.$message.success(response.message);
                    location.href = this.url;
//                    this.load = true;

                    return;
                }

                this.$message({message:'Error al subir el archivo', type: 'error'});
                this.imageUrl = '';
            },
//            closed() {
//                this.dialogVisible = false;
//
//                if (this.load) location.href = this.url;
//            }
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

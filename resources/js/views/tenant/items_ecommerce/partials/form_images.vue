<template>
    <el-dialog width="50%" :title="titleDialog" :visible="showDialog" :close-on-click-modal="false" @close="close" @open="create" append-to-body top="7vh">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-12">
                        <el-upload
                            ref="upload_images"
                            list-type="picture-card"
                            :file-list="fileList"
                            :headers="headers"
                            :action="`/${resource}/upload`"
                            :on-success="onSuccessF"
                            :on-remove="handleRemove" >
                            <i class="el-icon-plus"></i>
                        </el-upload>
                    </div>
                </div>
            </div>
            <div class="form-actions text-right pt-2">
                <el-button @click.prevent="close()">Cancelar</el-button>
                <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
            </div>
        </form>

    </el-dialog>
</template>

<script>

    export default {
          props: ['showDialog', 'recordId'],
        data() {
            return {
                titleDialog: 'Imagenes',
                loading_submit: false,
                fileList: [],
                headers: headers_token,
                resource: 'items',
                source_images: []
            }
        },
        created() {

        },
        methods: {
            handleRemove(file, fileList)
            {
                if(file.id)
                {

                    this.$http.get(`/${this.resource}/images/delete/${file.id}`)
                        .then(response => {
                           console.log(response.data)
                        })

                }else{
                    let ind = this.source_images.findIndex( x => x.filename.includes(file.name))
                    this.source_images.splice(ind, 1);
                }

            },
            onSuccessF(response)
            {
                if(response.success)
                {
                    this.source_images.push(response.data)
                }else {

                    // this.cleanFileList()
                    this.$message.error(response.message)
                }
            },
            cleanFileList(){
                // this.fileList = []
            },
            async submit()
            {
                await this.$emit('saveImages', this.source_images);
                 await this.$emit('update:showDialog', false)
            },
            async close() {
                await this.$emit('update:showDialog', false)
                this.clear()
            },
            create()
            {
                 if (this.recordId) {
                    this.$http.get(`/${this.resource}/images/${this.recordId}`)
                        .then(response => {
                            this.fileList = response.data.data
                        })
                }
            },
            clear()
            {
                if(this.$refs.upload_images)
                {
                    this.$refs.upload_images.clearFiles();
                }

                this.source_images= []
            }
        }
    }
</script>

<template> 
    <el-dialog :title="title" class="text-left" :visible="showDialog"   @opened="create" @close="close" :close-on-click-modal="false">
        <!-- <p class="text-center">* Se recomienda resoluciones 700x300.</p> -->
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">  
                
                    <div class="row"> 

                        <div class="col-md-6"> 
                            <div class="row"> 
                                
                                <div class="short-div col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">N° Constancia de pago - detracción</label>
                                        <el-input v-model="detraction.pay_constancy">
                                        </el-input>
                                    </div>
                                </div>  

                            </div>
                        </div> 
                        
                        <div class="col-md-6">
                            <div class="form-group" >
                                <label class="control-label">Imágen constancia</label>
                                <el-upload class="uploader"
                                        :headers="headers"
                                        :action="`/documents/pay-constancy/upload`"
                                        :show-file-list="false" 
                                        :on-success="onSuccess">
                                    <img v-if="form.imageUrl" :src="form.imageUrl" width="100%" class="avatar">
                                    <i v-else class="el-icon-plus uploader-icon"></i>
                                </el-upload>
                            </div> 
                        </div>
                    </div> 
 
                <div class="form-actions text-right mt-4">
                    <el-button @click.prevent="clickCancel()">Cancelar</el-button>
                    <el-button type="primary" native-type="submit"  :loading="loading_submit">Guardar</el-button>
                </div>
            </div>
        </form>  
    </el-dialog> 
</template>

<script>
    export default {
        props: ['showDialog', 'recordId'],
        data() {
            return {
                headers: headers_token,
                resource:'documents',
                dialogVisible: false,
                loading_submit: false,
                load: false,
                imageUrl: '',
                form:{},
                detraction:{},
                title:'',
                payment_method_type: null,
            }
        },
        async created(){ 

            this.initForm() 
        }, 
        mounted(){
            // console.log(this.currencyTypeIdActive, this.exchangeRateSale)
        },
        methods: {
            async changeDetractionType(){ 

            },
            validateDetraction(){

                let detraction = this.detraction 

                if(!detraction.pay_constancy)
                    return {success:false, message:'El campo constancia de pago es obligatorio'}

                // if(!detraction.image_pay_constancy)
                //     return {success:false, message:'El campo imágen constancia es obligatorio'}

                return {success:true}

            },
            onSuccess(response, file, fileList) {
                // console.log(response)
                if (response.success) {
                    this.form.image = response.data.filename
                    this.form.imageUrl = response.data.temp_image
                    this.form.temp_path = response.data.temp_path
                    this.detraction.upload_image_pay_constancy = this.form
                    // console.log(this.detraction)
                } else {
                    this.$message.error(response.message)
                }
            },   
            async create(){
                
                await this.$http.get(`/${this.resource}/record/${this.recordId}`).then(response => {
                    let doc = response.data.data;

                    this.form.imageUrl = doc.image_detraction
                    this.detraction.pay_constancy = doc.detraction.pay_constancy
                    this.detraction.image_pay_constancy = doc.detraction.image_pay_constancy
                    this.detraction.id = doc.id

                    this.title = 'Constancia de detracción: '+doc.number;
                });
            }, 
            initForm(){
                this.form = { 
                    image: null,
                    imageUrl: null,
                    temp_path: null, 
                }

                this.detraction = {
                    id:null,
                    pay_constancy:null,
                    image_pay_constancy:null,
                    upload_image_pay_constancy:null
                }

                this.imageUrl = null
                // this.detraction.payment_method_id = (this.detraction.payment_method_id) ? this.detraction.payment_method_id:"001"

            },  
            clickCancel() {
                this.close()
            },
            close() {
                this.initForm()
                this.$emit('update:showDialog', false)
            },
            async submit(){
                
                let val_detraction = await this.validateDetraction()
                if(!val_detraction.success)
                    return this.$message.error(val_detraction.message);
 
                this.loading_submit = true
                this.$http.post(`/${this.resource}/pay-constancy/save`, this.detraction).then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        this.$eventHub.$emit('reloadData')
                        this.close()
                    }
                    else {
                        this.$message.error(response.data.message);
                    }
                }).catch(error => {

                    //alert('sdsd')
                    if (error.response.status === 422) {
                        this.errors = error.response.data;
                    }
                    else {
                        this.$message.error(error.response.data.message);
                    }
                }).then(() => {
                    this.loading_submit = false;
                });
                
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
        width: 250px;
        height: 155px;
        line-height: 155px;
        text-align: center;
    }
    
    .avatar {
        width: 100%;
        height: 155px;
        display: block;
    }
</style>

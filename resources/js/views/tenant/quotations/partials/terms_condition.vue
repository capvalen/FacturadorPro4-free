<template>
    <el-dialog :title="titleDialog" :visible="showDialog"   @close="close" @open="create"   append-to-body top="7vh">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-12" > 
                        <div class="form-group" >
                            <!-- <label class="control-label">
                            </label> -->
                            <!-- <el-input  type="textarea"  :rows="3" v-model="form.terms_condition"></el-input> -->
                            <vue-ckeditor type="classic" v-model="form.terms_condition" :editors="editors"></vue-ckeditor>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions text-right pt-2 mt-2">
                <el-button @click.prevent="close()">Cerrar</el-button>
                <el-button type="primary" @click.prevent="clickSubmit">Guardar</el-button>
            </div>
        </form>
    </el-dialog>
</template>

<script>

    import ClassicEditor from '@ckeditor/ckeditor5-build-classic'
    import VueCkeditor from 'vue-ckeditor5'

    export default {
        components: {'vue-ckeditor': VueCkeditor.component},
        props:['showDialog', 'form'],
        data() {
            return {
                showImportDialog: false,
                resource: 'items',
                recordId: null,
                titleDialog: 'Términos y condiciones',
                editors: {
                  classic: ClassicEditor
                },
            }
        },
        created() {
        },
        methods: {
            clickSubmit(){
                this.$eventHub.$emit('submitFormConfigurations', this.form)
                this.close()
            },
            create(){
                // console.log(this.form)
            },
            close() {
                this.$emit('update:showDialog', false)
            },
        }
    }
</script>

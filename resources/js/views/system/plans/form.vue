<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @close="close" @open="create">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.name}">
                            <label class="control-label">Nombre</label>
                            <el-input v-model="form.name" :maxlength="11"></el-input>
                            <small class="form-control-feedback" v-if="errors.name" v-text="errors.name[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.pricing}">
                            <label class="control-label">Precio</label>
                            <el-input v-model="form.pricing"></el-input>
                            <small class="form-control-feedback" v-if="errors.pricing" v-text="errors.pricing[0]"></small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.limit_users || errorLUser.limit_users}">
                            <label class="control-label">Límite de usuarios</label>
                            <el-input v-model="limit_users" @input="validateLUsers"  :disabled="users_unlimited"></el-input>
                            <el-checkbox v-model="users_unlimited" @change="setUnlimitUsers">Ilimitado</el-checkbox><br>
                            <small class="form-control-feedback d-block" v-if="errors.limit_users" v-text="errors.limit_users[0]"></small>
                            <small class="form-control-feedback" v-if="errorLUser.limit_users" v-text="errorLUser.limit_users[0]"></small> 
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.limit_documents || errorLDocument.limit_documents}">
                            <label class="control-label">Límite de documentos</label>
                            <el-input v-model="limit_documents" @input="validateLDocuments" :disabled="documents_unlimited"></el-input>
                            <el-checkbox v-model="documents_unlimited" @change="setUnlimitDocuments">Ilimitado</el-checkbox><br>
                            <small class="form-control-feedback d-block" v-if="errors.limit_documents" v-text="errors.limit_documents[0]"></small>
                            <small class="form-control-feedback" v-if="errorLDocument.limit_documents" v-text="errorLDocument.limit_documents[0]"></small>
                        </div>
                    </div>
                </div>
                <!-- <div class="row">
                    <div class="col-md-12 mt-3">
                        <div class="form-group" :class="{'has-danger': (errors.plan_documents)}">
                            <label class="control-label font-weight-bold mb-0">Habilitar documentos electrónicos</label> 

                            <el-checkbox-group v-model="form.plan_documents"  >
                                <el-checkbox v-for="(city,ind) in plan_documents" class="plan_documents" :label="city.id"  :key="ind">{{city.description}}</el-checkbox>
                            </el-checkbox-group>

                            <small class="form-control-feedback" v-if="errors.plan_documents" v-text="errors.plan_documents[0]"></small> 
                        </div>
                    </div>
                   
                </div> -->
            </div>
            <div class="form-actions text-right pt-2">
                <el-button @click.prevent="close()">Cancelar</el-button>
                <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
            </div>
        </form>
    </el-dialog>
</template>

<style>
.plan_documents{ display:block ; margin: 15px 0 ;}
</style>

<script>

    import {EventBus} from '../../../helpers/bus'

    export default {
        props: ['showDialog', 'recordId','plan_documents'],
        data() {
            return {
                loading_submit: false,
                titleDialog: null,
                resource: 'plans',
                documents_unlimited:null,
                users_unlimited:null,
                limit_users:null,
                limit_documents:null,
                errors: {},
                errorLDocument:{},
                errorLUser:{},
                form: {}, 
            }
        },
        created() {
            this.initForm() 
        },
        methods: {
            initForm() {
                this.limit_users = null
                this.limit_documents = null
                this.documents_unlimited = false
                this.users_unlimited = false
                this.errors = {}
                this.errorLDocument = {}
                this.errorLUser = {}
                this.form = {
                    id: null,
                    name: null,
                    pricing: null,
                    limit_users: null,
                    limit_documents: null,
                    plan_documents:[]
                }
            },
            create() {

                this.titleDialog = (this.recordId)? 'Editar plan':'Nuevo plan'
                if (this.recordId) {
                    this.$http.get(`/${this.resource}/record/${this.recordId}`).then(response => {
                            this.setData(response.data.data)
                        })
                }
            },
            submit() {   

                if(this.validateLUsers().limit_users || this.validateLDocuments().limit_documents)
                    return
                    
                this.transform()

                this.loading_submit = true  
                this.$http.post(`${this.resource}`, this.form)
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success(response.data.message)
                            this.$eventHub.$emit('reloadData')
                            this.close()
                        } else {
                            this.$message.error(response.data.message)
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data 
                        } else {
                            console.log(error.response)
                        }
                    })
                    .then(() => {
                        this.loading_submit = false
                    })
                    
            },
            setData(data){

                this.form = data
                this.form.plan_documents = Object.values(data.plan_documents)
                this.users_unlimited = (data.limit_users == 0) ? true : false
                this.documents_unlimited = (data.limit_documents == 0) ? true : false                
                this.limit_users = (this.users_unlimited) ? "∞": data.limit_users
                this.limit_documents = (this.documents_unlimited) ? "∞":  data.limit_documents

            },
            transform(){

                if(this.users_unlimited){
                    this.form.limit_users = 0
                }else{
                    this.form.limit_users = this.limit_users
                }

                if(this.documents_unlimited){
                    this.form.limit_documents = 0
                }else{
                    this.form.limit_documents = this.limit_documents
                }
                
            },
            validateLDocuments(){

                this.errorLDocument = {} 

                if(!this.documents_unlimited){
                    if(this.limit_documents < 1)
                        this.$set(this.errorLDocument, 'limit_documents', ['limite de documentos debe ser mayor a cero']);
                } 

                return this.errorLDocument 
            },            
            
            validateLUsers(){

                this.errorLUser = {}  
                 
                if(!this.users_unlimited){
                    if(this.limit_users < 1)
                        this.$set(this.errorLUser, 'limit_users', ['limite de usuarios debe ser mayor a cero']);
                }

                return this.errorLUser 
            },            
            setUnlimitDocuments(){
                this.limit_documents = (this.documents_unlimited) ? "∞" : null
                this.form.limit_documents = (this.limit_documents == "∞") ? 0 : this.limit_documents
            },
            setUnlimitUsers(){
                this.limit_users = (this.users_unlimited) ? "∞" : null
                this.form.limit_users = (this.limit_users == "∞") ? 0 : this.limit_users

            },
            close() {
                this.$emit('update:showDialog', false)
                this.initForm()
            }
        }
    }
</script>
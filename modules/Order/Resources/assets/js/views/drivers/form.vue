<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @close="close" @open="create" @opened="opened" :close-on-click-modal="false" append-to-body>
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.identity_document_type_id}">
                            <label class="control-label">Tipo Doc. Identidad <span class="text-danger">*</span></label>
                            <el-select v-model="form.identity_document_type_id" filterable  popper-class="el-select-identity_document_type" dusk="identity_document_type_id" @change="changeIdentityDocType">
                                <el-option v-for="option in identity_document_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.identity_document_type_id" v-text="errors.identity_document_type_id[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.number}">
                            <label class="control-label">Número <span class="text-danger">*</span></label>

                            <!-- <el-input v-model="form.number" :maxlength="maxLength" dusk="number">
                            </el-input> -->

                            <div v-if="api_service_token != false">
                                <x-input-service :identity_document_type_id="form.identity_document_type_id" v-model="form.number" @search="searchNumber"></x-input-service>
                            </div>
                            <div v-else>
                                <el-input v-model="form.number" :maxlength="maxLength" dusk="number">
                                    <template v-if="form.identity_document_type_id === '6' || form.identity_document_type_id === '1'">
                                        <el-button type="primary" slot="append" :loading="loading_search" icon="el-icon-search" @click.prevent="searchCustomer">
                                            <template v-if="form.identity_document_type_id === '6'">
                                                SUNAT
                                            </template>
                                            <template v-if="form.identity_document_type_id === '1'">
                                                RENIEC
                                            </template>
                                        </el-button>
                                    </template>
                                </el-input>
                            </div>

                            <small class="form-control-feedback" v-if="errors.number" v-text="errors.number[0]"></small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.name}">
                            <label class="control-label">Nombre <span class="text-danger">*</span></label>
                            <el-input v-model="form.name" dusk="name"></el-input>
                            <small class="form-control-feedback" v-if="errors.name" v-text="errors.name[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.license}">
                            <label class="control-label">Licencia</label>
                            <el-input v-model="form.license"  @keyup.native="keyUpLicense"></el-input>
                            <small class="form-control-feedback" v-if="errors.license" v-text="errors.license[0]"></small>
                        </div>
                    </div>
                </div>

                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.telephone}">
                            <label class="control-label">Teléfono</label>
                            <el-input v-model="form.telephone" dusk="telephone"></el-input>
                            <small class="form-control-feedback" v-if="errors.telephone" v-text="errors.telephone[0]"></small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions text-right mt-4">
                <el-button @click.prevent="close()">Cancelar</el-button>
                <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
            </div>
        </form>
    </el-dialog>
</template>

<script>

    import {serviceNumber} from '@mixins/functions'

    export default {
        mixins: [serviceNumber],
        props: ['showDialog', 'recordId', 'external'],
        data() {
            return {
                loading_submit: false,
                titleDialog: null,
                resource: 'drivers',
                errors: {},
                form: {},
                api_service_token: false,
                identity_document_types: []
            }
        },
        async created() {
            await this.initForm()
            await this.$http.get(`/${this.resource}/tables`)
                .then(response => {
                    this.api_service_token = response.data.api_service_token
                    this.identity_document_types = response.data.identity_document_types;
                })

        },
        computed: {
            maxLength: function () {
                if (this.form.identity_document_type_id === '6') {
                    return 11
                }
                if (this.form.identity_document_type_id === '1') {
                    return 8
                }
            }
        },
        methods: {
            keyUpLicense(e){
                if(this.form.license.length == 1 && e.keyCode !== 8 && this.form.number){
                    this.form.license = this.form.license.concat(this.form.number)
                }
            },
            initForm() {
                this.errors = {}

                this.form = {
                    id: null,
                    identity_document_type_id: '1',
                    number: '',
                    name: null,
                    license: null,
                    telephone: null, 
                }
            },
            async opened() {

            },
            create() {
                
                this.titleDialog = (this.recordId)? 'Editar Conductor':'Nuevo Conductor'

                if (this.recordId) {
                    this.$http.get(`/${this.resource}/record/${this.recordId}`)
                        .then(response => {
                            this.form = response.data.data
                        })
                }
            },
            submit() {
                this.loading_submit = true
                this.$http.post(`/${this.resource}`, this.form)
                    .then(response => {
                        if (response.data.success) {

                            this.$message.success(response.data.message)

                            if (this.external) {
                                this.$eventHub.$emit('reloadDataDrivers', response.data.id)
                            } else {
                                this.$eventHub.$emit('reloadData')
                            }

                            this.close()

                        } else {
                            this.$message.error(response.data.message)
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data
                        } else {
                            console.log(error)
                        }
                    })
                    .then(() => {
                        this.loading_submit = false
                    })
            },
            changeIdentityDocType(){
                // (this.recordId == null) ? this.setDataDefaultCustomer() : null
            },
            setDataDefaultCustomer(){

                // if(this.form.identity_document_type_id == '0'){
                //     this.form.number = '99999999'
                //     this.form.name = "Clientes - Varios"
                // }else{
                //     this.form.number = ''
                //     this.form.name = null
                // }

            },
            close() {
                this.$emit('update:showDialog', false)
                this.initForm()
            },
            searchCustomer() {
                this.searchServiceNumberByType()
            },
            searchNumber(data) {

                this.form.name = (this.form.identity_document_type_id === '1')?data.nombre_completo:data.nombre_o_razon_social;

            },
        }
    }
</script>

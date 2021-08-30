<template>
    <el-dialog :title="titleDialog"  @open="create" :visible="showDialog" append-to-body :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false" >
         <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.identity_document_type_id}">
                            <label class="control-label">Tipo Doc. Identidad <span class="text-danger">*</span></label>
                            <el-select v-model="form.identity_document_type_id" filterable  popper-class="el-select-identity_document_type" dusk="identity_document_type_id" >
                                <el-option v-for="option in identity_document_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.identity_document_type_id" v-text="errors.identity_document_type_id[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.number}">
                            <label class="control-label">Número <span class="text-danger">*</span></label>

                            <div v-if="api_service_token != false">
                                <x-input-service :identity_document_type_id="form.identity_document_type_id" v-model="form.number" @search="searchNumber"></x-input-service>
                            </div>
                            <div v-else>
                                <el-input v-model="form.number" :maxlength="maxLength" dusk="number">
                                    <template v-if="form.identity_document_type_id === '1'">
                                        <el-button type="primary" slot="append" :loading="loading_search" icon="el-icon-search" @click.prevent="searchCustomer">
                                            RENIEC
                                        </el-button>
                                    </template>
                                </el-input>
                            </div>

                            <small class="form-control-feedback" v-if="errors.number" v-text="errors.number[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.name}">
                            <label class="control-label">Nombres y Apellidos</label>
                            <el-input v-model="form.name"></el-input>
                            <small class="form-control-feedback" v-if="errors.name" v-text="errors.name[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.sex}">
                            <label class="control-label">Sexo</label>
                            <el-select v-model="form.sex" filterable  >
                                <el-option v-for="option in sexs" :key="option.id" :value="option.id" :label="option.description"></el-option>

                            </el-select>
                            <small class="form-control-feedback" v-if="errors.sex" v-text="errors.sex[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.ocupation}">
                            <label class="control-label">Ocupación</label>
                            <el-input v-model="form.ocupation"></el-input>
                            <small class="form-control-feedback" v-if="errors.ocupation" v-text="errors.ocupation[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.age}">
                            <label class="control-label">Edad</label>
                            <el-input v-model="form.age"></el-input>
                            <small class="form-control-feedback" v-if="errors.age" v-text="errors.age[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.civil_status}">
                            <label class="control-label">Estado civil</label>
                            <el-select v-model="form.civil_status" filterable  >
                                <el-option v-for="option in civil_status" :key="option.id" :value="option.id" :label="option.description"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.civil_status" v-text="errors.civil_status[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.nacionality}">
                            <label class="control-label">Nacionalidad</label>
                            <el-input v-model="form.nacionality"></el-input>
                            <small class="form-control-feedback" v-if="errors.nacionality" v-text="errors.nacionality[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.origin}">
                            <label class="control-label">Procedencia</label>
                            <el-input v-model="form.origin"></el-input>
                            <small class="form-control-feedback" v-if="errors.origin" v-text="errors.origin[0]"></small>
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

    export default {
        // mixins: [serviceNumber],
        props: ['showDialog', 'identity_document_types', 'api_service_token', 'sexs', 'civil_status'],
        data() {
            return {
                titleDialog: 'Nuevo Huesped',
                form: {},
                errors: {},
                loading_submit: false,
                resource: 'bussiness_turns',

            }
        },
        computed: {
            maxLength: function () {
                if (this.identity_document_type_id === '1') {
                    return 8
                }else{
                    return 12
                }
            }
        },
        async created() {

        },
        methods: {
            create()
            {
                this.initForm()
            },
            initForm()
            {
                this.form = {
                    identity_document_type_id: '1',
                    number: '',
                    name: null,
                    sex: null,
                    ocupation: null,
                    age: null,
                    civil_status: null,
                    nacionality: null,
                    origin: null
                }
            },
            async submit()
            {
                await this.$http.post(`/${this.resource}/validate_hotel_guest`, this.form)
                    .then(response => {
                        if (response.data.success) {
                           // this.$emit('addDocumentHotel', this.hotel);
                            this.$emit('addRowGuest', this.form);
                            this.close()
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
            searchCustomer() {
                this.searchServiceNumberByType()
            },
            async searchServiceNumberByType() {
                if(this.form.number === '') {
                    this.$message.error('Ingresar el número a buscar')
                    return
                }
                let identity_document_type_name = ''
                if (this.form.identity_document_type_id === '1') {
                    identity_document_type_name = 'dni'
                }
                this.loading_search = true
                let response = await this.$http.get(`/services/${identity_document_type_name}/${this.form.number}`)
                if(response.data.success) {
                     console.log(response)
                    let data = response.data.data
                    this.form.name = data.name
                    this.form.sex = data.sex == 'Masculino' ? 'M':'F'

                } else {
                    this.$message.error(response.data.message)
                }
                this.loading_search = false
            },
            searchNumber(data) {
                if(data){

                    this.form.name = data.nombre_completo
                    this.form.sex = data.sexo == 'MASCULINO' ? 'M':'F'
                }
           },
           close() {
                //if(flag) this.$emit('addDocumentHotel', {});
                this.errors = {}
                // this.initVars()
                this.$emit('update:showDialog', false)
            },


        }
    }
</script>

<template>
    <el-dialog :title="titleDialog"   :visible="showDialog"  @open="create"  :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false">
         <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">

                    <!-- <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.identity_document_type_id}">
                            <label class="control-label">Tipo Doc. Identidad</label>
                            <el-select v-model="hotel.identity_document_type_id" filterable  popper-class="el-select-identity_document_type" >
                                <el-option v-for="option in identity_document_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.identity_document_type_id" v-text="errors.identity_document_type_id[0]"></small>
                        </div>
                    </div> -->
                    <!-- <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.number}">
                            <label class="control-label">Número</label>
                                <el-input v-model="hotel.number" :maxlength="maxLength" >
                                </el-input>
                            <small class="form-control-feedback" v-if="errors.number" v-text="errors.number[0]"></small>
                        </div>
                    </div> -->

                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.identity_document_type_id}">
                            <label class="control-label">Tipo Doc. Identidad <span class="text-danger">*</span></label>
                            <el-select v-model="identity_document_type_id" filterable  popper-class="el-select-identity_document_type" dusk="identity_document_type_id" >
                                <el-option v-for="option in identity_document_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.identity_document_type_id" v-text="errors.identity_document_type_id[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.number}">
                            <label class="control-label">Número <span class="text-danger">*</span></label>

                            <div v-if="api_service_token != false">
                                <x-input-service :identity_document_type_id="identity_document_type_id" v-model="hotel.number" @search="searchNumber"></x-input-service>
                            </div>
                            <div v-else>
                                <el-input v-model="hotel.number" :maxlength="maxLength" dusk="number">
                                    <template v-if="identity_document_type_id === '1'">
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
                            <el-input v-model="name"></el-input>
                            <small class="form-control-feedback" v-if="errors.name" v-text="errors.name[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.sex}">
                            <label class="control-label">Sexo</label>
                            <el-select v-model="sex" filterable  >
                                <el-option v-for="option in sexs" :key="option.id" :value="option.id" :label="option.description"></el-option>

                            </el-select>
                            <small class="form-control-feedback" v-if="errors.sex" v-text="errors.sex[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.ocupation}">
                            <label class="control-label">Ocupación</label>
                            <el-input v-model="hotel.ocupation"></el-input>
                            <small class="form-control-feedback" v-if="errors.ocupation" v-text="errors.ocupation[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.age}">
                            <label class="control-label">Edad</label>
                            <el-input v-model="hotel.age"></el-input>
                            <small class="form-control-feedback" v-if="errors.age" v-text="errors.age[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.civil_status}">
                            <label class="control-label">Estado civil</label>
                            <el-select v-model="hotel.civil_status" filterable  >
                                <el-option v-for="option in civil_status" :key="option.id" :value="option.id" :label="option.description"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.civil_status" v-text="errors.civil_status[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.room_type}">
                            <label class="control-label">Tipo habitación</label>
                            <el-select v-model="hotel.room_type" filterable  >
                                <el-option v-for="option in room_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.room_type" v-text="errors.room_type[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.room_number}">
                            <label class="control-label">N° Habitación</label>
                            <el-input v-model="hotel.room_number"></el-input>
                            <small class="form-control-feedback" v-if="errors.room_number" v-text="errors.room_number[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.nacionality}">
                            <label class="control-label">Nacionalidad</label>
                            <el-input v-model="hotel.nacionality"></el-input>
                            <small class="form-control-feedback" v-if="errors.nacionality" v-text="errors.nacionality[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.origin}">
                            <label class="control-label">Procedencia</label>
                            <el-input v-model="hotel.origin"></el-input>
                            <small class="form-control-feedback" v-if="errors.origin" v-text="errors.origin[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.date_entry}">
                            <label class="control-label">Fecha Ingreso</label>
                            <el-date-picker v-model="hotel.date_entry" type="date" value-format="yyyy-MM-dd" :clearable="false" ></el-date-picker>
                            <small class="form-control-feedback" v-if="errors.date_entry" v-text="errors.date_entry[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.time_entry}">
                            <label class="control-label">Hora Ingreso</label>
                            <el-time-picker
                                v-model="hotel.time_entry" value-format="HH:mm:ss">
                            </el-time-picker>
                            <small class="form-control-feedback" v-if="errors.time_entry" v-text="errors.time_entry[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.date_exit}">
                            <label class="control-label">Fecha Salida</label>
                            <el-date-picker v-model="hotel.date_exit" type="date" value-format="yyyy-MM-dd" :clearable="false" ></el-date-picker>
                            <small class="form-control-feedback" v-if="errors.date_exit" v-text="errors.date_exit[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.time_exit}">
                            <label class="control-label">Hora Salida</label>
                            <el-time-picker
                                v-model="hotel.time_exit" value-format="HH:mm:ss">
                            </el-time-picker>
                            <small class="form-control-feedback" v-if="errors.time_exit" v-text="errors.time_exit[0]"></small>
                        </div>
                    </div>
                </div> <br>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <table width="100%">
                            <thead>
                                <tr width="100%">
                                    <th >Tipo Doc. </th>
                                    <th >Número  </th>
                                    <th >Nombres </th>
                                    <th >Sexo </th>
                                    <th >Ocupación </th>
                                    <th >Origen </th>
                                    <th width="15%"><a href="#" @click.prevent="showDialogGuest = true" class="text-center font-weight-bold text-info">[+ Agregar hueped]</a></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(row, index) in guests" :key="index" width="100%">
                                    <td>{{row.identity_document_type_id}}</td>
                                    <td>{{row.number}}</td>
                                    <td>{{row.name}}</td>
                                    <td>{{row.sex}}</td>
                                    <td>{{row.ocupation}}</td>
                                    <td>{{row.origin}}</td>
                                    <td class="series-table-actions text-center">
                                        <button  type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickCancelGuest(index)">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>

                                    <br>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>


            </div>
            <div class="form-actions text-right mt-4">
                <el-button @click.prevent="close(true)">Cancelar</el-button>
                <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
            </div>
        </form>
        <guest :showDialog.sync="showDialogGuest" :identity_document_types = "identity_document_types" :api_service_token="api_service_token" :sexs="sexs" :civil_status="civil_status" @addRowGuest="addRowGuest"></guest>
    </el-dialog>
</template>

<script>
    // import {serviceNumber} from '@mixins/functions'
    import Guest from './guest.vue'

    export default {
        // mixins: [serviceNumber],
        components: {Guest},
        props: ['showDialog', 'hotel'],
        data() {
            return {
                titleDialog: 'Datos personales para reserva de hospedaje',
                loading_submit: false,
                errors: {},
                resource: 'bussiness_turns',
                company: {},
                configuration: {},
                identity_document_types:[],
                api_service_token:false,
                sexs:[],
                civil_status:[],
                cards_brand:[],
                room_types:[],
                name:null,
                identity_document_type_id:'1',
                sex:null,
                loading_search: false,
                showDialogGuest: false,
                guests: []
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

            await this.$http.get(`/${this.resource}/tables`)
                .then(response => {
                    this.identity_document_types = response.data.identity_document_types
                    this.api_service_token = response.data.api_service_token
                    // console.log(this.api_service_token)
                    this.sexs = response.data.sexs
                    this.civil_status = response.data.civil_status
                    this.room_types = response.data.room_types
                })

            // this.hotel.identity_document_type_id = '1'
        },
        methods: {
            initVars(){
                this.name = null
                this.identity_document_type_id = '1'
                this.sex = null
            },
            create(){
                // console.log(_.isEmpty(this.hotel))
                if(_.isEmpty(this.hotel)){
                    this.initVars()
                }
            },
            async submit() {
                this.hotel.name = this.name
                this.hotel.sex = this.sex
                this.hotel.identity_document_type_id = this.identity_document_type_id
                this.hotel.guests = JSON.stringify( this.guests )

                this.loading_submit = true
                await this.$http.post(`/${this.resource}/validate_hotel`, this.hotel)
                    .then(response => {
                        if (response.data.success) {
                            this.$emit('addDocumentHotel', this.hotel);
                            this.close(false)
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
            close(flag) {
                if(flag) this.$emit('addDocumentHotel', {});
                this.errors = {}
                // this.initVars()
                this.$emit('update:showDialog', false)
            },
            searchCustomer() {
                this.searchServiceNumberByType()
            },
            async searchServiceNumberByType() {
                if(this.hotel.number === '') {
                    this.$message.error('Ingresar el número a buscar')
                    return
                }
                let identity_document_type_name = ''
                if (this.identity_document_type_id === '1') {
                    identity_document_type_name = 'dni'
                }
                this.loading_search = true
                let response = await this.$http.get(`/services/${identity_document_type_name}/${this.hotel.number}`)
                if(response.data.success) {
                    // console.log(response)
                    let data = response.data.data
                    this.name = data.name
                    this.sex = data.sex == 'Masculino' ? 'M':'F'

                } else {
                    this.$message.error(response.data.message)
                }
                this.loading_search = false
            },
            searchNumber(data) {
                // console.log(data.nombre_completo)
                if(data){

                    this.name = data.nombre_completo
                    this.sex = data.sexo == 'MASCULINO' ? 'M':'F'
                }
           },
           addRowGuest(row)
           {
               this.guests.push(row)
           },
           clickCancelGuest(index)
           {
                this.guests.splice(index, 1);
           }

        }
    }
</script>

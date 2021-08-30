<template>
    <div class="card">
        <div class="card-header bg-info">
            <h3 class="my-0">Certificado PSE - Datos Sunat</h3>
        </div>
        <div class="card-body">
            <div class="title border-bottom pt-0">
                <h4>Certificado</h4>
            </div>
            <div class="table-responsive pt-2" v-if="record">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Archivo</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>{{ record }}</td>
                        <td class="text-right">
                            <button type="button" class="btn waves-effect waves-light btn-xs btn-danger"
                                    @click.prevent="clickDelete">Eliminar</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="row pt-2" v-else>
                <div class="col-md-12">
                    <el-button  type="primary" @click="clickCreate">Subir Certificado .pfx</el-button>
                </div>
            </div>
            <div class="title border-bottom">
                <h4>Usuario Secundario SUNAT</h4>
            </div>
            <div class="row">
                <div class="col-md-6 py-2">
                    <div class="form-group">
                        <label class="control-label">Usuario</label>
                        <el-input v-model="soap_username"></el-input>
                        <div class="sub-title text-muted"><small>RUC + Usuario. Ejemplo: 01234567890ELUSUARIO</small></div>
                    </div>
                </div>
                <div class="col-md-6 py-2">
                    <div class="form-group">
                        <label class="control-label">Contraseña</label>
                        <el-input v-model="soap_password"></el-input>
                    </div>
                </div>
                <div class="col-md-12 py-2">
                    <div class="form-group">
                        <el-button type="primary" native-type="submit" @click.prevent="clickSaveSoapUser">Guardar</el-button>
                    </div>
                </div>
            </div>
        </div>
        <certificates-form :showDialog.sync="showDialog"
                           :recordId="recordId"></certificates-form>
    </div>
</template>

<script>

    import CertificatesForm from './form.vue'
    import {deletable} from '../../../mixins/deletable'

    export default {
        mixins: [deletable],
        components: {CertificatesForm},
        data() {
            return {
                showDialog: false,
                resource: 'certificates',
                recordId: null,
                record: {},
                soap_username:null,
                soap_password:null,

            }
        },
        created() {
            this.$eventHub.$on('reloadData', () => {
                this.getData()
            })
            this.getData()
        },
        methods: {
            clickSaveSoapUser()
            {
                 let soap_username = this.soap_username
                 let soap_password = this.soap_password

                  this.$http.post(`${this.resource}/saveSoapUser`, { soap_username, soap_password})
                    .then(response => {
                        if (response.data.success) {

                             this.$message.success(response.data.message)

                        } else {

                            this.$message.error(response.data.message)
                        }
                    })
                    .catch(error => {

                        this.$message.error("Sucedio un error.");

                    })
                    .then(() => {

                    })

            },
            getData() {
                this.$http.get(`/${this.resource}/record`)
                    .then(response => {
                        this.record = response.data.certificate
                        this.soap_username = response.data.soap_username
                        this.soap_password = response.data.soap_password
                        //this.config_system_env = response.data.config_system_env
                    })
            },
            clickCreate() {
                this.showDialog = true
            },
            clickDelete() {
                this.destroy(`/${this.resource}`).then(() =>
                    this.$eventHub.$emit('reloadData')
                )
            }
        }
    }
</script>

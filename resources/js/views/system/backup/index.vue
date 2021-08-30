<template>
    <div class="row">
        <div class="card col-md-8">
            <div class="card-header justify-content-center d-block">
                <h4>Generar backup</h4>
                <br>
                <form class="row pt-2" @submit.prevent="onSubmitBackups">
                    <div class="col-12 mb-2">
                        <span>Tipo</span> <br>
                        <el-radio v-model="formBackups.type" label="todos">Todos</el-radio>
                        <el-radio v-model="formBackups.type" label="individual">Individual</el-radio>
                    </div>
                    <div class="col-6 col-md-3 form-group" v-if="formBackups.type === 'individual'" :class="{'has-danger': errors.hostname_id}">
                        <el-select v-model="formBackups.hostname_id" clearable filterable placeholder="Selecciona un cliente">
                            <el-option v-for="cl in clients" :key="cl.hostname_id" :label="cl.name" :value="cl.hostname_id"></el-option>
                        </el-select>
                        <small class="form-control-feedback" v-if="errors.hostname_id" v-text="errors.hostname_id[0]"></small>
                    </div>
                    <div class="col-6 col-md-3 form-group">
                        <el-button @click.prevent="start()" :loading="loading_submit" :disabled="loading_submit">Iniciar Proceso</el-button>
                    </div>
                </form>
                <br><br>
                <p class="mb-0">Espacio disponible en disco: {{discUsed}}</p>
                <p class="mb-0">Espacio ocupado por archivos de facturación: {{storageSize}}</p>
            </div>
            <div class="card-body">
                <p v-if="newLastZip !== ''">Ultimo Backup generado: <strong>{{newLastZip.name}} {{newLastZipDate}}</strong></p>
                <el-button @click.prevent="clickDownload()" >Descargar</el-button>
                <hr>
                <p class="mb-2">Para restaurar una base de datos debe ejecutar los siguientes comandos.</p>
                <code>mysql -u [user] -p [database_name] < [filename].sql</code>
                <br>
                <hr>
                <p class="mb-2">Para restaurar los archivos descargados debe copiar todas carpetas dentro de la carpeta del cliente.</p>
                <code>cp [path_del_zip]/signed storage/app/tenancy/tenants/tenancy_[subdominio del cliente]</code> <br>
                <code>cp [path_del_zip]/unsigned storage/app/tenancy/tenants/tenancy_[subdominio del cliente]</code> <br>
                <code>cp [path_del_zip]/cdr storage/app/tenancy/tenants/tenancy_[subdominio del cliente]</code> <br>
                <code>cp [path_del_zip]/pdf storage/app/tenancy/tenants/tenancy_[subdominio del cliente]</code> <br>
                <p>Repetir para todas las carpetas que estan dentro del .zip</p>
            </div>
        </div>
        <div class="card col-md-4 mt-0">
            <div class="card-header">
                Enviar por FTP último backup generado
            </div>
            <div class="card-body">

                <small class="text-muted">Por seguridad sus datos FTP no son guardados</small>
                <form v-if="newLastZip !== ''">
                    <div class="form-group" :class="{'has-danger': errors.host}">
                        <label class="control-label">Host/IP</label>
                        <el-input v-model="form.host"></el-input>
                    </div>
                    <div class="form-group" :class="{'has-danger': errors.port}">
                        <label class="control-label">Puerto</label>
                        <el-input v-model="form.port"></el-input>
                    </div>
                    <div class="form-group" :class="{'has-danger': errors.username}">
                        <label class="control-label">Usuario</label>
                        <el-input v-model="form.username"></el-input>
                    </div>
                    <div class="form-group" :class="{'has-danger': errors.password}">
                        <label class="control-label">Contraseña</label>
                        <el-input v-model="form.password"></el-input>
                    </div>
                    <div v-if="newLastZip !== ''" class="form-group">
                        <el-button @click.prevent="uploadFtp()" :loading="loading_upload">Enviar</el-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
<script>
import $ from 'jquery'

export default {
    props: ['storageSize','discUsed', 'lastZip', 'clients'],
    data() {
        return {
            newLastZipDate: '',
            headers: null,
            resource: 'backup',
            errors: {},
            form: {},
            loading_submit: false,
            loading_upload: false,
            db: {
                error: '',
                content: '',
                status: '',
            },
            files: {
                error: '',
                content: '',
                status: '',
            },
            newLastZip: '',
            formBackups: {
                type: 'todos',
            },
            errors: {},
        }
    },
    created() {
        this.initForm();
    },
    methods: {
        onGenerateNnameLastFile() {
            if (this.newLastZip) {
                if (this.newLastZip.date) {
                    this.newLastZipDate = `creado el ${this.newLastZip.date}`;
                }
            }
        },
        clickDownload() {
            window.open(`/${this.resource}/download/${this.newLastZip.name}`, '_blank');
        },
        initForm(){
            this.form = {
                host: null,
                port: null,
                username: null,
                password: null,
            }
            this.newLastZip = this.lastZip;
            this.onGenerateNnameLastFile();
        },
        async start() {
            this.initContent()
            this.loading_submit = true
            this.backupDb()
        },
        initContent() {
            this.db.error = ''
            this.db.content = ''
            this.db.status = false
            this.files.error = ''
            this.files.content = ''
            this.files.status = false
        },
        backupDb() {
            this.$http.post(`/${this.resource}/db`, this.formBackups)
            .then(response => {
                if (response.data !== '') {
                    this.db.content = response.data
                    this.errors = {};
                    if (response.status === 200) {
                        this.db.status = 'success'
                    }
                    this.backupFiles()
                }
            }).catch(error => {
                const status = error.response.status;
                if (status === 422) {
                    this.errors = error.response.data;
                } else if (status !== 200) {
                    this.db.error = error.response.data.message
                    this.db.status = 'false'
                }
            }).finally(() => this.loading_submit = false);
        },
        backupFiles() {
            this.$http.post(`/${this.resource}/files`, this.formBackups)
                .then(response => {
                    if (response.data !== '') {
                        this.files.content = response.data
                        this.errors = {};
                        if (response.status === 200) {
                            this.files.status = 'success'
                            this.mostRecent()
                        }
                        this.loading_submit = false
                    }
                }).catch(error => {
                    const status = error.response.status;
                    if (status === 422) {
                        this.errors = error.response.data;
                    } else if (status !== 200) {
                        this.db.error = error.response.data.message
                        this.db.status = 'false'
                    }
                })

        },
        mostRecent(){
            this.$http.get(`/${this.resource}/last-backup`)
                .then(response => {
                    if (response.data !== '') {
                        this.newLastZip = response.data
                        this.loading_submit = false
                        this.onGenerateNnameLastFile();
                    }
                }).catch(error => {
                    if (error.response.status !== 200) {
                        this.files.error = error.response.data.message
                    } else {
                        console.log(error)
                    }
                })
        },
        uploadFtp() {
            this.loading_upload = true
            this.sendFtp()
        },
        sendFtp() {
            this.$http.post(`${this.resource}/upload`, this.form)
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message)
                        this.$eventHub.$emit('reloadData')
                        this.loading_upload = false
                        // this.close()
                        this.initForm()
                    } else {
                        this.$message.error(response.data.message)
                    }
                })
                .catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data
                    }else if(error.response.status === 500){
                        this.$message.error(error.response.data.message);
                    }
                        else {
                        console.log(error.response)
                    }
                })
                .then(()=>{
                    this.loading_upload = false
                })


        }
    }
}
</script>

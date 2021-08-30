<template>
    <el-dialog :title="titleDialog" :visible="showDialog"  @close="close" @open="create" append-to-body top="7vh">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
 
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.user_id}">
                            <label class="control-label">Vendedor</label>
                            <el-select v-model="form.user_id" filterable>
                                <el-option v-for="option in users" :key="option.id" :value="option.id" :label="option.name"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.user_id" v-text="errors.user_id[0]"></small>
                        </div>
                    </div>    
                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.type}">
                            <label class="control-label">Tipo comisión</label>
                            <el-select v-model="form.type">
                                <el-option v-for="option in types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.type" v-text="errors.type[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.amount}">
                            <label class="control-label">Monto</label>
                            <el-input v-model="form.amount" ></el-input>
                            <small class="form-control-feedback" v-if="errors.amount" v-text="errors.amount[0]"></small>
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
        props: ['showDialog', 'recordId'],

        data() {
            return { 
                loading_submit: false,
                titleDialog: null,
                resource: 'user-commissions', 
                form: {}, 
                text_type:null,
                types:[],
                users:[],
            }
        },
        created() {

             this.$http.get(`/${this.resource}/tables`)
                        .then(response => {
                            this.users = response.data.users
                        })

            this.types = [{id:'amount', description:'Monto'}, {id:'percentage', description:'Porcentaje'}]
            this.initForm() 

        },
        methods: { 
            initForm() {
                this.loading_submit = false,
                this.errors = {}
                this.form = {
                    id: null,
                    user_id: null, 
                    amount:0,
                    type:'amount'
                }
                this.show_has_igv = true
                this.enabled_percentage_of_profit = false
            }, 
            resetForm() {
                this.initForm() 
            },
            create() {
                this.titleDialog = 'Registrar comisión'
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
                            console.log(error)
                        }
                    })
                    .then(() => {
                        this.loading_submit = false
                    })
            },
            close() {
                this.$emit('update:showDialog', false)
                this.resetForm()
            }, 
        }
    }
</script>

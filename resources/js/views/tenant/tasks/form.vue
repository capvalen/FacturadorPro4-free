<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @close="close">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.class}">
                            <label class="control-label">Tarea</label>
                            <el-select v-model="form.class" dusk="class">
                                <el-option v-for="option in commnads" :key="option.name" :value="option.class" :label="option.name"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.class" v-text="errors.class[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.execution_time}">
                            <label class="control-label">Hora de ejecución</label>
                            <el-time-picker v-model="execution_time" format="HH:mm" placeholder="Seleccionar" dusk="execution_time" @change="setTime"></el-time-picker>
                            <small class="form-control-feedback" v-if="errors.execution_time" v-text="errors.execution_time[0]"></small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions text-right pt-2">
                <el-button @click.prevent="close()">Cancelar</el-button>
                <el-button type="primary" native-type="submit" :loading="loading_submit" dusk="submit">
                    <template v-if="loading_submit">
                        Creando tarea...
                    </template>
                    <template v-else>
                        Guardar
                    </template>
                </el-button>
            </div>
        </form>
    </el-dialog>
</template>

<script>
    export default {
        props: ['showDialog'],
        data() {
            return {
                loading_submit: false,
                execution_time: null,
                titleDialog: 'Nueva tarea',
                resource: 'tasks',
                errors: {},
                commnads: [],
                form: {}
            }
        },
        created() {
            this.$http.post(`/${this.resource}/commands`).then((response) => {
                this.commnads = response.data;
            }).catch((error) => {
                console.log(error);
            }).then(() => {});
        },
        methods: {
            setTime(timePicker) {
                this.form.execution_time = `${moment(timePicker).format('HH:mm')}:00`;
            },
            submit() {
                this.loading_submit = true;

                this.$http.post(`/${this.resource}`, this.form).then((response) => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        this.close();

                        this.form.class = null;
                        this.execution_time = null;
                        this.form.execution_time = null;

                        this.errors = {};

                        this.$emit('refresh');
                    }
                    else {
                        this.$message.error(response.data.message);
                    }
                }).catch((error) => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data;
                    }
                    else {
                        this.$message.error(error.response.data.message);
                    }
                }).then(() => {
                    this.loading_submit = false;
                });
            },
            close() {
                this.$emit('update:showDialog', false);
            }
        }
    }
</script>
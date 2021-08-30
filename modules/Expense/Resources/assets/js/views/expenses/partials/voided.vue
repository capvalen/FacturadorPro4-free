<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @close="close" @open="getData">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <!-- <div class="row">
                    <div class="col-md-12">
                        <div class="form-group" :class="{'has-danger': errors.description}">
                            <label class="control-label">Descripción del motivo de anulación</label>
                            <el-input v-model="form.documents[0].description"  dusk="description"></el-input>
                            <small class="form-control-feedback" v-if="errors.description" v-text="errors.description[0]"></small>
                        </div>
                    </div>
                </div> -->
            </div>
            <div class="form-actions text-right mt-4">
                <el-button @click.prevent="close()">Cancelar</el-button>
                <el-button type="danger" @click.prevent="voided()" :loading="loading_submit">Anular</el-button>
            </div>
        </form>
    </el-dialog>
</template>

<script>
    export default {
        props: ['showDialog', 'expenseId'],
        data() {
            return {
                titleDialog: null,
                loading_submit: false,
                resource: 'expenses',
                form: {},
            }
        },
        async created() {
            await this.initForm();
        },
        methods: {
            initForm() {
                this.form = {
                    expense:[]
                };
            },
            async getData() {
                this.initForm();
                await this.$http.get(`/${this.resource}/record/${this.expenseId}`)
                    .then(response => {
                        this.form = response.data.data
                        this.titleDialog = 'Anular: G-'+this.form.number;

                    });
            },
            close() {
                this.$emit('update:showDialog', false)
                this.initForm()
            },
            voided() {
                this.loading_submit = true;
                this.$http.get(`/${this.resource}/${this.expenseId}/voided`)
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success(response.data.message);
                        } else {
                            this.$message.error(response.data.message)
                        }
                    })
                    .then(() => {
                        this.loading_submit = false
                    });
                this.$emit('update:showDialog', false);
                this.$eventHub.$emit('reloadData');
            }
        }
    }
</script>

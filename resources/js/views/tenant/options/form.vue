<template>
    <form autocomplete="off" @submit.prevent="deleteDocuments">
        <el-button type="primary" native-type="submit" :loading="loading_submit">Eliminar documentos de prueba</el-button>
    </form>
</template>

<script>

    export default {
        data() {
            return {
                loading_submit: false,
                resource: 'options',
                errors: {},
                form: {},
                loading_submit_voided: false
            }
        },
        methods: {
            initForm() {
                this.errors = {}
                this.form = {}
            },
            deleteDocuments() {
                this.loading_submit = true
                this.$http.post(`/${this.resource}/delete_documents`)
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success(response.data.message)
                        } else {
                            this.$message.error(response.data.message)
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data.errors
                        } else {
                            console.log(error)
                        }
                    })
                    .then(() => {
                        this.loading_submit = false
                    })
            },
            consultVoided()
            {
                this.loading_submit_voided = true
                this.$http.get(`/voided/status_masive`)
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success(response.data.message)
                        } else {
                            this.$message.error('Sucedio un error')
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data.errors
                        } else {
                            console.log(error)
                        }
                    })
                    .then(() => {
                        this.loading_submit_voided = false
                    })
            }
        }
    }
</script>

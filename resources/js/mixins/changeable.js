export const changeable = {
    methods: {
        change(url) {
            return new Promise((resolve) => {
                this.$confirm('¿Desea cambiar la clave?', 'Cambiar clave', {
                    confirmButtonText: 'Cambiar',
                    cancelButtonText: 'Cancelar',
                    type: 'warning'
                }).then(() => {
                    this.$http.post(url)
                        .then(res => {
                            if(res.data.success) {
                                this.$message.success('Se cambió correctamente la clave')
                                resolve()
                            }
                        })
                        .catch(error => {
                            if (error.response.status === 500) {
                                this.$message.error('Error al intentar cambiar');
                            } else {
                                console.log(error.response.data.message)
                            }
                        })
                }).catch(error => {
                    console.log(error)
                });
            })
        },
    }
}
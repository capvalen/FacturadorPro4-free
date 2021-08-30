export const functions = {
    data() {
        return {
            loading_search_exchange_rate: false,
            loading_search: false
        }
    },
    methods: {
        searchExchangeRate() {
            return new Promise((resolve) => {
                this.loading_search_exchange_rate = true
                this.$http.post(`/services/exchange_rate`, this.form)
                    .then(response => {
                        let res = response.data
                        if (res.success) {
                            this.data = res.data;
                            this.form.buy = res.data[this.form.cur_date].buy;
                            this.form.sell = res.data[this.form.cur_date].sell;
                            this.$message.success(res.message)
                        } else {
                            this.$message.error(res.message)
                            this.loading_search_exchange_rate = false
                        }
                        resolve()
                    })
                    .catch(error => {
                        console.log(error.response)
                        this.loading_search_exchange_rate = false
                    })
                    .then(() => {
                        this.loading_search_exchange_rate = false
                    })
            })
        },

        searchServiceNumber() {
            return new Promise((resolve) => {
                this.loading_search = true
                let identity_document_type_name = ''
                if (this.form.identity_document_type_id === '6') {
                    identity_document_type_name = 'ruc'
                }
                if (this.form.identity_document_type_id === '1') {
                    identity_document_type_name = 'dni'
                }
                this.$http.get(`/services/${identity_document_type_name}/${this.form.number}`)
                    .then(response => {
                        console.log(response.data)
                        let res = response.data
                        if (res.success) {
                            this.form.name = res.data.name
                            this.form.trade_name = res.data.trade_name
                            this.form.address = res.data.address
                            this.form.department_id = res.data.department_id
                            this.form.province_id = res.data.province_id
                            this.form.district_id = res.data.district_id
                            this.form.phone = res.data.phone
                        } else {
                            this.$message.error(res.message)
                        }
                        resolve()
                    })
                    .catch(error => {
                        console.log(error.response)
                    })
                    .then(() => {
                        this.loading_search = false
                    })
            })
        }
    }
};

export const exchangeRate = {
    methods: {
        async searchExchangeRateByDate(exchange_rate_date) {
            let response = await this.$http.get(`/services/exchange_rate/${exchange_rate_date}`)
            return parseFloat(response.data.sale)
        }
    }
};

export const serviceNumber = {
    data() {
        return {
            loading_search: false
        }
    },
    methods: {
        filterProvince() {
            this.form.province_id = null
            this.form.district_id = null
            this.filterProvinces()
        },
        filterProvinces() {
            this.provinces = this.all_provinces.filter(f => {
                return f.department_id === this.form.department_id
            })
        },
        filterDistrict() {
            this.form.district_id = null
            this.filterDistricts()
        },
        filterDistricts() {
            this.districts = this.all_districts.filter(f => {
                return f.province_id === this.form.province_id
            })
        },
        async searchServiceNumberByType() {
            if(this.form.number === '') {
                this.$message.error('Ingresar el número a buscar')
                return
            }
            let identity_document_type_name = ''
            if (this.form.identity_document_type_id === '6') {
                identity_document_type_name = 'ruc'
            }
            if (this.form.identity_document_type_id === '1') {
                identity_document_type_name = 'dni'
            }
            this.loading_search = true
            let response = await this.$http.get(`/services/${identity_document_type_name}/${this.form.number}`)
            if(response.data.success) {
                let data = response.data.data
                this.form.name = data.name
                this.form.trade_name = data.trade_name
                this.form.address = data.address
                this.form.department_id = data.department_id
                this.form.province_id = data.province_id
                this.form.district_id = data.district_id
                this.form.phone = data.phone

                this.filterProvinces()
                this.filterDistricts()

            } else {
                this.$message.error(response.data.message)
            }
            this.loading_search = false
        },
        async searchServiceNumber() {
            if(this.form.number === '') {
                this.$message.error('Ingresar el número a buscar')
                return
            }
            this.loading_search = true
            let response = await this.$http.get(`/services/ruc/${this.form.number}`)
            if(response.data.success) {
                let data = response.data.data
                this.form.name = data.name
                this.form.trade_name = data.trade_name
            } else {
                this.$message.error(response.data.message)
            }
            this.loading_search = false
        }
    }
};

<template>
    <div class="card">
        <div class="card-header bg-info">
            <h3 class="my-0">Información Adicional</h3>
        </div>
        <div class="card-body">
            <form autocomplete="off" @submit.prevent="submit">
                <div class="form-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group" :class="{'has-danger': errors.description}">
                                <label class="control-label">Descripción</label>
                                <el-input v-model="form.description"></el-input>
                                <small class="form-control-feedback" v-if="errors.description" v-text="errors.description[0]"></small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group" :class="{'has-danger': errors.code}">
                                <label class="control-label">Código Domicilio Fiscal</label>
                                <el-input v-model="form.code" :maxlength="4"></el-input>
                                <small class="form-control-feedback" v-if="errors.code" v-text="errors.code[0]"></small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group" :class="{'has-danger': errors.country_id}">
                                <label class="control-label">País</label>
                                <el-select v-model="form.country_id" filterable>
                                    <el-option v-for="option in countries" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.country_id" v-text="errors.country_id[0]"></small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group" :class="{'has-danger': errors.department_id}">
                                <label class="control-label">Departamento</label>
                                <el-select v-model="form.department_id" filterable @change="filterProvince">
                                    <el-option v-for="option in all_departments" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.department_id" v-text="errors.department_id[0]"></small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group" :class="{'has-danger': errors.province_id}">
                                <label class="control-label">Provincia</label>
                                <el-select v-model="form.province_id" filterable @change="filterDistrict">
                                    <el-option v-for="option in provinces" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.province_id" v-text="errors.province_id[0]"></small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group" :class="{'has-danger': errors.province_id}">
                                <label class="control-label">Distrito</label>
                                <el-select v-model="form.district_id" filterable>
                                    <el-option v-for="option in districts" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                </el-select>
                                <small class="form-control-feedback" v-if="errors.district_id" v-text="errors.district_id[0]"></small>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group" :class="{'has-danger': errors.address}">
                                <label class="control-label">Dirección</label>
                                <el-input v-model="form.address"></el-input>
                                <small class="form-control-feedback" v-if="errors.address" v-text="errors.address[0]"></small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" :class="{'has-danger': errors.phone}">
                                <label class="control-label">Teléfono</label>
                                <el-input v-model="form.phone"></el-input>
                                <small class="form-control-feedback" v-if="errors.phone" v-text="errors.phone[0]"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" :class="{'has-danger': errors.email}">
                                <label class="control-label">Correo electrónico</label>
                                <el-input v-model="form.email"></el-input>
                                <small class="form-control-feedback" v-if="errors.email" v-text="errors.email[0]"></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-actions text-right pt-2">
                    <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
                </div>
            </form>
        </div>
    </div>

</template>

<script>

    export default {
        data() {
            return {
                loading_submit: false,
                resource: 'establishments',
                errors: {},
                form: {},
                countries: [],
                all_departments: [],
                all_provinces: [],
                all_districts: [],
                provinces: [],
                districts: [],
            }
        },
        async created() {
            await this.initForm()
            await this.$http.get(`/${this.resource}/tables`)
                .then(response => {
                    this.countries = response.data.countries
                    this.all_departments = response.data.departments
                    this.all_provinces = response.data.provinces
                    this.all_districts = response.data.districts
                })
            await this.$http.get(`/${this.resource}/record/1`)
                .then(response => {
                    if (response.data !== '') {
                        this.form = response.data.data
                        this.filterProvinces()
                        this.filterDistricts()
                    }
                })
        },
        methods: {
            initForm() {
                this.errors = {}
                this.form = {
                    id: null,
                    description: null,
                    country_id: '040000PE',
                    department_id: null,
                    province_id: null,
                    district_id: null,
                    address: null,
                    phone: null,
                    email: null,
                    code: null,
                }
            },
            submit() {
                this.loading_submit = true
                this.$http.post(`/${this.resource}`, this.form)
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
        }
    }
</script>

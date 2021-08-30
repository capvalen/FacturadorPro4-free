<template>
    <el-dialog :title="title" class="text-left" :visible="showDialog"   @opened="create" @close="close" :close-on-click-modal="false">
        <!-- <p class="text-center">* Se recomienda resoluciones 700x300.</p> -->
        <form autocomplete="off" @submit.prevent="submit" v-if="detraction">
            <div class="form-body">
                <div class="row mb-3" >
                    <div class="col-lg-6 col-md-6">
                        <div class="form-group" >
                            <label class="control-label">Bienes y servicios sujetos a detracciones<span class="text-danger"> *</span></label>
                            <el-select v-model="detraction.detraction_type_id" @change="changeDetractionType" filterable >
                                <el-option v-for="option in detraction_types" :key="option.id" :value="option.id" :label="`${option.description} - ${option.percentage}%`"></el-option>
                            </el-select>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="form-group" >
                            <label class="control-label">Método pago - Detracción<span class="text-danger"> *</span></label>
                            <el-select v-model="payment_method_type"  filterable @change="changePaymentMethod" ref="select_payment">
                                <el-option v-for="option in cat_payment_method_types" :key="option.id" :value="option.id" :label="`${option.description}`"></el-option>
                            </el-select>
                        </div>
                    </div>
                </div>

                    <div class="row">

                        <div class="col-md-6">
                            <div class="row">

                                <div class="short-div col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">N° Cta Detracciones<span class="text-danger"> *</span></label>
                                        <el-input v-model="detraction.bank_account" readonly></el-input>
                                    </div>
                                </div>

                                <div class="short-div col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">N° Constancia de pago - detracción</label>
                                        <el-input v-model="detraction.pay_constancy">
                                        </el-input>
                                    </div>
                                </div>
                                <div class="short-div col-md-12">

                                    <div class="form-group">
                                        <label class="control-label">Monto de la detracción
                                            <span class="text-danger"> *</span>
                                            <el-tooltip class="item" effect="dark" content="Se calcula automaticamente en base al total del comprobante" placement="top">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>
                                        </label>
                                        <el-input v-model="detraction.amount" readonly></el-input>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group" >
                                <label class="control-label">Imágen constancia</label>
                                <el-upload class="uploader"
                                        :headers="headers"
                                        :action="`/documents/pay-constancy/upload`"
                                        :show-file-list="false"
                                        :on-success="onSuccess">
                                    <img v-if="form.imageUrl" :src="form.imageUrl" class="avatar">
                                    <i v-else class="el-icon-plus uploader-icon"></i>
                                </el-upload>
                            </div>
                        </div>
                    </div>

                    <template v-if="operationTypeId == '1004'">
                        <h6>DETALLE - SERVICIOS DE TRANSPORTE DE CARGA</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Ubigeo origen<span class="text-danger"> *</span></label>
                                    <el-cascader :options="locations" v-model="detraction.origin_location_id" filterable></el-cascader>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Dirección origen<span class="text-danger"> *</span></label>
                                    <el-input v-model="detraction.origin_address"></el-input>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Ubigeo destino<span class="text-danger"> *</span></label>
                                    <el-cascader :options="locations" v-model="detraction.delivery_location_id" filterable></el-cascader>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Dirección destino<span class="text-danger"> *</span></label>
                                    <el-input v-model="detraction.delivery_address"></el-input>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Valor referencial servicio de transporte<span class="text-danger"> *</span></label>
                                    <el-input-number v-model="detraction.reference_value_service" :precision="2" :step="1" :min="0.01"></el-input-number>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Valor referencia carga efectiva<span class="text-danger"> *</span></label>
                                    <el-input-number v-model="detraction.reference_value_effective_load" :precision="2" :step="1" :min="0.01"></el-input-number>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Valor referencial carga útil<span class="text-danger"> *</span></label>
                                    <el-input-number v-model="detraction.reference_value_payload" :precision="2" :step="1" :min="0.01"></el-input-number>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Detalle del viaje<span class="text-danger"> *</span></label>
                                    <el-input v-model="detraction.trip_detail" type="textarea"></el-input>
                                </div>
                            </div>
                        </div>
                    </template>

                <div class="form-actions text-right mt-4">
                    <el-button @click.prevent="clickCancel()">Cancelar</el-button>
                    <el-button type="primary" native-type="submit" >Guardar</el-button>
                </div>
            </div>
        </form>
    </el-dialog>
</template>

<script>
    export default {
        props: ['showDialog', 'detraction','total', 'currencyTypeIdActive', 'operationTypeId', 'exchangeRateSale'],
        data() {
            return {
                headers: headers_token,
                resource:'documents',
                dialogVisible: false,
                load: false,
                imageUrl: '',
                form:{},
                title:'Registrar datos de detracción',
                cat_payment_method_types: [],
                payment_method_type: null,
                detraction_types: [],
                locations: [],
                all_detraction_types: [],
            }
        },
        async created(){

            await this.$http.get(`/${this.resource}/detraction/tables`)
                .then(response => {
                    this.all_detraction_types = response.data.detraction_types
                    this.cat_payment_method_types = response.data.cat_payment_method_types
                    this.locations = response.data.locations
                })

            this.initForm()

            this.$eventHub.$on('eventInitForm', () => {
                this.initForm()
            })
        },
        mounted(){
            // console.log(this.currencyTypeIdActive, this.exchangeRateSale)
        },
        methods: {
            async changeDetractionType(){
                let detraction_type = await _.find(this.detraction_types, {'id':this.detraction.detraction_type_id})

                if(detraction_type){

                    this.detraction.percentage = detraction_type.percentage
                    this.detraction.amount = (this.currencyTypeIdActive == 'PEN') ? _.round(parseFloat(this.total) * (detraction_type.percentage/100),2): _.round((parseFloat(this.total) * this.exchangeRateSale) * (detraction_type.percentage/100),2)
                    // console.log(detraction_type, this.form.detraction)

                }
            },
            validateDetraction(){

                let detraction = this.detraction

                if(!detraction.detraction_type_id)
                    return {success:false, message:'El campo bien o servicio sujeto a detracción es obligatorio'}

                if(!this.payment_method_type)
                    return {success:false, message:'El campo método de pago - detracción es obligatorio'}

                // if(!detraction.payment_method_id)
                //     return {success:false, message:'El campo método de pago - detracción es obligatorio'}

                if(!detraction.bank_account)
                    return {success:false, message:'El campo cuenta bancaria es obligatorio'}

                if(this.operationTypeId == '1004'){

                    if(!detraction.origin_location_id)
                        return {success:false, message:'El campo Ubigeo origen es obligatorio'}

                    if(!detraction.origin_address)
                        return {success:false, message:'El campo Dirección origen es obligatorio'}

                    if(!detraction.delivery_location_id)
                        return {success:false, message:'El campo Ubigeo destino es obligatorio'}

                    if(!detraction.delivery_address)
                        return {success:false, message:'El campo Dirección destino es obligatorio'}

                    if(!detraction.reference_value_service)
                        return {success:false, message:'El campo Valor referencial servicio de transporte es obligatorio'}

                    if(!detraction.reference_value_effective_load)
                        return {success:false, message:'El campo Valor referencia carga efectiva es obligatorio'}

                    if(!detraction.reference_value_payload)
                        return {success:false, message:'El campo Valor referencial carga útil es obligatorio'}

                    if(!detraction.trip_detail)
                        return {success:false, message:'El campo Detalle del viaje es obligatorio'}

                }

                return {success:true}

            },
            onSuccess(response, file, fileList) {
                // console.log(response)
                if (response.success) {
                    this.form.image = response.data.filename
                    this.form.imageUrl = response.data.temp_image
                    this.form.temp_path = response.data.temp_path
                    this.detraction.image_pay_constancy = this.form
                    // console.log(this.detraction)
                } else {
                    this.$message.error(response.message)
                }
            },
            create(){
                this.$message.warning('Sujeta a detracción');
                this.filterDetractionTypes()
                // console.log(this.$refs.select_payment.$el.getElementsByTagName('input')[0])
                // this.$refs.select_payment.$el.getElementsByTagName('input')[0].value = "001"
            },
            filterDetractionTypes(){
                this.detraction_types = _.filter(this.all_detraction_types, {operation_type_id: this.operationTypeId})
            },
            initForm(){
                this.form = {
                    image: null,
                    imageUrl: null,
                    temp_path: null,
                }

                this.imageUrl = null
                this.payment_method_type = "001"
                // this.detraction.payment_method_id = (this.detraction.payment_method_id) ? this.detraction.payment_method_id:"001"

            },
            changePaymentMethod(){
                this.detraction.payment_method_id = this.payment_method_type

            },
            async clickCancel() {
                await this.initForm()
                this.$emit('addDocumentDetraction', {});
                await this.$emit('update:showDialog', false)
            },
            close() {
                this.$emit('update:showDialog', false)
            },
            async submit(){

                let val_detraction = await this.validateDetraction()
                if(!val_detraction.success)
                    return this.$message.error(val_detraction.message);

                this.detraction.payment_method_id = this.payment_method_type
                this.detraction.has_data_detraction = true
                await this.$emit('addDocumentDetraction', this.detraction);
                await this.$emit('update:showDialog', false)

            }
        }
    }
</script>

<style lang="scss">
    .uploader .el-upload {
        border: 1px dashed #d9d9d9;
        border-radius: 6px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .uploader .el-upload:hover {
        border-color: #409EFF;
    }
    .uploader-icon {
        font-size: 28px;
        color: #8c939d;
        width: 200px;
        height: 155px;
        line-height: 155px;
        text-align: center;
    }

    .avatar {
        width: 100%;
        height: 155px;
        display: block;
    }
</style>

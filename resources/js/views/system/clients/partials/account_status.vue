<template>
    <el-dialog  :title="title" :visible="showDialog" @close="close" @open="getData">
        <div class="form-body">
            <div class="row">
                <div class="col-md-6"> 
                    
                    <div class="form-group" >
                        <label class="control-label d-block">Nombre</label>
                        <!-- <label class="d-block"  v-text="client.client.name" ></label> -->
                        <el-input  v-model="client.name" readonly></el-input>
                    </div>
                    <div class="form-group" >
                        <label class="control-label d-block">RUC</label>
                        <!-- <label class="d-block"  v-text="client.number" ></label> -->
                        <el-input  v-model="client.number" readonly></el-input>
                    </div>
                    <div class="form-group" >
                        <label class="control-label d-block">C. electrónico</label>
                        <!-- <label  class="d-block"  v-text="client.email" ></label> -->
                        <el-input  v-model="client.email" readonly></el-input>
                    </div>
                </div> 
                <div class="col-md-6"> 

                    <div class="form-group" >
                        <label class="control-label d-block">Total deuda</label>
                        <el-input  v-model="totals.total_due" readonly>
                            <template slot="prepend">S/ </template>
                        </el-input>
                    </div>
                    <center class="mt-4">
                        <img :src="image_url" height="80" width="80" />
                        <label class="d-block" v-if="totals.total_due>0">
                            Tiene deuda
                        </label>
                        <label class="d-block" v-else>
                            No tiene deuda
                        </label>
                    </center>

                </div> 
            </div> 

            <div class="row mt-4">
                <div class="col-md-12" v-if="records.length > 0">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Fec. Pago</th>
                                <th>Método de pago</th>
                                <th>Tarjeta</th>
                                <th>Referencia</th>
                                <th class="text-right">Estado</th>
                                <th class="text-right">Monto</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(row, index) in records">
                                <td>{{ row.date_of_payment }}</td>
                                <td>{{ row.payment_method_type_description }}</td>
                                <td v-if="row.card_brand">{{ row.card_brand.description }}</td>
                                <td v-else>-</td>
                                <td>{{ row.reference }}</td>
                                <td class="text-right">{{ row.state_description }}</td>
                                <td class="text-right">S/ {{ row.payment }}</td>
                               
                                 
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="5" class="text-right">TOTAL PAGADO</td>
                                <td class="text-right">S/ {{ totals.total_paid }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-right">TOTAL A PAGAR</td>
                                <td class="text-right">S/ {{ totals.total }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-right">PENDIENTE DE PAGO</td>
                                <td class="text-right">S/ {{ totals.total_difference }}</td>
                                <td></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div> 
            </div>

        </div>
    </el-dialog>

</template>

<script>


    export default {
        props: ['showDialog', 'clientId'],

        data() {
            return {
                title: null,
                recordId: null,
                image_url: null,
                resource: 'client_account_status',
                records: [],
                payment_method_types: [],
                card_brands: [],
                showAddButton: true,
                has_card: false,
                client: {},
                totals: {},
            }
        },
        async created() {
            await this.initForm(); 
        },
        methods: { 
            initForm() {
                this.records = [];
                this.showAddButton = true;
                this.title = 'Estado de cuenta'
            },
            async getData() {
                this.initForm();
                await this.$http.get(`/${this.resource}/client/${this.clientId}`)
                    .then(response => {
                        this.client = response.data.client;
                        this.totals = response.data.totals;
                        this.image_url = response.data.image_url;
                    });
                await this.$http.get(`/${this.resource}/records/${this.clientId}`)
                    .then(response => {
                        this.records = response.data.data
                    });
            },
            clickAddRow() {
                this.records.push({
                    id: null,
                    date_of_payment: moment().format('YYYY-MM-DD'),
                    payment_method_type_id: null,
                    card_brand_id: null,
                    reference: null,
                    reference: null,
                    payment: 0,
                    errors: {},
                    loading: false
                });
                this.showAddButton = false;
            },
            clickCancel(index) {
                this.records.splice(index, 1);
                this.showAddButton = true;
            }, 
            close() {
                this.$emit('update:showDialog', false); 
            } 
        }
    }
</script>
<template>
    <el-dialog
        :title="titleDialog"
        width="40%"
        :visible="showDialog"
        @open="create"
        :close-on-click-modal="false"
        :close-on-press-escape="false"
        append-to-body
        :show-close="false"
    >
        <div class="form-body">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Seleccionado</th>
                                <th>
                                   codigo
                                </th>
                                <th>Cantidad</th>
                                <th class="">Fecha vencimiento</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row, index) in lots_group" v-show="row.quantity > 0" :key="index">
                                <th align="center">
                                    <el-checkbox
                                        :disabled="row.quantity  < 0"
                                        v-model="row.checked"
                                        @change="changeSelect(index, row.id, row.quantity)"
                                    ></el-checkbox>
                                </th>
                                <th>{{ row.code }}</th>
                                <th class="">{{ row.quantity }}</th>
                                <th class="">{{ row.date_of_due }}</th>

                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="form-actions text-right pt-2">
            <el-button type="primary" @click="submit">Guardar</el-button>
        </div>
    </el-dialog>
</template>

<script>
export default {
    props: ["showDialog", "lots_group", "stock", "recordId", "quantity"],
    data() {
        return {
            titleDialog: "Lotes",
            loading: false,
            errors: {},
            form: {},
            states: ["Activo", "Inactivo", "Desactivado", "Voz", "M2m"],
            idSelected: null
        };
    },
    async created() {
        // await this.$http.get(`/pos/payment_tables`)
        //     .then(response => {
        //         this.payment_method_types = response.data.payment_method_types
        //         this.cards_brand = response.data.cards_brand
        //         this.clickAddLot()
        //     })
    },
    methods: {
        changeSelect(index, id, quantity_lot)
        {
            
            if (this.quantity > quantity_lot) {

                this.$message.error('La cantidad a vender es superior al stock');
                this.lots_group[index].checked = false;

            }else {

                this.lots_group.forEach(row => {
                    row.checked  = false
                })

                this.lots_group[index].checked = true

                this.idSelected = id

            }

        },
        handleSelectionChange(val) {
            //this.$refs.multipleTable.clearSelection();
            let row = val[val.length - 1];
            this.multipleSelection = [row];
        },
        create() {},

        async submit() {
            await this.$emit("addRowLotGroup", this.idSelected);
            await this.$emit("update:showDialog", false);
        },

        clickCancel(item) {
            //this.lots.splice(index, 1);
            item.deleted = true;
            this.$emit("addRowLotGroup", this.lots);
        },

        async clickCancelSubmit() {
            this.$emit("addRowLotGroup", []);
            await this.$emit("update:showDialog", false);
        },
        close() {
            this.$emit("update:showDialog", false);
        }
    }
};
</script>

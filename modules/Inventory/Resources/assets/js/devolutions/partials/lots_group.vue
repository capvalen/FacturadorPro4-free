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
                <div class="col-lg-5 col-md-5 col-sm-12 pb-2">
                    <el-input placeholder="Buscar código ..."
                        v-model="search"
                        style="width: 100%;"
                        prefix-icon="el-icon-search"
                        @input="filter">
                    </el-input>
                </div>

                <div class="col-lg-12 col-md-12">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Seleccionado</th>
                                <th>codigo</th>
                                <th>Cantidad</th>
                                <th class>Fecha vencimiento</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(row, index) in lots_group_"
                                :key="index"
                                v-show="row.quantity > 0"
                            >
                                <th align="center">
                                    <el-checkbox
                                        :disabled="row.quantity  < 0"
                                        v-model="row.checked"
                                        @change="changeSelect(index, row.id, row.quantity)"
                                    ></el-checkbox>
                                </th>
                                <th>{{ row.code }}</th>
                                <th class>{{ row.quantity }}</th>
                                <th class>{{ row.date_of_due }}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="form-actions text-right pt-2">
            <el-button @click.prevent="close()">Cerrar</el-button>
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
            idSelected: null,
            search: '',
            lots_group_: []
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
        filter(){

            if(this.search)
            {
                this.lots_group_ = _.filter(this.lots_group, x => x.code.toUpperCase().includes(this.search.toUpperCase()))
            }
            else{
                this.lots_group_ = this.lots_group
            }
        },
        changeSelect(index, id, quantity_lot) {

            if (this.quantity > quantity_lot) {
                this.$message.error('La cantidad a vender es superior al stock');
                this.lots_group_[index].checked = false;
            } else {

                this.lots_group.forEach((row) => {
                    row.checked = false;
                });

                this.lots_group_.forEach((row) => {
                    row.checked = false;
                });

                this.lots_group_[index].checked = true;

                this.idSelected = id;
            }

        },
        handleSelectionChange(val) {
            //this.$refs.multipleTable.clearSelection();
            let row = val[val.length - 1];
            this.multipleSelection = [row];
        },
        create() {
          this.filter()
        },

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
        },
    },
};
</script>

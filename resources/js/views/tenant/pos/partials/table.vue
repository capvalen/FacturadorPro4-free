<template>
    <div>
        <Keypress key-event="keyup" :key-code="40" @success="handle40" />
        <Keypress key-event="keyup" :key-code="38" @success="handle38" />
        <Keypress key-event="keyup" :key-code="13" @success="handle13" />

        <el-table
            ref="singleTable"
            :data="records"
            highlight-current-row
            @current-change="handleCurrentChange"
            style="width: 100%"
        >
            <el-table-column type="index" width="50"> </el-table-column>
            <el-table-column property="description" label="Nombre" width="180">
            </el-table-column>
            <el-table-column property="internal_id" label="Código" width="130">
            </el-table-column>
            <el-table-column property="brand" label="Marca" width="130">
                <!-- <template slot-scope="{ row }">
                    {{ row }}
                </template> -->
            </el-table-column>
            <!-- <el-table-column property="currency_type_id" label="Moneda" width="80">
                </el-table-column> -->
            <el-table-column label="Precio" width="130">
                <template slot-scope="{ row }">
                    {{ row.currency_type_symbol }} {{ row.sale_unit_price }}
                </template>
            </el-table-column>

            <el-table-column label="Pack" width="150">
                <template slot-scope="{ row }">
                    <br />
                    <small> {{ row.sets.join("-") }} </small>
                </template>
            </el-table-column>
            <el-table-column label="Stock">
                <template slot-scope="{ row }">
                    <!-- <button type="button" class="btn btn-xs btn-primary-pos" @click="clickWarehouseDetail(row)">
                            <i class="fa fa-search"></i>
                        </button> -->
                    <div v-if="config.product_only_location == true">
                        {{ row.stock }}
                    </div>
                    <div v-else>
                        <template
                            v-if="
                                typeUser == 'seller' && row.unit_type_id != 'ZZ'
                            "
                            >{{ row.stock }}</template
                        >
                        <template
                            v-else-if="
                                typeUser != 'seller' && row.unit_type_id != 'ZZ'
                            "
                        >
                            <button
                                type="button"
                                class="btn waves-effect waves-light btn-xs btn-info"
                                @click.prevent="clickWarehouseDetail(row)"
                            >
                                <i class="fa fa-search"></i>
                            </button>
                        </template>
                    </div>
                </template>
            </el-table-column>

            <el-table-column label="Historial ventas">
                <template slot-scope="{ row }">
                    <button
                        type="button"
                        class="btn btn-xs btn-primary-pos"
                        @click="clickHistorySales(row.item_id)"
                    >
                        <i class="fa fa-list"></i>
                    </button>
                </template>
            </el-table-column>

            <!-- <el-table-column label="Historial compras">
                    <template slot-scope="{row}">
                        <button type="button" class="btn btn-xs btn-primary-pos" @click="clickHistoryPurchases(row.item_id)"><i class="fas fa-cart-plus"></i></button>
                    </template>
                </el-table-column> -->
        </el-table>
    </div>
</template>

<script>
import Keypress from "vue-keypress";

export default {
    components: { Keypress },
    props: {
        typeUser: String,
        records: {
            type: Array,
            default: [],
            required: false
        },
        visibleTagsCustomer: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            currentIndex: 0,
            currentRow: null,
            config: {}
        };
    },
    created() {
        this.$http.get(`/configurations/record`).then(response => {
            this.config = response.data.data;
        });
    },
    methods: {
        handle13() {
            if (this.visibleTagsCustomer) {
                return false;
            }

            if (this.records.length == 1) {
                this.$emit("clickAddItem", this.records[0]);
            } else {
                if (this.currentRow) {
                    this.$emit("clickAddItem", this.currentRow);
                }
            }
        },
        handle40() {
            if (this.visibleTagsCustomer) {
                return;
            }
            this.currentIndex += 1;

            if (this.records[this.currentIndex]) {
                this.setCurrent(this.records[this.currentIndex]);
            } else {
                this.currentIndex = 0;
                this.setCurrent(this.records[0]);
            }
        },
        handle38() {
            if (this.visibleTagsCustomer) {
                return;
            }

            if (this.currentIndex == 0) {
                return;
            }
            this.currentIndex -= 1;
            this.setCurrent(this.records[this.currentIndex]);
        },
        setCurrent(row) {
            this.$refs.singleTable.setCurrentRow(row);
        },
        handleCurrentChange(val) {
            this.currentRow = val;
        },
        clickWarehouseDetail(id) {
            this.$emit("clickWarehouseDetail", id);
        },
        clickHistorySales(id) {
            this.$emit("clickHistorySales", id);
        },
        clickHistoryPurchases(id) {
            this.$emit("clickHistoryPurchases", id);
        },
        reset() {
            this.currentIndex = 0;
            this.setCurrent(this.records[this.currentIndex]);
        }
    }
};
</script>

<style></style>

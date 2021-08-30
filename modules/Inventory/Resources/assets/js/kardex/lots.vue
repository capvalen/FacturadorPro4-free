<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <div class="card mb-0">
            <div class="card-body">
                <data-table :resource="resource">
                    <tr slot="heading">
                        <th width="5%">#</th>
                        <th>Codigo</th>
                        <th>Nombre</th>
                        <th>Und</th>
                        <th class="text-center">Stock</th>
                        <th class="text-center">Lote</th>
                        <th class="text-center">Vcto</th>
                        <th class="text-center">Dias x vencer</th>
                    </tr>

                    <tr slot-scope="{ index, row }">
                        <td>{{ index + 1 }}</td>
                        <td>{{ row.code_item }}</td>
                        <td>{{ row.name_item }}</td>
                        <td>{{ row.und_item }}</td>
                        <!-- <td>{{ row.reference_payment }}</td> -->
                        <td class="text-center">{{ row.quantity }}</td>
                        <td class="text-center">{{ row.code }}</td>
                        <td class="text-center">{{ row.date_of_due }}</td>
                        <td class="text-center">{{ row.diff_days }}</td>
                    </tr>
                </data-table>
            </div>
        </div>
    </div>
</template>

<script>
import DataTable from "../../components/DataTableKardex.vue";

export default {
    components: { DataTable },
    data() {
        return {
            resource: "reports/kardex_lots",
            form: {},
            item_id: "",
            records: [],
            items: []
        };
    },
    created() {
        this.$eventHub.$on("emitItemID", item_id => {
            // console.log(item_id)
            this.item_id = item_id;
        });
    },
    methods: {}
};
</script>

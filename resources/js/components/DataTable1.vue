<template>
    <div>
        <div class="row">
            <div class="col-md-7">
            </div>
            <div class="col-md-5">
                <el-input placeholder="Buscar"
                          v-model="search.value"
                          class="input-with-select"
                          prefix-icon="el-icon-search"
                          @input="getRecords">
                    <el-select v-model="search.column" slot="prepend" placeholder="Select" @change="getRecords">
                        <el-option v-for="(label, key) in columns" :key="key" :value="key" :label="label"></el-option>
                    </el-select>
                </el-input>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <slot name="heading"></slot>
                </thead>
                <tbody>
                <slot v-for="(row, index) in records" :row="row" :index="customIndex(index)"></slot>
                </tbody>
            </table>
            <div>
                <el-pagination
                        @current-change="getRecords"
                        layout="total, prev, pager, next"
                        :total="pagination.total"
                        :current-page.sync="pagination.current_page"
                        :page-size="pagination.per_page">
                </el-pagination>
            </div>
        </div>
    </div>
</template>

<style>
    .input-with-select .el-select .el-input {
        width: 140px;
    }
    .input-with-select .el-select .el-input .el-input__inner {
        padding-left: 10px;
        padding-right: 10px;
    }
    .input-with-select .el-input-group__prepend {
        background-color: #fff;
    }
</style>
<script>
    import queryString from 'query-string'

    export default {
        props: {
            resource: String
        },
        data () {
            return {
                search: {
                    column: null,
                    value: null
                },
                columns: [],
                records: [],
                pagination: {}
            }
        },
        computed: {
        },
        created() {
            this.$eventHub.$on('reloadData', () => {
                this.getRecords()
            })
        },
        async mounted () {
            let column_resource = _.split(this.resource, '/')
            await this.$http.get(`/${_.head(column_resource)}/columns`).then((response) => {
                this.columns = response.data
                this.search.column = _.head(Object.keys(this.columns))
            });
            await this.getRecords()
        },
        methods: {
            customIndex(index) {
                return (this.pagination.per_page * (this.pagination.current_page - 1)) + index + 1
            },
            getRecords() {
                return this.$http.get(`/${this.resource}/records?${this.getQueryParameters()}`).then((response) => {
                    this.records = response.data.data
                    this.pagination = response.data.meta
                });
            },
            getQueryParameters() {
                return queryString.stringify({
                    page: this.pagination.current_page,
                    limit: this.limit,
                    ...this.search
                })
            },
        }
    }
</script>
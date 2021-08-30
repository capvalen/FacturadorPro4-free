<script>
    import {Line} from "vue-chartjs";
    import {lineChartOptions} from "./config";

    export default {
        extends: Line,
        props: ["data"],
        data() {
            return {
                options: lineChartOptions
            };
        },
        mounted() {
            this.renderChart(this.data, this.options);
        }
    };
</script>

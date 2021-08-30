<template>
    <div class="chart-line-container">
        <canvas ref="canvas"></canvas>
    </div>
</template>

<style>
    .chart-line-container {
        position: relative;
        height: 99%;
        width: 99%;
    }
</style>

<script>
    import Chart from 'chart.js';

    export default {
        props: ['allData'],
        data() {
            return {
                chart: null,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: false,
                    }
                }
            }
        },
        created() {
            this.title = 'Comprobantes';
        },
        mounted() {
        },
        watch: {
            allData() {
                this.createChart();
            }
        },
        methods: {
            createChart() {
                if (this.chart) {
                    this.chart.destroy();
                }
                this.chart = new Chart(this.$refs.canvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: this.allData.labels,
                        datasets: this.allData.datasets,
                    },
                    options: this.options,
                });
            }
        }
    }
</script>

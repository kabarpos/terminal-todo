<script setup>
import { ref } from "vue";
import VueApexCharts from "vue3-apexcharts";

const props = defineProps({
    data: {
        type: Object,
        required: true,
    },
});

const chartOptions = ref({
    chart: {
        type: "donut",
    },
    plotOptions: {
        pie: {
            donut: {
                size: "75%",
                labels: {
                    show: true,
                    total: {
                        show: true,
                        label: "Total Count",
                        formatter: () => props.data.totalCount,
                    },
                },
            },
        },
    },
    labels: ["Sales", "View Count", "Percentage"],
    colors: ["#FF9F43", "#90EE90", "#0B2447"],
    legend: {
        position: "bottom",
    },
    responsive: [
        {
            breakpoint: 480,
            options: {
                chart: {
                    width: 300,
                },
                legend: {
                    position: "bottom",
                },
            },
        },
    ],
});

const series = ref(props.data.segments.map((segment) => segment.percentage));
</script>

<template>
    <apexchart
        type="donut"
        :options="chartOptions"
        :series="series"
    ></apexchart>
</template>

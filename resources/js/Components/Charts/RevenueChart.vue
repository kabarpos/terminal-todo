<script setup>
import { ref, onMounted } from "vue";
import VueApexCharts from "vue3-apexcharts";

const props = defineProps({
    data: {
        type: Object,
        required: true,
    },
});

const chartOptions = ref({
    chart: {
        type: "bar",
        // height: 350,
        toolbar: {
            show: false,
        },
    },
    plotOptions: {
        bar: {
            horizontal: false,
            columnWidth: "55%",
            borderRadius: 4,
        },
    },
    dataLabels: {
        enabled: false,
    },
    stroke: {
        show: true,
        width: 2,
        colors: ["transparent"],
    },
    xaxis: {
        categories: props.data.months,
        axisBorder: {
            show: false,
        },
        axisTicks: {
            show: false,
        },
    },
    yaxis: {
        title: {
            text: "$ (thousands)",
        },
    },
    fill: {
        opacity: 1,
    },
    tooltip: {
        y: {
            formatter: function (val) {
                return "$ " + val.toLocaleString();
            },
        },
    },
    colors: ["#0B2447", "#90EE90"],
    legend: {
        position: "top",
        horizontalAlign: "right",
    },
});

const series = ref([
    {
        name: "Income",
        data: props.data.income,
    },
    {
        name: "Expenses",
        data: props.data.expenses,
    },
]);
</script>

<template>
    <apexchart :options="chartOptions" :series="series"></apexchart>
</template>

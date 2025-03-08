<template>
    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ title }}
            </h3>
            <div class="flex items-center space-x-2">
                <button
                    v-for="period in periods"
                    :key="period.value"
                    class="px-3 py-1 text-sm rounded-md"
                    :class="selectedPeriod === period.value
                        ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300'
                        : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700'"
                    @click="selectedPeriod = period.value"
                >
                    {{ period.label }}
                </button>
            </div>
        </div>

        <div class="relative" style="height: 300px">
            <Line
                v-if="chartData"
                :data="chartData"
                :options="chartOptions"
            />
            <div
                v-else
                class="absolute inset-0 flex items-center justify-center"
            >
                <p class="text-gray-500 dark:text-gray-400">
                    Tidak ada data untuk ditampilkan
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
} from 'chart.js'
import { Line } from 'vue-chartjs'
import { format, subDays, subWeeks, subMonths, parseISO } from 'date-fns'
import { id } from 'date-fns/locale'

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
)

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    data: {
        type: Array,
        required: true
    },
    dateField: {
        type: String,
        default: 'recorded_at'
    },
    valueField: {
        type: String,
        default: 'value'
    }
})

const selectedPeriod = ref('7d')

const periods = [
    { value: '7d', label: '7 Hari' },
    { value: '30d', label: '30 Hari' },
    { value: '90d', label: '90 Hari' }
]

const filteredData = computed(() => {
    const now = new Date()
    let cutoffDate

    switch (selectedPeriod.value) {
        case '7d':
            cutoffDate = subDays(now, 7)
            break
        case '30d':
            cutoffDate = subDays(now, 30)
            break
        case '90d':
            cutoffDate = subDays(now, 90)
            break
        default:
            cutoffDate = subDays(now, 7)
    }

    return props.data
        .filter(item => parseISO(item[props.dateField]) >= cutoffDate)
        .sort((a, b) => parseISO(a[props.dateField]) - parseISO(b[props.dateField]))
})

const chartData = computed(() => {
    if (!filteredData.value.length) return null

    const labels = filteredData.value.map(item => 
        format(parseISO(item[props.dateField]), 'dd MMM', { locale: id })
    )

    const values = filteredData.value.map(item => item[props.valueField])

    return {
        labels,
        datasets: [
            {
                label: props.title,
                data: values,
                borderColor: '#6366f1',
                backgroundColor: '#6366f1',
                tension: 0.4
            }
        ]
    }
})

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    interaction: {
        intersect: false,
        mode: 'index'
    },
    plugins: {
        legend: {
            display: false
        },
        tooltip: {
            enabled: true,
            mode: 'index',
            intersect: false,
            callbacks: {
                label: function(context) {
                    let label = context.dataset.label || ''
                    if (label) {
                        label += ': '
                    }
                    if (context.parsed.y !== null) {
                        label += new Intl.NumberFormat('id-ID').format(context.parsed.y)
                    }
                    return label
                }
            }
        }
    },
    scales: {
        x: {
            grid: {
                display: false
            }
        },
        y: {
            beginAtZero: true,
            ticks: {
                callback: function(value) {
                    return new Intl.NumberFormat('id-ID').format(value)
                }
            }
        }
    }
}
</script> 
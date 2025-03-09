<template>
    <Head title="Analytics" />

    <AuthenticatedLayout :auth="auth" title="Analytics">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Analytics
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Filter Section -->
                <div class="mb-6 p-6 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <InputLabel for="platform_id" value="Platform" />
                            <select
                                id="platform_id"
                                v-model="filters.platform_id"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                @change="filter"
                            >
                                <option value="">Semua Platform</option>
                                <option v-for="platform in platforms" :key="platform.id" :value="platform.id">
                                    {{ platform.name }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <InputLabel for="account_id" value="Akun" />
                            <select
                                id="account_id"
                                v-model="filters.account_id"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                @change="filter"
                            >
                                <option value="">Semua Akun</option>
                                <option v-for="account in accounts" :key="account.id" :value="account.id">
                                    {{ account.name }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <InputLabel for="date_range" value="Rentang Waktu" />
                            <select
                                id="date_range"
                                v-model="filters.date_range"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                @change="filter"
                            >
                                <option value="7">7 Hari Terakhir</option>
                                <option value="30">30 Hari Terakhir</option>
                                <option value="90">90 Hari Terakhir</option>
                                <option value="custom">Kustom</option>
                            </select>
                        </div>

                        <div v-if="filters.date_range === 'custom'" class="grid grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="start_date" value="Tanggal Mulai" />
                                <TextInput
                                    id="start_date"
                                    v-model="filters.start_date"
                                    type="date"
                                    class="mt-1 block w-full"
                                    @input="filter"
                                />
                            </div>
                            <div>
                                <InputLabel for="end_date" value="Tanggal Akhir" />
                                <TextInput
                                    id="end_date"
                                    v-model="filters.end_date"
                                    type="date"
                                    class="mt-1 block w-full"
                                    @input="filter"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Followers Growth Chart -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Pertumbuhan Followers</h3>
                        <Line
                            :chart-data="followersChartData"
                            :chart-options="chartOptions"
                        />
                    </div>

                    <!-- Engagement Rate Chart -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Engagement Rate</h3>
                        <Line
                            :chart-data="engagementChartData"
                            :chart-options="chartOptions"
                        />
                    </div>

                    <!-- Reach & Impressions Chart -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Reach & Impressions</h3>
                        <Line
                            :chart-data="reachChartData"
                            :chart-options="chartOptions"
                        />
                    </div>

                    <!-- Interactions Chart -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Interaksi</h3>
                        <Bar
                            :chart-data="interactionsChartData"
                            :chart-options="chartOptions"
                        />
                    </div>
                </div>

                <!-- Insights Section -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Insights & Rekomendasi</h3>
                    
                    <div class="space-y-4">
                        <div v-for="(insight, index) in insights" :key="index" class="p-4 rounded-lg" :class="getInsightClass(insight.type)">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <component :is="getInsightIcon(insight.type)" class="h-6 w-6" />
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium" :class="getInsightTextClass(insight.type)">
                                        {{ insight.title }}
                                    </h4>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        {{ insight.description }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import { Line, Bar } from 'vue-chartjs'
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    Title,
    Tooltip,
    Legend
} from 'chart.js'
import {
    ChartBarIcon,
    ArrowTrendingUpIcon,
    ExclamationTriangleIcon,
    LightBulbIcon
} from '@heroicons/vue/24/outline'

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    Title,
    Tooltip,
    Legend
)

const props = defineProps({
    platforms: {
        type: Array,
        required: true
    },
    accounts: {
        type: Array,
        required: true
    },
    analytics: {
        type: Object,
        required: true
    },
    insights: {
        type: Array,
        required: true
    },
    auth: {
        type: Object,
        required: true
    }
})

const filters = ref({
    platform_id: '',
    account_id: '',
    date_range: '30',
    start_date: '',
    end_date: ''
})

// Chart Data
const followersChartData = computed(() => ({
    labels: props.analytics.dates,
    datasets: [
        {
            label: 'Followers',
            data: props.analytics.followers,
            borderColor: '#3b82f6',
            tension: 0.1
        }
    ]
}))

const engagementChartData = computed(() => ({
    labels: props.analytics.dates,
    datasets: [
        {
            label: 'Engagement Rate (%)',
            data: props.analytics.engagement_rates,
            borderColor: '#10b981',
            tension: 0.1
        }
    ]
}))

const reachChartData = computed(() => ({
    labels: props.analytics.dates,
    datasets: [
        {
            label: 'Reach',
            data: props.analytics.reach,
            borderColor: '#8b5cf6',
            tension: 0.1
        },
        {
            label: 'Impressions',
            data: props.analytics.impressions,
            borderColor: '#6366f1',
            tension: 0.1
        }
    ]
}))

const interactionsChartData = computed(() => ({
    labels: props.analytics.dates,
    datasets: [
        {
            label: 'Likes',
            data: props.analytics.likes,
            backgroundColor: '#ef4444'
        },
        {
            label: 'Comments',
            data: props.analytics.comments,
            backgroundColor: '#f59e0b'
        },
        {
            label: 'Shares',
            data: props.analytics.shares,
            backgroundColor: '#3b82f6'
        }
    ]
}))

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'top'
        }
    }
}

// Insights styling
const getInsightClass = (type) => {
    const classes = {
        success: 'bg-green-50 dark:bg-green-900/20',
        warning: 'bg-yellow-50 dark:bg-yellow-900/20',
        danger: 'bg-red-50 dark:bg-red-900/20',
        info: 'bg-blue-50 dark:bg-blue-900/20'
    }
    return classes[type] || classes.info
}

const getInsightTextClass = (type) => {
    const classes = {
        success: 'text-green-800 dark:text-green-200',
        warning: 'text-yellow-800 dark:text-yellow-200',
        danger: 'text-red-800 dark:text-red-200',
        info: 'text-blue-800 dark:text-blue-200'
    }
    return classes[type] || classes.info
}

const getInsightIcon = (type) => {
    const icons = {
        success: ArrowTrendingUpIcon,
        warning: ExclamationTriangleIcon,
        danger: ExclamationTriangleIcon,
        info: LightBulbIcon
    }
    return icons[type] || LightBulbIcon
}

// Filter function
const debounce = (fn, delay) => {
    let timeoutId
    return (...args) => {
        clearTimeout(timeoutId)
        timeoutId = setTimeout(() => fn(...args), delay)
    }
}

const filter = debounce(() => {
    router.get(route('social-media.analytics'), filters.value, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    })
}, 300)
</script> 
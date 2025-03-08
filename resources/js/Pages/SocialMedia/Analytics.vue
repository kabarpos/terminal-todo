<template>
    <Head title="Analisis Media Sosial" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Analisis Media Sosial
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Filters -->
                <MetricFilter
                    :platforms="platforms"
                    :accounts="accounts"
                    :initial-filters="filters"
                    @update:filters="updateFilters"
                    class="mb-6"
                />

                <!-- Overview Cards -->
                <div class="grid grid-cols-1 gap-6 mb-6 sm:grid-cols-2 lg:grid-cols-4">
                    <MetricCard
                        title="Total Pengikut"
                        :value="totalFollowers"
                        :trend="followersTrend"
                        icon="UserGroupIcon"
                        color="blue"
                    />
                    <MetricCard
                        title="Rata-rata Engagement"
                        :value="avgEngagement"
                        :trend="engagementTrend"
                        unit="percentage"
                        icon="ChartBarIcon"
                        color="purple"
                    />
                    <MetricCard
                        title="Total Interaksi"
                        :value="totalInteractions"
                        :trend="interactionsTrend"
                        icon="ChatBubbleLeftIcon"
                        color="green"
                    />
                    <MetricCard
                        title="Total Jangkauan"
                        :value="totalReach"
                        :trend="reachTrend"
                        icon="GlobeAltIcon"
                        color="yellow"
                    />
                </div>

                <!-- Charts -->
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <MetricChart
                        title="Pertumbuhan Pengikut"
                        :data="followersData"
                    />
                    <MetricChart
                        title="Engagement Rate"
                        :data="engagementData"
                    />
                </div>

                <!-- Platform Performance -->
                <div class="mt-6">
                    <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">
                        Performa per Platform
                    </h3>
                    <div class="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Platform
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Pengikut
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Engagement Rate
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Total Interaksi
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Jangkauan
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="platform in platformPerformance" :key="platform.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img :src="platform.icon" :alt="platform.name" class="h-10 w-10 rounded-full">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ platform.name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                            {{ formatNumber(platform.followers) }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ formatTrend(platform.followersTrend) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                            {{ formatNumber(platform.engagementRate, '%') }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ formatTrend(platform.engagementTrend) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                            {{ formatNumber(platform.interactions) }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ formatTrend(platform.interactionsTrend) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                            {{ formatNumber(platform.reach) }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ formatTrend(platform.reachTrend) }}
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import MetricFilter from '@/Components/SocialMedia/MetricFilter.vue'
import MetricCard from '@/Components/SocialMedia/MetricCard.vue'
import MetricChart from '@/Components/SocialMedia/MetricChart.vue'
import {
    UserGroupIcon,
    ChartBarIcon,
    ChatBubbleLeftIcon,
    GlobeAltIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    platforms: {
        type: Array,
        required: true
    },
    accounts: {
        type: Array,
        required: true
    },
    metrics: {
        type: Object,
        required: true
    }
})

const filters = ref({
    platform_id: '',
    account_id: '',
    start_date: '',
    end_date: '',
    metric_type: '',
    group_by: 'weekly'
})

// Computed properties untuk overview cards
const totalFollowers = computed(() => props.metrics.followers.current)
const followersTrend = computed(() => props.metrics.followers.trend)
const avgEngagement = computed(() => props.metrics.engagement.current)
const engagementTrend = computed(() => props.metrics.engagement.trend)
const totalInteractions = computed(() => props.metrics.interactions.current)
const interactionsTrend = computed(() => props.metrics.interactions.trend)
const totalReach = computed(() => props.metrics.reach.current)
const reachTrend = computed(() => props.metrics.reach.trend)

// Data untuk grafik
const followersData = computed(() => props.metrics.followers.history)
const engagementData = computed(() => props.metrics.engagement.history)

// Data performa platform
const platformPerformance = computed(() => props.metrics.platforms)

// Methods
const updateFilters = (newFilters) => {
    filters.value = newFilters
}

const formatNumber = (value, suffix = '') => {
    return new Intl.NumberFormat('id-ID').format(value) + suffix
}

const formatTrend = (value) => {
    if (!value) return '-'
    const prefix = value > 0 ? '+' : ''
    return `${prefix}${value}%`
}
</script> 
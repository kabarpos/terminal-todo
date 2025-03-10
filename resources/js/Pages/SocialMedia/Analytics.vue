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
                    <!-- Menghapus card analytics sedang dalam pengembangan -->
                </div>

                <!-- Overview Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Followers Card -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                    Total Followers
                                </p>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ formatNumber(analytics?.total_followers || 0) }}
                                </h3>
                                <p class="mt-1 text-sm" :class="[
                                    analytics?.followers_growth > 0 
                                        ? 'text-green-600 dark:text-green-400' 
                                        : 'text-red-600 dark:text-red-400'
                                ]">
                                    <span v-if="analytics?.followers_growth > 0">+</span>
                                    {{ analytics?.followers_growth || 0 }}%
                                </p>
                            </div>
                            <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-full">
                                <UsersIcon class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                            </div>
                        </div>
                    </div>

                    <!-- Engagement Rate Card -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                    Rata-rata Engagement
                                </p>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ analytics?.avg_engagement?.toFixed(2) || '0.00' }}%
                                </h3>
                                <p class="mt-1 text-sm" :class="[
                                    analytics?.engagement_growth > 0 
                                        ? 'text-green-600 dark:text-green-400' 
                                        : 'text-red-600 dark:text-red-400'
                                ]">
                                    <span v-if="analytics?.engagement_growth > 0">+</span>
                                    {{ analytics?.engagement_growth || 0 }}%
                                </p>
                            </div>
                            <div class="p-3 bg-purple-100 dark:bg-purple-900/20 rounded-full">
                                <ChartBarIcon class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                            </div>
                        </div>
                    </div>

                    <!-- Reach Card -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                    Total Reach
                                </p>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ formatNumber(analytics?.total_reach || 0) }}
                                </h3>
                                <p class="mt-1 text-sm" :class="[
                                    analytics?.reach_growth > 0 
                                        ? 'text-green-600 dark:text-green-400' 
                                        : 'text-red-600 dark:text-red-400'
                                ]">
                                    <span v-if="analytics?.reach_growth > 0">+</span>
                                    {{ analytics?.reach_growth || 0 }}%
                                </p>
                            </div>
                            <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-full">
                                <GlobeAltIcon class="w-6 h-6 text-green-600 dark:text-green-400" />
                            </div>
                        </div>
                    </div>

                    <!-- Interactions Card -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                    Total Interaksi
                                </p>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ formatNumber(analytics?.total_interactions || 0) }}
                                </h3>
                                <p class="mt-1 text-sm" :class="[
                                    analytics?.interactions_growth > 0 
                                        ? 'text-green-600 dark:text-green-400' 
                                        : 'text-red-600 dark:text-red-400'
                                ]">
                                    <span v-if="analytics?.interactions_growth > 0">+</span>
                                    {{ analytics?.interactions_growth || 0 }}%
                                </p>
                            </div>
                            <div class="p-3 bg-orange-100 dark:bg-orange-900/20 rounded-full">
                                <HandRaisedIcon class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Stats -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Detail Interaksi</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Likes</p>
                                    <p class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                        {{ formatNumber(analytics?.total_likes || 0) }}
                                    </p>
                                </div>
                                <HeartIcon class="w-5 h-5 text-red-500" />
                            </div>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Comments</p>
                                    <p class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                        {{ formatNumber(analytics?.total_comments || 0) }}
                                    </p>
                                </div>
                                <ChatBubbleLeftIcon class="w-5 h-5 text-blue-500" />
                            </div>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Shares</p>
                                    <p class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                        {{ formatNumber(analytics?.total_shares || 0) }}
                                    </p>
                                </div>
                                <ShareIcon class="w-5 h-5 text-green-500" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Insights Section -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <div class="text-center py-8">
                        <LightBulbIcon class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-gray-100">
                            Insights Akan Segera Hadir
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Kami akan segera menghadirkan insights yang membantu Anda menganalisis performa media sosial Anda.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import {
    ChartBarIcon,
    LightBulbIcon,
    ArrowLeftIcon,
    UsersIcon,
    GlobeAltIcon,
    HandRaisedIcon,
    HeartIcon,
    ChatBubbleLeftIcon,
    ShareIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    platforms: {
        type: Array,
        required: true,
        default: () => []
    },
    accounts: {
        type: Array,
        required: true,
        default: () => []
    },
    analytics: {
        type: Object,
        required: true,
        default: () => ({
            total_followers: 0,
            followers_growth: 0,
            avg_engagement: 0,
            engagement_growth: 0,
            total_reach: 0,
            reach_growth: 0,
            total_interactions: 0,
            interactions_growth: 0,
            total_likes: 0,
            total_comments: 0,
            total_shares: 0
        })
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

// Format number helper
const formatNumber = (num) => {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M'
    }
    if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K'
    }
    return num.toString()
}

// Filter function
const debounce = (fn, delay) => {
    let timeoutId
    return (...args) => {
        clearTimeout(timeoutId)
        timeoutId = setTimeout(() => fn(...args), delay)
    }
}

const handleDateRangeChange = () => {
    if (filters.value.date_range === 'custom') {
        return;
    }

    filters.value.start_date = '';
    filters.value.end_date = '';

    filter();
}

const filter = debounce(() => {
    let params = { ...filters.value };
    
    if (params.date_range !== 'custom') {
        delete params.start_date;
        delete params.end_date;
    }

    router.get('/social-analytics', params, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
}, 300);
</script> 
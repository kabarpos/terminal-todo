<template>
    <Head title="Detail Data Metrik" />

    <AuthenticatedLayout :auth="auth" title="Detail Data Metrik">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Detail Data Metrik
                </h2>
                <Link
                    :href="route('metric-data.index')"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                >
                    <ArrowLeftIcon class="w-5 h-5 mr-2" />
                    Kembali
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6">
                        <!-- Informasi Akun -->
                        <div class="mb-8">
                            <div class="flex items-center mb-6">
                                <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full" 
                                    :class="getPlatformClass(props.metricData.account?.platform?.name?.toLowerCase())">
                                    <i :class="getPlatformIcon(props.metricData.account?.platform?.name?.toLowerCase())" class="text-lg"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        {{ props.metricData.account?.name || '-' }}
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ props.metricData.account?.platform?.name || '-' }} • {{ formatDate(props.metricData.date) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Metrik Cards -->
                        <div class="grid grid-cols-1 gap-6 mb-8 sm:grid-cols-2 lg:grid-cols-4">
                            <!-- Followers Card -->
                            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Followers</div>
                                <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ formatNumber(props.metricData.followers_count) }}
                                </div>
                            </div>

                            <!-- Engagement Rate Card -->
                            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Engagement Rate</div>
                                <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ props.metricData.engagement_rate }}%
                                </div>
                            </div>

                            <!-- Reach Card -->
                            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Reach</div>
                                <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ formatNumber(props.metricData.reach) }}
                                </div>
                            </div>

                            <!-- Total Interactions Card -->
                            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Interactions</div>
                                <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ formatNumber(getTotalInteractions()) }}
                                </div>
                            </div>
                        </div>

                        <!-- Detail Metrics -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                            <!-- Interactions Section -->
                            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Interaksi Detail</h3>
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <div class="flex items-center">
                                            <HeartIcon class="w-5 h-5 text-red-500 mr-2" />
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Likes</span>
                                        </div>
                                        <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                            {{ formatNumber(props.metricData.likes) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <div class="flex items-center">
                                            <ChatBubbleLeftIcon class="w-5 h-5 text-blue-500 mr-2" />
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Comments</span>
                                        </div>
                                        <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                            {{ formatNumber(props.metricData.comments) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <div class="flex items-center">
                                            <ShareIcon class="w-5 h-5 text-green-500 mr-2" />
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Shares</span>
                                        </div>
                                        <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                            {{ formatNumber(props.metricData.shares) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Reach & Impressions Section -->
                            <div class="p-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Jangkauan & Impresi</h3>
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <div class="flex items-center">
                                            <EyeIcon class="w-5 h-5 text-purple-500 mr-2" />
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Reach</span>
                                        </div>
                                        <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                            {{ formatNumber(props.metricData.reach) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <div class="flex items-center">
                                            <ChartBarIcon class="w-5 h-5 text-yellow-500 mr-2" />
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Impressions</span>
                                        </div>
                                        <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                            {{ formatNumber(props.metricData.impressions) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end gap-4 mt-6">
                            <Link
                                v-if="props.metricData?.id"
                                :href="`/metric-data/${props.metricData.id}/edit`"
                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                            >
                                <PencilSquareIcon class="w-4 h-4 mr-2" />
                                Edit Data
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { 
    ArrowLeftIcon, 
    PencilSquareIcon,
    HeartIcon,
    ChatBubbleLeftIcon,
    ShareIcon,
    EyeIcon,
    ChartBarIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    metricData: {
        type: Object,
        required: true
    },
    debug: {
        type: Object,
        default: () => ({})
    },
    auth: {
        type: Object,
        required: true
    },
    rawDebug: {
        type: Object,
        default: null
    }
})

const formatNumber = (number) => {
    return number ? number.toLocaleString() : '0'
}

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString('id-ID', { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    })
}

const getTotalInteractions = () => {
    const { likes = 0, comments = 0, shares = 0 } = props.metricData
    return likes + comments + shares
}

const getPlatformIcon = (platform) => {
    const icons = {
        instagram: 'fa-brands fa-instagram',
        facebook: 'fa-brands fa-facebook-f',
        twitter: 'fa-brands fa-twitter',
        tiktok: 'fa-brands fa-tiktok',
        youtube: 'fa-brands fa-youtube',
        linkedin: 'fa-brands fa-linkedin-in',
        pinterest: 'fa-brands fa-pinterest-p',
        default: 'fa-brands fa-globe'
    }
    return icons[platform] || icons.default
}

const getPlatformClass = (platform) => {
    const classes = {
        instagram: 'bg-gradient-to-r from-purple-500 to-pink-500 text-white',
        facebook: 'bg-blue-600 text-white',
        twitter: 'bg-blue-400 text-white',
        tiktok: 'bg-black text-white',
        youtube: 'bg-red-600 text-white',
        linkedin: 'bg-blue-700 text-white',
        pinterest: 'bg-red-700 text-white',
        default: 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400'
    }
    return classes[platform] || classes.default
}
</script> 
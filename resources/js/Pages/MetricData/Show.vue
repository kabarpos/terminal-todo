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
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800"
                >
                    <ArrowLeftIcon class="w-5 h-5 mr-2" />
                    Kembali
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Informasi Akun</h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Platform</label>
                                        <div class="mt-1 text-gray-900 dark:text-gray-100">
                                            {{ props.metricData.account?.platform?.name || '-' }}
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Akun</label>
                                        <div class="mt-1 text-gray-900 dark:text-gray-100">
                                            {{ props.metricData.account?.name || '-' }}
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal</label>
                                        <div class="mt-1 text-gray-900 dark:text-gray-100">
                                            {{ formatDate(props.metricData.date) }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Metrik Utama</h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Followers</label>
                                        <div class="mt-1 text-gray-900 dark:text-gray-100">
                                            {{ formatNumber(props.metricData.followers_count) }}
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Engagement Rate</label>
                                        <div class="mt-1 text-gray-900 dark:text-gray-100">
                                            {{ props.metricData.engagement_rate }}%
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reach</label>
                                        <div class="mt-1 text-gray-900 dark:text-gray-100">
                                            {{ formatNumber(props.metricData.reach) }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Interaksi</h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Likes</label>
                                        <div class="mt-1 text-gray-900 dark:text-gray-100">
                                            {{ formatNumber(props.metricData.likes) }}
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Comments</label>
                                        <div class="mt-1 text-gray-900 dark:text-gray-100">
                                            {{ formatNumber(props.metricData.comments) }}
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Shares</label>
                                        <div class="mt-1 text-gray-900 dark:text-gray-100">
                                            {{ formatNumber(props.metricData.shares) }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Jangkauan</h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Impressions</label>
                                        <div class="mt-1 text-gray-900 dark:text-gray-100">
                                            {{ formatNumber(props.metricData.impressions) }}
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total Interaksi</label>
                                        <div class="mt-1 text-gray-900 dark:text-gray-100">
                                            {{ formatNumber(getTotalInteractions()) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4 mt-6">
                            <Link
                                v-if="props.metricData?.id"
                                :href="`/metric-data/${props.metricData.id}/edit`"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                <PencilSquareIcon class="w-5 h-5 mr-2" />
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
import { ArrowLeftIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'

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
</script> 
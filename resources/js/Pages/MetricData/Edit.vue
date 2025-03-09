<template>
    <Head title="Edit Data Metrik" />

    <AuthenticatedLayout :auth="auth" title="Edit Data Metrik">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Edit Data Metrik
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
                    <form @submit.prevent="submit" class="p-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Platform & Akun</label>
                                <select
                                    v-model="form.social_account_id"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500"
                                    required
                                >
                                    <option value="">Pilih Akun</option>
                                    <option v-for="account in accounts" :key="account.id" :value="account.id">
                                        {{ account.platform.name }} - {{ account.name }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal</label>
                                <input
                                    type="date"
                                    v-model="form.date"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500"
                                    required
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Followers</label>
                                <input
                                    type="number"
                                    v-model="form.followers_count"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500"
                                    required
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Engagement Rate (%)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    v-model="form.engagement_rate"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500"
                                    required
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reach</label>
                                <input
                                    type="number"
                                    v-model="form.reach"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500"
                                    required
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Impressions</label>
                                <input
                                    type="number"
                                    v-model="form.impressions"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500"
                                    required
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Likes</label>
                                <input
                                    type="number"
                                    v-model="form.likes"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500"
                                    required
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Comments</label>
                                <input
                                    type="number"
                                    v-model="form.comments"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500"
                                    required
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Shares</label>
                                <input
                                    type="number"
                                    v-model="form.shares"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500"
                                    required
                                />
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4 mt-6">
                            <button
                                type="submit"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                :disabled="form.processing"
                            >
                                <SaveIcon v-if="!form.processing" class="w-5 h-5 mr-2" />
                                <SpinnerIcon v-else class="w-5 h-5 mr-2 animate-spin" />
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ArrowLeftIcon } from '@heroicons/vue/24/outline'
import SaveIcon from '@/Components/Icons/SaveIcon.vue'
import SpinnerIcon from '@/Components/Icons/SpinnerIcon.vue'

const props = defineProps({
    metricData: {
        type: Object,
        required: true
    },
    accounts: {
        type: Array,
        required: true
    },
    auth: {
        type: Object,
        required: true
    }
})

const form = useForm({
    social_account_id: props.metricData.social_account_id,
    date: props.metricData.date,
    followers_count: props.metricData.followers_count,
    engagement_rate: props.metricData.engagement_rate,
    reach: props.metricData.reach,
    impressions: props.metricData.impressions,
    likes: props.metricData.likes,
    comments: props.metricData.comments,
    shares: props.metricData.shares
})

const submit = () => {
    form.put(route('metric-data.update', props.metricData.id))
}
</script> 
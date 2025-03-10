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
                    <form @submit.prevent="submit" class="p-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <InputLabel for="social_account_id" value="Platform & Akun" />
                                <select
                                    id="social_account_id"
                                    v-model="form.social_account_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                >
                                    <option value="">Pilih Akun</option>
                                    <option v-for="account in accounts" :key="account.id" :value="account.id">
                                        {{ account.platform.name }} - {{ account.name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.social_account_id" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="date" value="Tanggal" />
                                <TextInput
                                    id="date"
                                    type="date"
                                    v-model="form.date"
                                    class="mt-1 block w-full"
                                />
                                <InputError :message="form.errors.date" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="followers_count" value="Jumlah Followers *" />
                                <TextInput
                                    id="followers_count"
                                    type="number"
                                    v-model="form.followers_count"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.followers_count" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="engagement_rate" value="Engagement Rate (%)" />
                                <TextInput
                                    id="engagement_rate"
                                    type="number"
                                    step="0.01"
                                    v-model="form.engagement_rate"
                                    class="mt-1 block w-full"
                                />
                                <InputError :message="form.errors.engagement_rate" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="reach" value="Reach" />
                                <TextInput
                                    id="reach"
                                    type="number"
                                    v-model="form.reach"
                                    class="mt-1 block w-full"
                                />
                                <InputError :message="form.errors.reach" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="impressions" value="Impressions" />
                                <TextInput
                                    id="impressions"
                                    type="number"
                                    v-model="form.impressions"
                                    class="mt-1 block w-full"
                                />
                                <InputError :message="form.errors.impressions" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="likes" value="Likes" />
                                <TextInput
                                    id="likes"
                                    type="number"
                                    v-model="form.likes"
                                    class="mt-1 block w-full"
                                />
                                <InputError :message="form.errors.likes" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="comments" value="Comments" />
                                <TextInput
                                    id="comments"
                                    type="number"
                                    v-model="form.comments"
                                    class="mt-1 block w-full"
                                />
                                <InputError :message="form.errors.comments" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="shares" value="Shares" />
                                <TextInput
                                    id="shares"
                                    type="number"
                                    v-model="form.shares"
                                    class="mt-1 block w-full"
                                />
                                <InputError :message="form.errors.shares" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4 mt-6">
                            <Link
                                :href="route('metric-data.index')"
                                class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-600 focus:bg-gray-400 dark:focus:bg-gray-600 active:bg-gray-500 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:focus:ring-gray-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                            >
                                Batal
                            </Link>

                            <button
                                type="submit"
                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                            >
                                <SaveIcon v-if="!form.processing" class="w-4 h-4 mr-2" />
                                <SpinnerIcon v-else class="w-4 h-4 mr-2 animate-spin" />
                                {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
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
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
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

// Format tanggal untuk input date
const formatDateForInput = (dateString) => {
    if (!dateString) return ''
    const date = new Date(dateString)
    return date.toISOString().split('T')[0]
}

const form = useForm({
    social_account_id: props.metricData.social_account_id?.toString() || '',
    date: formatDateForInput(props.metricData.date),
    followers_count: props.metricData.followers_count?.toString() || '',
    engagement_rate: props.metricData.engagement_rate?.toString() || '',
    reach: props.metricData.reach?.toString() || '',
    impressions: props.metricData.impressions?.toString() || '',
    likes: props.metricData.likes?.toString() || '',
    comments: props.metricData.comments?.toString() || '',
    shares: props.metricData.shares?.toString() || ''
})

const submit = () => {
    form.put(route('metric-data.update', props.metricData.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Optional: Tambahkan notifikasi sukses jika diperlukan
        },
        onError: () => {
            // Optional: Tangani error jika diperlukan
        }
    })
}
</script> 
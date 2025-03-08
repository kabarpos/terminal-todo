<template>
    <Head title="Data Metrik" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Data Metrik
                </h2>
                <Link
                    :href="route('metric-data.create')"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition"
                >
                    Input Data Baru
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Filter Section -->
                <div class="mb-6 p-6 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                    <form @submit.prevent="filter" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <InputLabel for="account_id" value="Akun" />
                            <select
                                id="account_id"
                                v-model="filters.account_id"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                            >
                                <option value="">Semua Akun</option>
                                <option v-for="account in accounts" :key="account.id" :value="account.id">
                                    {{ account.platform.name }} - {{ account.name }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <InputLabel for="metric_id" value="Metrik" />
                            <select
                                id="metric_id"
                                v-model="filters.metric_id"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                            >
                                <option value="">Semua Metrik</option>
                                <option v-for="metric in metrics" :key="metric.id" :value="metric.id">
                                    {{ metric.name }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <InputLabel for="date_range" value="Periode" />
                            <select
                                id="date_range"
                                v-model="filters.date_range"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                            >
                                <option value="all">Semua Waktu</option>
                                <option value="today">Hari Ini</option>
                                <option value="week">Minggu Ini</option>
                                <option value="month">Bulan Ini</option>
                                <option value="year">Tahun Ini</option>
                            </select>
                        </div>

                        <div class="flex items-end">
                            <PrimaryButton type="submit" class="w-full">
                                Filter Data
                            </PrimaryButton>
                        </div>
                    </form>
                </div>

                <!-- Data Table -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Platform
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Akun
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Metrik
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Nilai
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Tanggal
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="data in metricData.data" :key="data.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ data.account.platform.name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ data.account.name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ data.metric.name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ data.value }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ formatDate(data.recorded_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex space-x-2">
                                        <Link :href="route('metric-data.edit', data.id)" 
                                              class="text-blue-600 hover:text-blue-800">
                                            Edit
                                        </Link>
                                        <button @click="deleteData(data.id)" 
                                                class="text-red-600 hover:text-red-800">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="px-6 py-4 bg-white dark:bg-gray-800 border-t dark:border-gray-700">
                        <Pagination :links="metricData.links" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
    metricData: {
        type: Object,
        required: true
    },
    accounts: {
        type: Array,
        required: true
    },
    metrics: {
        type: Array,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    }
})

const filters = ref({
    account_id: props.filters.account_id || '',
    metric_id: props.filters.metric_id || '',
    date_range: props.filters.date_range || 'all'
})

const filter = () => {
    router.get(route('metric-data.index'), filters.value, {
        preserveState: true,
        preserveScroll: true
    })
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

const deleteData = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        router.delete(route('metric-data.destroy', id))
    }
}
</script> 
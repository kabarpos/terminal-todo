<template>
    <Head title="Laporan Media Sosial" />

    <AuthenticatedLayout :auth="auth" title="Laporan Media Sosial">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Laporan Medsos
                </h2>
                <div class="flex gap-2">
                    <button
                        @click="exportToExcel"
                        class="inline-flex items-center px-2 sm:px-3 py-1 sm:py-2 bg-green-600 hover:bg-green-700 text-white text-xs sm:text-sm font-medium rounded-lg transition-colors duration-200"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="hidden md:inline ml-2">Export Excel</span>
                    </button>
                    <Link
                        :href="route('social-media-reports.create')"
                        class="inline-flex items-center px-2 sm:px-3 py-1 sm:py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-xs sm:text-sm font-medium rounded-lg transition-colors duration-200"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        <span class="hidden md:inline ml-2">Tambah Laporan</span>
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <!-- Filter Section -->
                        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Bulan
                                </label>
                                <input
                                    v-model="filters.month"
                                    type="month"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Jenis Konten
                                </label>
                                <select
                                    v-model="filters.category_id"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm"
                                >
                                    <option value="">Semua Jenis</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Tim
                                </label>
                                <select
                                    v-model="filters.user_id"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm"
                                >
                                    <option value="">Semua Tim</option>
                                    <option v-for="user in users" :key="user.id" :value="user.id">
                                        {{ user.name }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Summary Section -->
                        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <h3 class="text-lg font-medium mb-2">Ringkasan Laporan</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Konten</p>
                                    <p class="text-2xl font-semibold">{{ filteredReports.length }}</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Konten per Kategori</p>
                                    <div class="mt-2 space-y-1">
                                        <div v-for="category in categoryStats" :key="category.id" class="flex items-center justify-between text-sm">
                                            <span>{{ category.name }}</span>
                                            <span class="font-medium">{{ category.count }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Konten per Tim</p>
                                    <div class="mt-2 space-y-1">
                                        <div v-for="user in userStats" :key="user.id" class="flex items-center justify-between text-sm">
                                            <span>{{ user.name }}</span>
                                            <span class="font-medium">{{ user.count }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Tanggal Posting
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Jenis Konten
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Link URL
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Tim
                                        </th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <tr v-for="report in filteredReports" :key="report.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ formatDate(report.posting_date) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-4 w-4 rounded-full mr-2" :style="{ backgroundColor: report.category.color }"></div>
                                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                                    {{ report.category.name }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <a :href="report.url" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                {{ report.url }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ report.user.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link
                                                :href="route('social-media-reports.edit', report.id)"
                                                class="inline-flex items-center px-2 sm:px-3 py-1 sm:py-1.5 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-xs sm:text-sm font-medium rounded-lg transition-colors duration-200 mr-2"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                @click="confirmReportDeletion(report)"
                                                class="inline-flex items-center px-2 sm:px-3 py-1 sm:py-1.5 bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white text-xs sm:text-sm font-medium rounded-lg transition-colors duration-200"
                                            >
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal :show="confirmingReportDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Hapus Laporan
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Apakah Anda yakin ingin menghapus laporan ini?
                </p>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal" class="mr-3">
                        Batal
                    </SecondaryButton>

                    <PrimaryButton
                        variant="danger"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteReport"
                    >
                        {{ form.processing ? 'Menghapus...' : 'Hapus' }}
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    reports: {
        type: Array,
        required: true
    },
    categories: {
        type: Array,
        required: true
    },
    users: {
        type: Array,
        required: true
    },
    auth: {
        type: Object,
        required: true
    }
});

const filters = ref({
    month: new Date().toISOString().slice(0, 7), // Default ke bulan sekarang (format: YYYY-MM)
    category_id: '',
    user_id: ''
});

const selectedReport = ref(null);
const confirmingReportDeletion = ref(false);
const form = useForm({});

// Computed property untuk filtered reports
const filteredReports = computed(() => {
    return props.reports.filter(report => {
        const monthMatch = !filters.value.month || report.posting_date.startsWith(filters.value.month);
        const categoryMatch = !filters.value.category_id || report.category_id === filters.value.category_id;
        const userMatch = !filters.value.user_id || report.user_id === filters.value.user_id;
        return monthMatch && categoryMatch && userMatch;
    });
});

// Statistik kategori
const categoryStats = computed(() => {
    const stats = {};
    filteredReports.value.forEach(report => {
        if (!stats[report.category.id]) {
            stats[report.category.id] = {
                id: report.category.id,
                name: report.category.name,
                count: 0
            };
        }
        stats[report.category.id].count++;
    });
    return Object.values(stats).sort((a, b) => b.count - a.count);
});

// Statistik user
const userStats = computed(() => {
    const stats = {};
    filteredReports.value.forEach(report => {
        if (!stats[report.user.id]) {
            stats[report.user.id] = {
                id: report.user.id,
                name: report.user.name,
                count: 0
            };
        }
        stats[report.user.id].count++;
    });
    return Object.values(stats).sort((a, b) => b.count - a.count);
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const confirmReportDeletion = (report) => {
    selectedReport.value = report;
    confirmingReportDeletion.value = true;
};

const closeModal = () => {
    confirmingReportDeletion.value = false;
    selectedReport.value = null;
};

const deleteReport = () => {
    if (selectedReport.value) {
        form.delete(route('social-media-reports.destroy', selectedReport.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
            onError: () => {
                // Handle error
            }
        });
    }
};

const exportToExcel = () => {
    window.location.href = route('social-media-reports.export', filters.value);
};
</script> 
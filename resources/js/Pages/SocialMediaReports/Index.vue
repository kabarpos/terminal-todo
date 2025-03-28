<template>
    <Head title="Posting Reports" />

    <AuthenticatedLayout :auth="auth" title="Posting Reports">
        <template #header>
            <div class="w-full">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg md:text-xl font-semibold text-[var(--text-primary)] truncate">
                        Posting Reports
                    </h2>
                    
                    <div class="flex items-center gap-2">
                        <button
                            @click="exportToExcel"
                            class="inline-flex items-center px-3 py-2 md:px-4 md:py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
                        >
                            <ArrowDownTrayIcon class="h-5 w-5" />
                            <span class="hidden md:inline ml-2">Export Excel</span>
                        </button>

                        <Link
                            :href="route('social-media-reports.create')"
                            class="inline-flex items-center px-3 py-2 md:px-4 md:py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
                        >
                            <span class="flex items-center">
                                <PlusIcon class="h-5 w-5" />
                                <span class="hidden md:inline ml-2">Create New Report</span>
                            </span>
                        </Link>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <!-- Filter Section -->
                        <div class="mb-6">
                            <div class="flex flex-col sm:flex-row gap-4 sm:items-center sm:justify-between">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    Filter Data
                                </h3>
                                <button
                                    @click="resetFilters"
                                    class="inline-flex items-center px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200"
                                >
                                    <span class="mr-2">Reset Filter</span>
                                    <XMarkIcon class="w-4 h-4" />
                                </button>
                            </div>
                            
                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <!-- Filter Tanggal -->
                                <div class="flex flex-col gap-2">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Range Tanggal
                                    </label>
                                    <div class="flex items-center gap-2">
                                        <input
                                            type="date"
                                            v-model="filters.startDate"
                                            class="flex-1 px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        />
                                        <span class="text-gray-500">s/d</span>
                                        <input
                                            type="date"
                                            v-model="filters.endDate"
                                            class="flex-1 px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        />
                                    </div>
                                </div>
                                
                                <!-- Filter Bulan -->
                                <div class="flex flex-col gap-2">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Bulan
                                    </label>
                                    <select
                                        v-model="filters.month"
                                        class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                        <option value="">Semua Bulan</option>
                                        <option v-for="(month, index) in months" :key="index" :value="index + 1">
                                            {{ month }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Platform</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Posting Date</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created By</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="report in filteredReports" :key="report.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-4 py-4">
                                            <div class="text-sm text-gray-900 dark:text-gray-100 break-words max-w-xs">{{ report.title }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400 max-w-xs">
                                                <a :href="report.url" target="_blank" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 break-all line-clamp-1 hover:line-clamp-none" :title="report.url">{{ report.url }}</a>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="text-sm text-gray-900 dark:text-gray-100 break-words">{{ report.category?.name }}</div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="text-sm text-gray-900 dark:text-gray-100 break-words">{{ report.platform?.name }}</div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="text-sm text-gray-900 dark:text-gray-100 break-words">{{ formatDate(report.posting_date) }}</div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="text-sm text-gray-900 dark:text-gray-100 break-words">{{ report.creator?.name }}</div>
                                        </td>
                                        <td class="px-4 py-4 text-right">
                                            <div class="inline-flex rounded-md">
                                                <Link
                                                    :href="route('social-media-reports.edit', report.id)"
                                                    class="inline-flex items-center px-2 sm:px-3 py-1 sm:py-1.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-xs sm:text-sm font-medium rounded-l-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
                                                >
                                                    <PencilSquareIcon class="w-4 h-4 mr-1" />
                                                    Edit
                                                </Link>
                                                <button
                                                    @click="confirmReportDeletion(report)"
                                                    class="inline-flex items-center px-2 sm:px-3 py-1 sm:py-1.5 bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white text-xs sm:text-sm font-medium rounded-r-lg border-l-0 transition-colors duration-200"
                                                >
                                                    <TrashIcon class="w-4 h-4 mr-1" />
                                                    Delete
                                                </button>
                                            </div>
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
                    Delete Report
                </h2>

                <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                    Are you sure you want to delete this report? This action cannot be undone.
                </p>

                <div class="mt-6 flex justify-end gap-4">
                    <SecondaryButton @click="closeModal">
                        Cancel
                    </SecondaryButton>

                    <DangerButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteReport"
                    >
                        Delete Report
                    </DangerButton>
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
import { format } from 'date-fns';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { 
    ArrowDownTrayIcon, 
    PlusIcon, 
    PencilSquareIcon, 
    TrashIcon,
    XMarkIcon
} from '@heroicons/vue/24/outline';
import * as XLSX from 'xlsx';

const props = defineProps({
    reports: {
        type: Array,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
    platforms: {
        type: Array,
        required: true,
    },
});

const confirmingReportDeletion = ref(false);
const reportToDelete = ref(null);
const form = useForm({});

// Data untuk filter
const filters = ref({
    startDate: '',
    endDate: '',
    month: ''
});

// Daftar bulan dalam bahasa Indonesia
const months = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

// Computed property untuk memfilter reports
const filteredReports = computed(() => {
    let filtered = [...props.reports];

    // Filter berdasarkan tanggal
    if (filters.value.startDate && filters.value.endDate) {
        filtered = filtered.filter(report => {
            const postingDate = new Date(report.posting_date);
            const start = new Date(filters.value.startDate);
            const end = new Date(filters.value.endDate);
            return postingDate >= start && postingDate <= end;
        });
    }

    // Filter berdasarkan bulan
    if (filters.value.month) {
        filtered = filtered.filter(report => {
            const postingDate = new Date(report.posting_date);
            return postingDate.getMonth() + 1 === parseInt(filters.value.month);
        });
    }

    return filtered;
});

const formatDate = (date) => {
    return date ? format(new Date(date), 'dd MMM yyyy') : '';
};

const resetFilters = () => {
    filters.value = {
        startDate: '',
        endDate: '',
        month: ''
    };
};

const exportToExcel = () => {
    // Menggunakan data yang sudah difilter
    const exportData = filteredReports.value.map(report => ({
        'Title': report.title,
        'URL': report.url,
        'Category': report.category?.name,
        'Platform': report.platform?.name,
        'Posting Date': formatDate(report.posting_date),
        'Created By': report.creator?.name
    }));

    const worksheet = XLSX.utils.json_to_sheet(exportData);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Social Media Reports');
    
    // Nama file termasuk range tanggal jika ada filter
    let fileName = 'social-media-reports';
    if (filters.value.startDate && filters.value.endDate) {
        fileName += `-${filters.value.startDate}-to-${filters.value.endDate}`;
    } else if (filters.value.month) {
        fileName += `-${months[parseInt(filters.value.month) - 1]}-${new Date().getFullYear()}`;
    } else {
        fileName += `-${format(new Date(), 'yyyy-MM-dd')}`;
    }
    fileName += '.xlsx';

    XLSX.writeFile(workbook, fileName);
};

const confirmReportDeletion = (report) => {
    reportToDelete.value = report;
    confirmingReportDeletion.value = true;
};

const closeModal = () => {
    confirmingReportDeletion.value = false;
    reportToDelete.value = null;
};

const deleteReport = () => {
    if (reportToDelete.value) {
        form.delete(route('social-media-reports.destroy', reportToDelete.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
            onError: () => closeModal(),
        });
    }
};
</script> 
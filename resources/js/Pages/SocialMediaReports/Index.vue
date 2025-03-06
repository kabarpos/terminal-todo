<template>
    <Head title="Social Media Reports" />

    <AuthenticatedLayout :auth="auth" title="Social Media Reports Management">
        <template #header>
            <div class="w-full">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg md:text-xl font-semibold text-[var(--text-primary)] truncate">
                        Social Media Reports
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
                                    <tr v-for="report in reports" :key="report.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-4 py-4">
                                            <div class="text-sm text-gray-900 dark:text-gray-100 break-words">{{ report.title }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400 break-words">
                                                <a :href="report.url" target="_blank" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">{{ report.url }}</a>
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
                                        <td class="px-4 py-4 text-right space-x-2">
                                            <Link
                                                :href="route('social-media-reports.edit', report.id)"
                                                class="inline-flex items-center px-2 sm:px-3 py-1 sm:py-1.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-xs sm:text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
                                            >
                                                <PencilSquareIcon class="w-4 h-4 mr-1" />
                                                Edit
                                            </Link>
                                            <button
                                                @click="confirmReportDeletion(report)"
                                                class="inline-flex items-center px-2 sm:px-3 py-1 sm:py-1.5 bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white text-xs sm:text-sm font-medium rounded-lg transition-colors duration-200"
                                            >
                                                <TrashIcon class="w-4 h-4 mr-1" />
                                                Delete
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
import { ref } from 'vue';
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
    TrashIcon 
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

const formatDate = (date) => {
    return date ? format(new Date(date), 'dd MMM yyyy') : '';
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

const exportToExcel = () => {
    const exportData = props.reports.map(report => ({
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
    
    const fileName = `social-media-reports-${format(new Date(), 'yyyy-MM-dd')}.xlsx`;
    XLSX.writeFile(workbook, fileName);
};
</script> 
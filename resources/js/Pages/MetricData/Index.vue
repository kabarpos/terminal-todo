<template>
    <Head title="Data Metrik" />

    <AuthenticatedLayout :auth="auth" title="Data Metrik">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Data Metrik
            </h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Filter & Actions Section -->
                <div class="mb-6">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <!-- Filter Dropdowns -->
                        <div class="flex flex-wrap items-center gap-4">
                            <div class="w-full sm:w-64">
                                <select
                                    v-model="filters.account_id"
                                    class="block w-full text-sm border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                    @change="filter"
                                >
                                    <option value="">Semua Akun</option>
                                    <option v-for="account in accounts" :key="account.id" :value="account.id">
                                        {{ account.platform.name }} - {{ account.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="w-full sm:w-48">
                                <select
                                    v-model="filters.date_range"
                                    class="block w-full text-sm border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                    @change="filter"
                                >
                                    <option value="7">7 Hari Terakhir</option>
                                    <option value="30">30 Hari Terakhir</option>
                                    <option value="90">90 Hari Terakhir</option>
                                    <option value="custom">Kustom</option>
                                </select>
                            </div>

                            <template v-if="filters.date_range === 'custom'">
                                <div class="w-40">
                                    <input
                                        type="date"
                                        v-model="filters.start_date"
                                        class="block w-full text-sm border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                        @change="filter"
                                    />
                                </div>
                                <div class="w-40">
                                    <input
                                        type="date"
                                        v-model="filters.end_date"
                                        class="block w-full text-sm border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                        @change="filter"
                                    />
                                </div>
                            </template>
                        </div>

                        <!-- Action Buttons -->
                        <div class="grid grid-cols-2 gap-2 sm:flex sm:items-center">
                            <div class="col-span-2 sm:col-auto grid grid-cols-3 gap-2">
                                <button
                                    @click="downloadTemplate"
                                    class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                                >
                                    <DocumentArrowDownIcon class="w-5 h-5 sm:mr-1.5" />
                                    <span class="hidden sm:inline">Template</span>
                                </button>
                                
                                <button
                                    @click="showImportModal = true"
                                    class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                                >
                                    <ArrowUpTrayIcon class="w-5 h-5 sm:mr-1.5" />
                                    <span class="hidden sm:inline">Import</span>
                                </button>

                                <button
                                    @click="exportData"
                                    :disabled="exporting"
                                    class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                                >
                                    <SpinnerIcon v-if="exporting" class="w-5 h-5 sm:mr-1.5 animate-spin" />
                                    <ArrowDownTrayIcon v-else class="w-5 h-5 sm:mr-1.5" />
                                    <span class="hidden sm:inline">{{ exporting ? 'Mengekspor...' : 'Export' }}</span>
                                </button>
                            </div>

                            <Link
                                :href="route('metric-data.create')"
                                class="col-span-2 sm:col-auto inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 border border-transparent rounded-lg transition-colors duration-200"
                            >
                                <PlusIcon class="w-5 h-5 sm:mr-1.5" />
                                <span class="inline sm:inline">Tambah Data</span>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Overview Cards -->
                <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="p-4 bg-white rounded-lg shadow-sm dark:bg-gray-800">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Followers</div>
                        <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ totalFollowers }}</div>
                        <div class="flex items-center mt-1 text-sm">
                            <span :class="[
                                followerGrowth >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'
                            ]">
                                {{ followerGrowth >= 0 ? '+' : '' }}{{ followerGrowth }}%
                            </span>
                            <span class="ml-1 text-gray-500 dark:text-gray-400">vs bulan lalu</span>
                        </div>
                    </div>

                    <div class="p-4 bg-white rounded-lg shadow-sm dark:bg-gray-800">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Engagement Rate</div>
                        <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ averageEngagement }}%</div>
                        <div class="flex items-center mt-1 text-sm">
                            <span :class="[
                                engagementGrowth >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'
                            ]">
                                {{ engagementGrowth >= 0 ? '+' : '' }}{{ engagementGrowth }}%
                            </span>
                            <span class="ml-1 text-gray-500 dark:text-gray-400">vs bulan lalu</span>
                        </div>
                    </div>

                    <div class="p-4 bg-white rounded-lg shadow-sm dark:bg-gray-800">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Reach</div>
                        <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ totalReach }}</div>
                        <div class="flex items-center mt-1 text-sm">
                            <span :class="[
                                reachGrowth >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'
                            ]">
                                {{ reachGrowth >= 0 ? '+' : '' }}{{ reachGrowth }}%
                            </span>
                            <span class="ml-1 text-gray-500 dark:text-gray-400">vs bulan lalu</span>
                        </div>
                    </div>

                    <div class="p-4 bg-white rounded-lg shadow-sm dark:bg-gray-800">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Interactions</div>
                        <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ totalInteractions }}</div>
                        <div class="flex items-center mt-1 text-sm">
                            <span :class="[
                                interactionsGrowth >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'
                            ]">
                                {{ interactionsGrowth >= 0 ? '+' : '' }}{{ interactionsGrowth }}%
                            </span>
                            <span class="ml-1 text-gray-500 dark:text-gray-400">vs bulan lalu</span>
                        </div>
                    </div>
                </div>

                <!-- Metrics Table -->
                <div class="overflow-hidden bg-white rounded-lg shadow-sm dark:bg-gray-800">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                        Tanggal
                                    </th>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                        Akun
                                    </th>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                        Followers
                                    </th>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                        Engagement
                                    </th>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                        Reach
                                    </th>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                        Likes
                                    </th>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                        Comments
                                    </th>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                        Shares
                                    </th>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase dark:text-gray-300">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="metric in metrics.data" :key="metric.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap dark:text-gray-100">
                                        {{ formatDate(metric.date) }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-full" :class="getPlatformClass(metric.account?.platform?.name?.toLowerCase() || '')">
                                                <i :class="getPlatformIcon(metric.account?.platform?.name?.toLowerCase() || '')" class="text-lg"></i>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ metric.account?.name }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ metric.account?.platform?.name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap dark:text-gray-100">
                                        {{ metric.followers_count }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap dark:text-gray-100">
                                        {{ metric.engagement_rate }}%
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap dark:text-gray-100">
                                        {{ metric.reach }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap dark:text-gray-100">
                                        {{ metric.likes }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap dark:text-gray-100">
                                        {{ metric.comments }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap dark:text-gray-100">
                                        {{ metric.shares }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right whitespace-nowrap">
                                        <div class="inline-flex rounded-md">
                                            <Link
                                                :href="`/metric-data/${metric.id}`"
                                                class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-l-md dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                                            >
                                                <EyeIcon class="w-4 h-4" />
                                            </Link>
                                            <Link
                                                :href="`/metric-data/${metric.id}/edit`"
                                                class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-700 bg-white border-t border-b border-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                                            >
                                                <PencilSquareIcon class="w-4 h-4" />
                                            </Link>
                                            <button
                                                @click="confirmMetricDeletion(metric)"
                                                class="inline-flex items-center px-2 py-1 text-xs font-medium text-white bg-red-600 border border-transparent rounded-r-md hover:bg-red-700"
                                            >
                                                <TrashIcon class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-4 py-3 bg-white border-t dark:bg-gray-800 dark:border-gray-700">
                        <Pagination :links="metrics.links" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal :show="confirmingMetricDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Hapus Data Metrik
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Apakah Anda yakin ingin menghapus data metrik ini?
                </p>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton 
                        @click="closeModal"
                        class="mr-3"
                    >
                        Batal
                    </SecondaryButton>

                    <DangerButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteMetric"
                    >
                        {{ form.processing ? 'Menghapus...' : 'Hapus' }}
                    </DangerButton>
                </div>
            </div>
        </Modal>

        <!-- Import Modal -->
        <Modal :show="showImportModal" @close="showImportModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Import Data Metrik
                </h2>

                <form @submit.prevent="submitImport" class="mt-6">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            File Excel/CSV
                        </label>
                        <input
                            type="file"
                            ref="fileInput"
                            accept=".xlsx,.xls,.csv"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500"
                            required
                        />
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Unggah file Excel/CSV dengan format yang sesuai. 
                            <a href="#" @click.prevent="downloadTemplate" class="text-blue-600 dark:text-blue-400 hover:underline">
                                Download template
                            </a>
                        </p>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <button
                            type="button"
                            class="inline-flex items-center px-4 py-2 mr-4 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                            @click="showImportModal = false"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            :disabled="importing"
                        >
                            <SpinnerIcon v-if="importing" class="w-5 h-5 mr-2 animate-spin" />
                            <ArrowUpTrayIcon v-else class="w-5 h-5 mr-2" />
                            {{ importing ? 'Mengimport...' : 'Import' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import { format } from 'date-fns'
import { id } from 'date-fns/locale'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import DangerButton from '@/Components/DangerButton.vue'
import Pagination from '@/Components/Pagination.vue'
import Modal from '@/Components/Modal.vue'
import SpinnerIcon from '@/Components/Icons/SpinnerIcon.vue'
import { PlusIcon, PencilSquareIcon, TrashIcon, EyeIcon, FunnelIcon, ArrowUpTrayIcon, DocumentArrowDownIcon, ArrowDownTrayIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    metrics: {
        type: Object,
        required: true
    },
    accounts: {
        type: Array,
        required: true
    },
    stats: {
        type: Object,
        required: true
    },
    auth: {
        type: Object,
        required: true
    }
})

const filters = ref({
    account_id: '',
    date_range: '30',
    start_date: '',
    end_date: ''
})

// Stats
const totalFollowers = computed(() => props.stats.total_followers)
const followerGrowth = computed(() => props.stats.follower_growth)
const averageEngagement = computed(() => props.stats.average_engagement)
const engagementGrowth = computed(() => props.stats.engagement_growth)
const totalReach = computed(() => props.stats.total_reach)
const reachGrowth = computed(() => props.stats.reach_growth)
const totalInteractions = computed(() => props.stats.total_interactions)
const interactionsGrowth = computed(() => props.stats.interactions_growth)

// Debounce function
const debounce = (fn, delay) => {
    let timeoutId
    return (...args) => {
        clearTimeout(timeoutId)
        timeoutId = setTimeout(() => fn(...args), delay)
    }
}

const filter = debounce(() => {
    router.get(route('metric-data.index'), filters.value, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    })
}, 300)

const selectedMetric = ref(null)
const confirmingMetricDeletion = ref(false)
const processing = ref(false)
const form = useForm({})
const showImportModal = ref(false)
const importing = ref(false)
const exporting = ref(false)
const downloading = ref(false)
const fileInput = ref(null)

const confirmMetricDeletion = (metric) => {
    selectedMetric.value = metric
    confirmingMetricDeletion.value = true
}

const closeModal = () => {
    confirmingMetricDeletion.value = false
    selectedMetric.value = null
    processing.value = false
}

const deleteMetric = () => {
    if (selectedMetric.value) {
        form.delete(route('metric-data.destroy', selectedMetric.value.id), {
            preserveScroll: true,
            preserveState: true,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            onBefore: () => {
                processing.value = true;
            },
            onSuccess: () => {
                closeModal();
                router.reload();
            },
            onError: (error) => {
                if (error.response?.status === 419) {
                    // Refresh halaman jika CSRF token expired
                    window.location.reload();
                } else {
                    alert('Terjadi kesalahan saat menghapus data');
                }
            },
            onFinish: () => {
                processing.value = false;
            }
        });
    }
}

const formatDate = (date) => {
    return format(new Date(date), 'dd MMM yyyy', { locale: id })
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

const submitImport = async () => {
    if (!fileInput.value.files.length) {
        alert('Silakan pilih file terlebih dahulu');
        return;
    }
    
    importing.value = true;
    const formData = new FormData();
    formData.append('file', fileInput.value.files[0]);
    
    try {
        const response = await axios.post(route('metric-data.import'), formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        showImportModal.value = false;
        alert(response.data.message || 'Data berhasil diimport');
        router.reload();
    } catch (error) {
        console.error('Error importing data:', error);
        let errorMessage = 'Terjadi kesalahan saat mengimport data.';
        
        if (error.response && error.response.data) {
            if (error.response.data.message) {
                errorMessage = error.response.data.message;
            } else if (error.response.data.error) {
                errorMessage = error.response.data.error;
            } else if (error.response.data.errors) {
                // Handle validation errors
                const errors = error.response.data.errors;
                errorMessage = Object.values(errors).flat().join('\n');
            }
        }
        
        alert(errorMessage);
    } finally {
        importing.value = false;
    }
}

const exportData = () => {
    // Menampilkan status loading
    exporting.value = true;
    
    // Siapkan parameter filter
    const filters = cleanFilters();
    
    // Buat URL untuk export
    let url = route('metric-data.export');
    
    // Tambahkan query parameters
    const queryParams = new URLSearchParams();
    
    if (filters.account_id) {
        queryParams.append('account_id', filters.account_id);
    }
    
    if (filters.date_range) {
        queryParams.append('date_range', filters.date_range);
    }
    
    if (filters.start_date) {
        queryParams.append('start_date', filters.start_date);
    }
    
    if (filters.end_date) {
        queryParams.append('end_date', filters.end_date);
    }
    
    const queryString = queryParams.toString();
    if (queryString) {
        url = `${url}?${queryString}`;
    }
    
    console.log('Exporting data from URL:', url);
    
    // Gunakan teknik frame terpisah untuk download agar tidak mengganggu aplikasi utama
    const downloadFrame = document.createElement('iframe');
    downloadFrame.style.display = 'none';
    document.body.appendChild(downloadFrame);
    
    // Saat frame selesai loading, hilangkan status loading
    downloadFrame.onload = () => {
        setTimeout(() => {
            exporting.value = false;
            // Cek apakah ada error dengan melihat konten frame
            try {
                const frameContent = downloadFrame.contentDocument.body.textContent;
                if (frameContent && frameContent.includes('error')) {
                    // Tampilkan pesan error jika ada
                    alert('Terjadi kesalahan saat export data: ' + frameContent);
                }
            } catch (e) {
                // Jika tidak bisa mengakses konten frame, anggap berhasil
                console.log('Export selesai');
            }
        }, 2000); // Tunggu 2 detik sebelum menghilangkan status loading
    };
    
    // Jika terjadi error, hilangkan status loading
    downloadFrame.onerror = () => {
        exporting.value = false;
        alert('Terjadi kesalahan saat export data');
    };
    
    // Lakukan download
    downloadFrame.src = url;
}

const cleanFilters = () => {
    // Hilangkan filter kosong agar tidak mengganggu query
    const cleanedFilters = {};
    
    if (filters.value.account_id) {
        cleanedFilters.account_id = filters.value.account_id;
    }
    
    cleanedFilters.date_range = filters.value.date_range;
    
    if (filters.value.date_range === 'custom') {
        if (filters.value.start_date) {
            cleanedFilters.start_date = filters.value.start_date;
        }
        if (filters.value.end_date) {
            cleanedFilters.end_date = filters.value.end_date;
        }
    }
    
    console.log('Exporting with filters:', cleanedFilters);
    return cleanedFilters;
}

const downloadTemplate = () => {
    downloading.value = true;
    
    // Buat URL untuk download template
    const url = route('metric-data.template');
    console.log('Downloading template from URL:', url);
    
    // Gunakan window.location.href langsung untuk kompatibilitas maksimal
    window.location.href = url;
    
    // Atur timeout untuk mengubah status downloading
    setTimeout(() => {
        downloading.value = false;
    }, 1000);
}
</script> 
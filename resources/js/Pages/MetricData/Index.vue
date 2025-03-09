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
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                        <!-- Filter Dropdowns -->
                        <div class="flex flex-wrap items-center gap-4">
                            <div class="w-64">
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

                            <div class="w-48">
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
                        <div class="flex items-center gap-2">
                            <div class="flex items-center gap-2">
                                <Link
                                    :href="route('metric-data.template')"
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                                >
                                    <DocumentArrowDownIcon class="w-5 h-5 mr-1.5" />
                                    Template
                                </Link>
                                
                                <button
                                    @click="showImportModal = true"
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                                >
                                    <ArrowUpTrayIcon class="w-5 h-5 mr-1.5" />
                                    Import
                                </button>

                                <Link
                                    :href="route('metric-data.export', filters)"
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                                >
                                    <ArrowDownTrayIcon class="w-5 h-5 mr-1.5" />
                                    Export
                                </Link>
                            </div>

                            <Link
                                :href="route('metric-data.create')"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700"
                            >
                                <PlusIcon class="w-5 h-5 mr-1.5" />
                                Tambah Data
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
                                            <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-full" :class="getPlatformClass(metric.account.platform.name.toLowerCase())">
                                                <i :class="getPlatformIcon(metric.account.platform.name.toLowerCase())" class="text-lg"></i>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ metric.account.name }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ metric.account.platform.name }}
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

                    <PrimaryButton
                        variant="danger"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteMetric"
                    >
                        {{ form.processing ? 'Menghapus...' : 'Hapus' }}
                    </PrimaryButton>
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
const form = useForm({})
const showImportModal = ref(false)
const importing = ref(false)
const fileInput = ref(null)

const confirmMetricDeletion = (metric) => {
    selectedMetric.value = metric
    confirmingMetricDeletion.value = true
}

const closeModal = () => {
    confirmingMetricDeletion.value = false
    selectedMetric.value = null
}

const deleteMetric = () => {
    if (selectedMetric.value) {
        form.delete(route('metric-data.destroy', selectedMetric.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
            onError: () => {
                // Handle error
            }
        })
    }
}

const formatDate = (date) => {
    return format(new Date(date), 'dd MMM yyyy', { locale: id })
}

const getPlatformIcon = (platform) => {
    const icons = {
        instagram: 'fab fa-instagram',
        facebook: 'fab fa-facebook',
        twitter: 'fab fa-twitter',
        tiktok: 'fab fa-tiktok',
        youtube: 'fab fa-youtube',
        linkedin: 'fab fa-linkedin',
        pinterest: 'fab fa-pinterest',
        default: 'fab fa-globe'
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
    importing.value = true
    const formData = new FormData()
    formData.append('file', fileInput.value.files[0])

    try {
        const response = await axios.post(route('metric-data.import'), formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })

        if (response.data.success) {
            showImportModal.value = false
            router.get(route('metric-data.index'), filters.value, {
                preserveState: true,
                preserveScroll: true,
                replace: true
            })
        } else {
            // Handle error
        }
    } catch (error) {
        // Handle error
    } finally {
        importing.value = false
    }
}
</script> 
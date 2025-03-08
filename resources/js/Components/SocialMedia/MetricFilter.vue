<template>
    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Platform Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Platform
                </label>
                <select
                    v-model="filters.platform_id"
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    @change="applyFilters"
                >
                    <option value="">Semua Platform</option>
                    <option v-for="platform in platforms" :key="platform.id" :value="platform.id">
                        {{ platform.name }}
                    </option>
                </select>
            </div>

            <!-- Account Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Akun
                </label>
                <select
                    v-model="filters.account_id"
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    @change="applyFilters"
                >
                    <option value="">Semua Akun</option>
                    <option v-for="account in filteredAccounts" :key="account.id" :value="account.id">
                        {{ account.name }}
                    </option>
                </select>
            </div>

            <!-- Date Range Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Tanggal Mulai
                </label>
                <input
                    type="date"
                    v-model="filters.start_date"
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    @change="applyFilters"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Tanggal Akhir
                </label>
                <input
                    type="date"
                    v-model="filters.end_date"
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    @change="applyFilters"
                >
            </div>
        </div>

        <!-- Additional Filters -->
        <div v-if="showAdvancedFilters" class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <!-- Metric Type Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Jenis Metrik
                </label>
                <select
                    v-model="filters.metric_type"
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    @change="applyFilters"
                >
                    <option value="">Semua Jenis</option>
                    <option v-for="type in metricTypes" :key="type.key" :value="type.key">
                        {{ type.name }}
                    </option>
                </select>
            </div>

            <!-- Group By Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Kelompokkan
                </label>
                <select
                    v-model="filters.group_by"
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    @change="applyFilters"
                >
                    <option value="daily">Harian</option>
                    <option value="weekly">Mingguan</option>
                    <option value="monthly">Bulanan</option>
                </select>
            </div>

            <!-- Sort By Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Urutkan
                </label>
                <select
                    v-model="filters.sort_by"
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    @change="applyFilters"
                >
                    <option value="date_desc">Tanggal Terbaru</option>
                    <option value="date_asc">Tanggal Terlama</option>
                    <option value="value_desc">Nilai Tertinggi</option>
                    <option value="value_asc">Nilai Terendah</option>
                </select>
            </div>
        </div>

        <!-- Toggle Advanced Filters -->
        <div class="flex justify-end">
            <button
                type="button"
                class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200"
                @click="showAdvancedFilters = !showAdvancedFilters"
            >
                {{ showAdvancedFilters ? 'Sembunyikan Filter Lanjutan' : 'Tampilkan Filter Lanjutan' }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { debounce } from 'lodash'

const props = defineProps({
    platforms: {
        type: Array,
        default: () => []
    },
    accounts: {
        type: Array,
        default: () => []
    },
    initialFilters: {
        type: Object,
        default: () => ({})
    }
})

const emit = defineEmits(['update:filters'])

const showAdvancedFilters = ref(false)

const filters = ref({
    platform_id: '',
    account_id: '',
    start_date: '',
    end_date: '',
    metric_type: '',
    group_by: 'weekly',
    sort_by: 'date_desc',
    ...props.initialFilters
})

const filteredAccounts = computed(() => {
    if (!filters.value.platform_id) return props.accounts
    return props.accounts.filter(account => account.platform_id === filters.value.platform_id)
})

const metricTypes = [
    { key: 'engagement', name: 'Engagement' },
    { key: 'reach', name: 'Jangkauan' },
    { key: 'followers', name: 'Pengikut' },
    { key: 'interactions', name: 'Interaksi' }
]

const applyFilters = debounce(() => {
    emit('update:filters', filters.value)
}, 300)
</script> 
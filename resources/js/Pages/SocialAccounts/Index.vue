<template>
    <Head title="Akun Media Sosial" />

    <AuthenticatedLayout :auth="auth" title="Akun Media Sosial">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Akun Media Sosial
                </h2>
                <Link
                    :href="route('social-accounts.create')"
                    class="inline-flex items-center px-3 py-2 md:px-4 md:py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
                >
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        <span class="hidden md:inline ml-2">Tambah Akun</span>
                    </span>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Filter Section -->
                <div class="mb-6 p-6 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <InputLabel for="search" value="Cari" />
                            <TextInput
                                id="search"
                                v-model="filters.search"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="Cari nama atau username..."
                                @input="filter"
                            />
                        </div>

                        <div>
                            <InputLabel for="platform_id" value="Platform" />
                            <select
                                id="platform_id"
                                v-model="filters.platform_id"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                @change="filter"
                            >
                                <option value="">Semua Platform</option>
                                <option v-for="platform in platforms" :key="platform.id" :value="platform.id">
                                    {{ platform.name }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <InputLabel for="status" value="Status" />
                            <select
                                id="status"
                                v-model="filters.status"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                @change="filter"
                            >
                                <option value="">Semua Status</option>
                                <option value="active">Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Accounts Table -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Platform
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Nama Akun
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Username
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="account in accounts.data" :key="account.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-full" :class="getPlatformClass(account.platform.name.toLowerCase())">
                                                <i :class="getPlatformIcon(account.platform.name.toLowerCase())" class="text-xl"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ account.platform.name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ account.name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ account.username }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="[
                                            'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                            account.is_active 
                                                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                                : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                        ]">
                                            {{ account.is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="inline-flex rounded-md">
                                            <Link
                                                :href="route('metric-data.show', account.id)"
                                                class="inline-flex items-center px-2 sm:px-3 py-1 sm:py-1.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-xs sm:text-sm font-medium rounded-l-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
                                            >
                                                <ChartBarIcon class="w-4 h-4 mr-1" />
                                                Metrik
                                            </Link>
                                            <Link
                                                :href="route('social-accounts.edit', account.id)"
                                                class="inline-flex items-center px-2 sm:px-3 py-1 sm:py-1.5 bg-white dark:bg-gray-800 border-t border-b border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-xs sm:text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
                                            >
                                                <PencilSquareIcon class="w-4 h-4 mr-1" />
                                                Edit
                                            </Link>
                                            <button
                                                @click="confirmAccountDeletion(account)"
                                                class="inline-flex items-center px-2 sm:px-3 py-1 sm:py-1.5 bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white text-xs sm:text-sm font-medium rounded-r-lg border-l-0 transition-colors duration-200"
                                            >
                                                <TrashIcon class="w-4 h-4 mr-1" />
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 bg-white dark:bg-gray-800 border-t dark:border-gray-700">
                        <Pagination :links="accounts.links" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal :show="confirmingAccountDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Hapus Akun
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Apakah Anda yakin ingin menghapus akun ini? Semua data metrik terkait akan ikut terhapus.
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
                        @click="deleteAccount"
                    >
                        {{ form.processing ? 'Menghapus...' : 'Hapus Akun' }}
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import Pagination from '@/Components/Pagination.vue'
import Modal from '@/Components/Modal.vue'
import { PencilSquareIcon, TrashIcon, ChartBarIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    accounts: {
        type: Object,
        required: true
    },
    platforms: {
        type: Array,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    }
})

const filters = ref({
    search: props.filters.search || '',
    platform_id: props.filters.platform_id || '',
    status: props.filters.status || ''
})

// Debounce function
const debounce = (fn, delay) => {
    let timeoutId
    return (...args) => {
        clearTimeout(timeoutId)
        timeoutId = setTimeout(() => fn(...args), delay)
    }
}

const filter = debounce(() => {
    router.get(route('social-accounts.index'), filters.value, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    })
}, 300) // 300ms delay

const selectedAccount = ref(null)
const confirmingAccountDeletion = ref(false)
const form = useForm({})

const confirmAccountDeletion = (account) => {
    selectedAccount.value = account
    confirmingAccountDeletion.value = true
}

const closeModal = () => {
    confirmingAccountDeletion.value = false
    selectedAccount.value = null
}

const deleteAccount = () => {
    if (selectedAccount.value) {
        form.delete(route('social-accounts.destroy', selectedAccount.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
            onError: () => {
                // Handle error
            }
        })
    }
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
        // Tambahkan platform lain sesuai kebutuhan
        default: 'fa-brands fa-globe' // Icon default jika platform tidak ditemukan
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
        // Tambahkan platform lain sesuai kebutuhan
        default: 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400'
    }
    return classes[platform] || classes.default
}
</script> 
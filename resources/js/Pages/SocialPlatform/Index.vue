<template>
    <Head title="Platform Media Sosial" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Platform Media Sosial
                </h2>
                <Link
                    :href="route('social-platforms.create')"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition"
                >
                    Tambah Platform
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Nama Platform
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Jumlah Akun
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="platform in platforms" :key="platform.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img v-if="platform.icon" :src="platform.icon" class="h-10 w-10 rounded-full" :alt="platform.name">
                                            <div v-else class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                {{ platform.name[0] }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ platform.name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="[
                                        'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                        platform.is_active 
                                            ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'
                                            : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100'
                                    ]">
                                        {{ platform.is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ platform.accounts_count }} akun
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-3">
                                        <Link :href="route('social-platforms.edit', platform.id)"
                                              class="text-blue-600 hover:text-blue-900 dark:hover:text-blue-400">
                                            Edit
                                        </Link>
                                        <button @click="toggleStatus(platform)"
                                                class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200">
                                            {{ platform.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                        <button @click="deletePlatform(platform.id)"
                                                class="text-red-600 hover:text-red-900 dark:hover:text-red-400">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    platforms: {
        type: Array,
        required: true
    }
})

const toggleStatus = (platform) => {
    if (confirm(`Apakah Anda yakin ingin ${platform.is_active ? 'menonaktifkan' : 'mengaktifkan'} platform ini?`)) {
        router.put(route('social-platforms.toggle-status', platform.id))
    }
}

const deletePlatform = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus platform ini? Semua akun dan data terkait akan ikut terhapus.')) {
        router.delete(route('social-platforms.destroy', id))
    }
}
</script> 
<template>
    <Head title="Platform" />

    <AuthenticatedLayout :auth="auth" title="Platform">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Platform
                </h2>
                <Link
                    :href="route('platforms.create')"
                    class="inline-flex items-center px-3 py-2 md:px-4 md:py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
                >
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        <span class="hidden md:inline ml-2">Tambah Platform</span>
                    </span>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <!-- Search and Filter Section -->
                        <div class="mb-4">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Cari platform..."
                                class="w-full px-4 py-2 text-sm border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-400 focus:ring-primary-500 dark:focus:ring-primary-400"
                            >
                        </div>

                        <!-- Platforms Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Platform
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Deskripsi
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Total Task
                                        </th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <tr v-for="platform in filteredPlatforms" :key="platform.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center text-gray-500 dark:text-gray-400">
                                                    <i :class="['text-2xl fa-brands', `fa-${platform.icon}`]"></i>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {{ platform.name }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                {{ platform.description || '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                :class="{
                                                    'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': platform.is_active,
                                                    'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': !platform.is_active
                                                }"
                                            >
                                                {{ platform.is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ platform.tasks_count }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="inline-flex rounded-md">
                                                <Link
                                                    :href="route('platforms.edit', platform.id)"
                                                    class="inline-flex items-center px-2 sm:px-3 py-1 sm:py-1.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-xs sm:text-sm font-medium rounded-l-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
                                                >
                                                    <PencilSquareIcon class="w-4 h-4 mr-1" />
                                                    Edit
                                                </Link>
                                                <button
                                                    @click="confirmPlatformDeletion(platform)"
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
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal :show="confirmingPlatformDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Hapus Platform
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Apakah Anda yakin ingin menghapus platform ini?
                </p>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton 
                        @click="closeModal"
                        variant="outline"
                        type="button"
                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                    >
                        Batal
                    </SecondaryButton>

                    <PrimaryButton
                        variant="danger"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deletePlatform"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-medium rounded-lg transition-colors duration-200 disabled:opacity-25"
                    >
                        {{ form.processing ? 'Menghapus...' : 'Hapus Platform' }}
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
import { PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    platforms: {
        type: Array,
        required: true
    },
    auth: {
        type: Object,
        required: true
    }
});

const search = ref('');
const selectedPlatform = ref(null);
const confirmingPlatformDeletion = ref(false);
const form = useForm({});

// Computed property untuk filtered platforms
const filteredPlatforms = computed(() => {
    if (!search.value) return props.platforms;
    
    const searchTerm = search.value.toLowerCase();
    return props.platforms.filter(platform => 
        platform.name.toLowerCase().includes(searchTerm) ||
        platform.description?.toLowerCase().includes(searchTerm)
    );
});

const confirmPlatformDeletion = (platform) => {
    selectedPlatform.value = platform;
    confirmingPlatformDeletion.value = true;
};

const closeModal = () => {
    confirmingPlatformDeletion.value = false;
    selectedPlatform.value = null;
};

const deletePlatform = () => {
    if (selectedPlatform.value) {
        form.delete(route('platforms.destroy', selectedPlatform.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
            onError: () => {
                // Handle error
            }
        });
    }
};
</script> 
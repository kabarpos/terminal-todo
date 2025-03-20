<template>
    <Head title="Media Trash" />

    <AuthenticatedLayout :auth="auth" title="Media Trash">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Media Trash
                </h2>
                <Link
                    href="/media"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-colors duration-200"
                >
                    <ArrowUturnLeftIcon class="w-5 h-5 mr-1" />
                    Kembali ke Media
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Alert Message -->
                <div v-if="$page.props.flash.message" class="mb-6">
                    <Alert
                        :type="$page.props.flash.type || 'success'"
                        :message="$page.props.flash.message"
                        class="mb-6"
                    />
                </div>

                <!-- Media Content -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Empty State -->
                        <div v-if="media.data.length === 0" class="text-center py-12">
                            <TrashIcon class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Sampah kosong</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Tidak ada file media yang dihapus.
                            </p>
                        </div>

                        <!-- Media Grid -->
                        <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                            <div v-for="item in media.data" :key="item.id" class="relative group">
                                <!-- Preview Card -->
                                <div class="relative aspect-w-10 aspect-h-7 overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-700">
                                    <!-- Image Preview -->
                                    <img v-if="item.type === 'image'" 
                                        :src="item.url" 
                                        :alt="item.name"
                                        class="object-cover"
                                    />
                                    
                                    <!-- Document Preview -->
                                    <div v-else-if="item.type === 'document'" 
                                        class="flex items-center justify-center"
                                    >
                                        <DocumentIcon class="h-16 w-16 text-gray-400" />
                                    </div>

                                    <!-- Other File Types -->
                                    <div v-else 
                                        class="flex items-center justify-center"
                                    >
                                        <DocumentIcon class="h-16 w-16 text-gray-400" />
                                    </div>

                                    <!-- Hover Overlay -->
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-opacity flex items-center justify-center opacity-0 group-hover:opacity-100">
                                        <div class="flex space-x-2">
                                            <button v-if="can.restore" @click.stop="restoreFile(item)" class="p-2 text-white bg-green-600/50 rounded-lg hover:bg-green-600/75">
                                                <ArrowUturnUpIcon class="w-5 h-5" />
                                            </button>
                                            <button v-if="can.force_delete" @click.stop="confirmForceDelete(item)" class="p-2 text-white bg-red-600/50 rounded-lg hover:bg-red-600/75">
                                                <TrashIcon class="w-5 h-5" />
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- File Info -->
                                <div class="mt-2">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                                {{ item.name }}
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ formatFileSize(item.size) }}
                                            </p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <img :src="item.uploader.avatar" :alt="item.uploader.name" class="w-6 h-6 rounded-full">
                                        </div>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Dihapus pada {{ formatDate(item.deleted_at) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div v-if="media.data.length > 0" class="mt-6">
                            <Pagination :links="media.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Force Delete Confirmation Modal -->
        <Modal :show="confirmingForceDelete" @close="closeDeleteModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-[var(--text-primary)]">
                    Hapus Permanen Media
                </h2>

                <p class="mt-3 text-sm text-[var(--text-secondary)]">
                    <strong class="text-red-600 dark:text-red-400">PERHATIAN:</strong> Media yang dihapus secara permanen tidak dapat dipulihkan. File akan dihapus dari storage dan database.
                </p>

                <div class="mt-6 flex justify-end gap-4">
                    <SecondaryButton 
                        @click="closeDeleteModal"
                        variant="outline"
                        type="button"
                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                    >
                        Batal
                    </SecondaryButton>

                    <PrimaryButton
                        variant="danger"
                        :class="{ 'opacity-25': processing }"
                        :disabled="processing"
                        @click="forceDeleteMedia"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-medium rounded-lg transition-colors duration-200 disabled:opacity-25"
                    >
                        {{ processing ? 'Menghapus...' : 'Hapus Permanen' }}
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Modal from '@/Components/Modal.vue'
import LoadingSpinner from '@/Components/LoadingSpinner.vue'
import Pagination from '@/Components/Pagination.vue'
import Alert from '@/Components/Alert.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { 
    DocumentIcon,
    TrashIcon,
    ArrowUturnLeftIcon,
    ArrowUturnUpIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    media: Object,
    can: Object,
    auth: {
        type: Object,
        required: true
    }
})

const selectedMedia = ref(null)
const confirmingForceDelete = ref(false)
const processing = ref(false)

const restoreFile = (file) => {
    processing.value = true
    router.post(`/media/restore/${file.id}`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            processing.value = false
        },
        onError: () => {
            processing.value = false
        }
    })
}

const confirmForceDelete = (file) => {
    selectedMedia.value = file
    confirmingForceDelete.value = true
}

const closeDeleteModal = () => {
    confirmingForceDelete.value = false
    selectedMedia.value = null
    processing.value = false
}

const forceDeleteMedia = () => {
    if (selectedMedia.value) {
        processing.value = true
        router.delete(`/media/force-delete/${selectedMedia.value.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                closeDeleteModal()
            },
            onError: () => {
                processing.value = false
            },
            onFinish: () => {
                processing.value = false
            }
        })
    }
}

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes'
    const k = 1024
    const sizes = ['Bytes', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}
</script> 
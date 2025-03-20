<template>
    <Head title="Media Library" />

    <AuthenticatedLayout :auth="auth" title="Media Library">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Media Library
                </h2>
                <div class="flex items-center space-x-2">
                    <Link
                        v-if="can.create"
                        href="/media/trash"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-colors duration-200"
                    >
                        <TrashIcon class="w-5 h-5 mr-1" />
                        Trash
                    </Link>
                </div>
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

                <!-- Actions and Filters -->
                <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <div class="flex flex-col space-y-4">
                        <!-- Upload Button -->
                        <div v-if="can.create" class="flex justify-end">
                            <label 
                                for="fileInput"
                                class="inline-flex items-center px-3 py-2 md:px-4 md:py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 cursor-pointer"
                                :class="{ 'opacity-50 cursor-not-allowed': uploading }"
                            >
                                <CloudArrowUpIcon class="w-5 h-5 mr-2" />
                                Upload Files
                            </label>
                            <input
                                id="fileInput"
                                type="file"
                                multiple
                                class="hidden"
                                @change="handleFileUpload"
                                :disabled="uploading"
                            />
                        </div>

                        <!-- Filters -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            <div class="flex-1">
                                <TextInput
                                    v-model="filters.search"
                                    type="search"
                                    class="w-full"
                                    placeholder="Cari media..."
                                    @input="applyFilters"
                                />
                            </div>
                            <div class="w-full sm:w-48">
                                <select 
                                    v-model="filters.type" 
                                    @change="applyFilters" 
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">Semua Tipe</option>
                                    <option value="image">Gambar</option>
                                    <option value="document">Dokumen</option>
                                    <option value="spreadsheet">Spreadsheet</option>
                                    <option value="presentation">Presentasi</option>
                                    <option value="video">Video</option>
                                    <option value="audio">Audio</option>
                                    <option value="other">Lainnya</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Media Content -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Loading State -->
                        <div v-if="uploading" class="flex items-center justify-center py-12">
                            <LoadingSpinner />
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Uploading files...</span>
                        </div>

                        <!-- Empty State -->
                        <div v-else-if="media.data.length === 0" class="text-center py-12">
                            <DocumentIcon class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No media files</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Get started by uploading your first file.
                            </p>
                        </div>

                        <!-- Media Grid -->
                        <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                            <div v-for="item in filteredMedia" :key="item.id" class="relative group">
                                <!-- Preview Card -->
                                <div class="relative aspect-w-10 aspect-h-7 overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-700">
                                    <!-- Image Preview -->
                                    <img v-if="item.type === 'image'" 
                                        :src="item.url" 
                                        :alt="item.name"
                                        class="object-cover"
                                        @click="previewFile(item)"
                                    />
                                    
                                    <!-- Document Preview -->
                                    <div v-else-if="item.type === 'document'" 
                                        class="flex items-center justify-center"
                                        @click="previewFile(item)"
                                    >
                                        <DocumentIcon class="h-16 w-16 text-gray-400" />
                                    </div>

                                    <!-- Spreadsheet Preview -->
                                    <div v-else-if="item.type === 'spreadsheet'" 
                                        class="flex items-center justify-center"
                                        @click="previewFile(item)"
                                    >
                                        <TableCellsIcon class="h-16 w-16 text-gray-400" />
                                    </div>

                                    <!-- Presentation Preview -->
                                    <div v-else-if="item.type === 'presentation'" 
                                        class="flex items-center justify-center"
                                        @click="previewFile(item)"
                                    >
                                        <PresentationChartBarIcon class="h-16 w-16 text-gray-400" />
                                    </div>

                                    <!-- Video Preview -->
                                    <div v-else-if="item.type === 'video'" 
                                        class="flex items-center justify-center"
                                        @click="previewFile(item)"
                                    >
                                        <FilmIcon class="h-16 w-16 text-gray-400" />
                                    </div>

                                    <!-- Audio Preview -->
                                    <div v-else-if="item.type === 'audio'" 
                                        class="flex items-center justify-center"
                                        @click="previewFile(item)"
                                    >
                                        <MusicalNoteIcon class="h-16 w-16 text-gray-400" />
                                    </div>

                                    <!-- Other Files Preview -->
                                    <div v-else 
                                        class="flex items-center justify-center"
                                        @click="previewFile(item)"
                                    >
                                        <DocumentIcon class="h-16 w-16 text-gray-400" />
                                    </div>

                                    <!-- Hover Overlay -->
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-opacity flex items-center justify-center opacity-0 group-hover:opacity-100">
                                        <div class="flex space-x-2">
                                            <button @click.stop="downloadFile(item)" class="p-2 text-white bg-gray-800/50 rounded-lg hover:bg-gray-800/75">
                                                <ArrowDownTrayIcon class="w-5 h-5" />
                                            </button>
                                            <button v-if="can.delete" @click.stop="deleteFile(item)" class="p-2 text-white bg-red-600/50 rounded-lg hover:bg-red-600/75">
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
                                        Uploaded {{ formatDate(item.created_at) }}
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

        <!-- Preview Modal -->
        <Modal :show="!!previewItem" @close="closePreview">
            <div class="p-6">
                <div v-if="previewItem" class="space-y-4">
                    <!-- Preview Content -->
                    <div class="aspect-w-16 aspect-h-9 bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden">
                        <img v-if="previewItem.type === 'image'" 
                            :src="previewItem.url" 
                            :alt="previewItem.name"
                            class="object-contain"
                        />
                        <video v-else-if="previewItem.type === 'video'"
                            :src="previewItem.url"
                            controls
                            class="w-full h-full"
                        ></video>
                        <audio v-else-if="previewItem.type === 'audio'"
                            :src="previewItem.url"
                            controls
                            class="w-full mt-20"
                        ></audio>
                        <iframe v-else-if="previewItem.type === 'document'"
                            :src="previewItem.url"
                            class="w-full h-full"
                        ></iframe>
                        <div v-else class="flex items-center justify-center">
                            <DocumentIcon class="h-24 w-24 text-gray-400" />
                        </div>
                    </div>

                    <!-- File Details -->
                    <div class="mt-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ previewItem.name }}
                        </h3>
                        <dl class="mt-2 grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Type</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ previewItem.type }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Size</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ formatFileSize(previewItem.size) }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Uploaded by</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ previewItem.uploader.name }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Upload date</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ formatDate(previewItem.created_at) }}</dd>
                            </div>
                            <div v-if="previewItem.metadata?.dimensions" class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Dimensions</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ previewItem.metadata.dimensions.width }} × {{ previewItem.metadata.dimensions.height }} pixels
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Actions -->
                    <div class="mt-6 flex justify-end space-x-3">
                        <button
                            @click="closePreview"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                        >
                            Tutup
                        </button>
                        <button
                            @click="downloadFile(previewItem)"
                            :disabled="processing"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 disabled:opacity-25"
                        >
                            <ArrowDownTrayIcon v-if="!processing" class="w-5 h-5 mr-2" />
                            <LoadingSpinner v-else class="w-5 h-5 mr-2" />
                            {{ processing ? 'Mengunduh...' : 'Unduh' }}
                        </button>
                    </div>
                </div>
            </div>
        </Modal>

        <!-- Delete Confirmation Modal -->
        <Modal :show="confirmingMediaDeletion" @close="closeDeleteModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-[var(--text-primary)]">
                    Hapus Media
                </h2>

                <p class="mt-3 text-sm text-[var(--text-secondary)]">
                    Apakah Anda yakin ingin menghapus media ini? Media yang sudah dihapus akan dipindahkan ke trash.
                </p>
                
                <div class="mt-4">
                    <div class="flex items-center">
                        <input
                            id="force-delete"
                            v-model="forceDelete"
                            type="checkbox"
                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 dark:text-indigo-400 dark:border-gray-700 dark:bg-gray-800 dark:focus:ring-indigo-400"
                        />
                        <label for="force-delete" class="ml-2 block text-sm text-red-600 dark:text-red-400">
                            Hapus permanen (tidak dapat dikembalikan)
                        </label>
                    </div>
                </div>

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
                        @click="deleteMedia"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-medium rounded-lg transition-colors duration-200 disabled:opacity-25"
                    >
                        {{ processing ? 'Menghapus...' : 'Hapus Media' }}
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Modal from '@/Components/Modal.vue'
import LoadingSpinner from '@/Components/LoadingSpinner.vue'
import Pagination from '@/Components/Pagination.vue'
import Alert from '@/Components/Alert.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { 
    CloudArrowUpIcon,
    DocumentIcon,
    TableCellsIcon,
    PresentationChartBarIcon,
    FilmIcon,
    MusicalNoteIcon,
    ArrowDownTrayIcon,
    TrashIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    media: Object,
    can: Object,
    filters: {
        type: Object,
        default: () => ({
            type: '',
            search: ''
        })
    },
    auth: {
        type: Object,
        required: true
    }
})

const uploading = ref(false)
const previewItem = ref(null)
const selectedMedia = ref(null)
const confirmingMediaDeletion = ref(false)
const processing = ref(false)
const forceDelete = ref(false)

const filters = ref({
    type: '',
    search: props.filters.search || ''
})

const filteredMedia = computed(() => {
    let result = props.media.data;
    
    // Filter berdasarkan pencarian
    if (filters.value.search) {
        const searchTerm = filters.value.search.toLowerCase();
        result = result.filter(item => {
            const fileName = item.file_name?.toLowerCase() || '';
            const displayName = item.name?.toLowerCase() || '';
            const originalName = item.original_name?.toLowerCase() || '';
            
            return fileName.includes(searchTerm) || 
                   displayName.includes(searchTerm) || 
                   originalName.includes(searchTerm);
        });
    }
    
    // Filter berdasarkan tipe
    if (filters.value.type) {
        result = result.filter(item => item.type === filters.value.type);
    }
    
    return result;
});

const applyFilters = () => {
    router.get('/media', {
        type: filters.value.type,
        search: filters.value.search
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const handleFileUpload = (event) => {
    const files = event.target.files
    if (!files.length) return

    const formData = new FormData()
    Array.from(files).forEach(file => {
        formData.append('files[]', file)
    })

    uploading.value = true
    router.post('/media', formData, {
        onFinish: () => {
            uploading.value = false
            event.target.value = null
        },
    })
}

const downloadFile = async (file) => {
    try {
        // Tampilkan loading state
        processing.value = true;

        // Fetch file dari URL
        const response = await fetch(file.url);
        const blob = await response.blob();

        // Buat URL objek untuk blob
        const blobUrl = window.URL.createObjectURL(blob);

        // Buat element anchor untuk download
        const link = document.createElement('a');
        link.href = blobUrl;
        // Gunakan original_name jika ada, jika tidak gunakan name sebagai fallback
        link.download = file.original_name || file.name;
        document.body.appendChild(link);
        link.click();

        // Cleanup
        document.body.removeChild(link);
        window.URL.revokeObjectURL(blobUrl);
    } catch (error) {
        console.error('Error downloading file:', error);
    } finally {
        processing.value = false;
    }
};

const closeDeleteModal = () => {
    confirmingMediaDeletion.value = false;
    selectedMedia.value = null;
    processing.value = false;
    forceDelete.value = false;
};

const confirmDelete = (file) => {
    selectedMedia.value = file;
    forceDelete.value = false;
    confirmingMediaDeletion.value = true;
};

const deleteMedia = () => {
    if (selectedMedia.value) {
        processing.value = true;
        
        // Parameter untuk force delete
        const params = { force_delete: forceDelete.value };
        
        router.delete(`/media/${selectedMedia.value.id}`, {
            data: params,
            preserveScroll: true,
            onSuccess: () => {
                closeDeleteModal();
                if (previewItem.value?.id === selectedMedia.value.id) {
                    closePreview();
                }
            },
            onError: () => {
                processing.value = false;
            },
            onFinish: () => {
                processing.value = false;
            }
        });
    }
};

const deleteFile = (file) => {
    confirmDelete(file);
};

const previewFile = (file) => {
    previewItem.value = file
}

const closePreview = () => {
    previewItem.value = null
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
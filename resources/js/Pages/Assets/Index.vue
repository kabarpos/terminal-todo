<template>
    <Head title="Assets" />

    <AuthenticatedLayout title="Assets">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Assets
                </h2>
                <button
                    @click="showUploadModal = true"
                    class="inline-flex items-center px-3 py-2 md:px-4 md:py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
                >
                    <ArrowUpTrayIcon class="w-5 h-5 mr-2" />
                    Unggah Asset
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6 bg-white dark:bg-gray-800">
                        <!-- Filter Section -->
                        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                            <div class="flex items-center space-x-4">
                                <button 
                                    v-for="type in ['all', 'image', 'video', 'document']" 
                                    :key="type"
                                    @click="selectedType = type"
                                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200"
                                    :class="{
                                        'bg-gradient-to-r from-blue-600 to-purple-600 text-white': selectedType === type,
                                        'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600': selectedType !== type
                                    }"
                                >
                                    {{ type === 'all' ? 'Semua' : type === 'image' ? 'Gambar' : type === 'video' ? 'Video' : 'Dokumen' }}
                                </button>
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                Total: {{ filteredAssets.length }} asset
                            </div>
                        </div>

                        <!-- Assets Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            <div v-for="asset in filteredAssets" :key="asset.id" 
                                class="relative group overflow-hidden bg-gray-100 dark:bg-gray-900 rounded-lg shadow-sm">
                                <!-- Preview -->
                                <div class="aspect-square">
                                    <img v-if="asset.type === 'image'" :src="asset.url" :alt="asset.name" 
                                        class="w-full h-full object-cover">
                                    <video v-else-if="asset.type === 'video'" :src="asset.url" 
                                        class="w-full h-full object-cover"></video>
                                    <div v-else class="w-full h-full flex items-center justify-center">
                                        <DocumentIcon class="w-16 h-16 text-gray-400" />
                                    </div>
                                </div>

                                <!-- Overlay -->
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-opacity flex items-center justify-center opacity-0 group-hover:opacity-100">
                                    <div class="flex space-x-2">
                                        <button @click="previewAsset(asset)"
                                            class="p-2 text-white bg-gray-800/50 rounded-lg hover:bg-gray-800/75">
                                            <EyeIcon class="w-5 h-5" />
                                        </button>
                                        <button @click="downloadAsset(asset)"
                                            class="p-2 text-white bg-gray-800/50 rounded-lg hover:bg-gray-800/75">
                                            <ArrowDownTrayIcon class="w-5 h-5" />
                                        </button>
                                        <button @click="deleteAsset(asset)"
                                            class="p-2 text-white bg-red-600/50 rounded-lg hover:bg-red-600/75">
                                            <TrashIcon class="w-5 h-5" />
                                        </button>
                                    </div>
                                </div>

                                <!-- Info -->
                                <div class="p-4">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ asset.name }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ formatFileSize(asset.size) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Modal -->
        <Modal :show="showUploadModal" @close="closeUploadModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                    Unggah Asset
                </h2>
                <div class="mt-4">
                    <div class="mb-4">
                        <InputLabel for="files" value="Pilih File" />
                        <input
                            type="file"
                            id="files"
                            multiple
                            @change="handleFileSelect"
                            class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-primary-50 dark:file:bg-primary-900 file:text-primary-700 dark:file:text-primary-300
                                hover:file:bg-primary-100 dark:hover:file:bg-primary-800"
                        />
                        <InputError class="mt-2" :message="form.errors.files" />
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button
                        @click="closeUploadModal"
                        class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-600 focus:bg-gray-400 dark:focus:bg-gray-600 active:bg-gray-500 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:focus:ring-gray-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                    >
                        Batal
                    </button>
                    <button
                        @click="uploadFiles"
                        :disabled="form.processing"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                        :class="{ 'opacity-25': form.processing }"
                    >
                        <ArrowUpTrayIcon class="w-5 h-5 mr-2" />
                        {{ form.processing ? 'Mengunggah...' : 'Unggah' }}
                    </button>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    ArrowUpTrayIcon, 
    ArrowDownTrayIcon,
    DocumentIcon,
    EyeIcon,
    TrashIcon 
} from '@heroicons/vue/24/outline';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    assets: {
        type: Array,
        required: true
    }
});

const showUploadModal = ref(false);
const selectedType = ref('all');

const form = useForm({
    files: []
});

const filteredAssets = computed(() => {
    if (selectedType.value === 'all') {
        return props.assets;
    }
    return props.assets.filter(asset => asset.type === selectedType.value);
});

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const handleFileSelect = (e) => {
    form.files = Array.from(e.target.files);
};

const closeUploadModal = () => {
    showUploadModal.value = false;
    form.reset();
};

const uploadFiles = () => {
    form.post(route('assets.store'), {
        onSuccess: () => closeUploadModal()
    });
};

const previewAsset = (asset) => {
    // Implementasi preview asset
};

const downloadAsset = (asset) => {
    window.open(asset.url, '_blank');
};

const deleteAsset = (asset) => {
    if (confirm('Apakah Anda yakin ingin menghapus asset ini?')) {
        form.delete(route('assets.destroy', asset.id));
    }
};
</script> 
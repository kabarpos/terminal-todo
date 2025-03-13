<template>
    <Head title="Calendar" />

    <AuthenticatedLayout :auth="auth" title="Detail Event">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Detail Event
                </h2>
                <div class="flex space-x-2">
                    <Link
                        v-if="event"
                        :href="route('calendar.edit', event.id)"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-white transition-colors duration-200 bg-blue-600 rounded-lg hover:bg-blue-700"
                    >
                        <PencilIcon class="w-4 h-4 mr-2" />
                        Edit
                    </Link>
                    <button
                        v-if="event"
                        @click="confirmDelete"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-white transition-colors duration-200 bg-red-600 rounded-lg hover:bg-red-700"
                    >
                        <TrashIcon class="w-4 h-4 mr-2" />
                        Hapus
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div v-if="event && Object.keys(event).length > 0" class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6">
                        <!-- Event Details -->
                        <div class="mb-8">
                            <h3 class="mb-4 text-2xl font-bold text-gray-900 dark:text-gray-100">
                                {{ event.title }}
                            </h3>
                            <p class="mb-4 text-gray-600 dark:text-gray-400">
                                {{ event.description }}
                            </p>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <h4 class="mb-2 font-semibold text-gray-700 dark:text-gray-300">Platform</h4>
                                    <div class="flex items-center">
                                        <i :class="['mr-2 text-xl fa-brands', `fa-${event.platform.icon}`]"></i>
                                        <span>{{ event.platform.name }}</span>
                                    </div>
                                </div>

                                <div v-if="event.team">
                                    <h4 class="mb-2 font-semibold text-gray-700 dark:text-gray-300">Tim</h4>
                                    <div class="flex items-center">
                                        <span>{{ event.team.name }}</span>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="mb-2 font-semibold text-gray-700 dark:text-gray-300">Kategori</h4>
                                    <div class="flex items-center">
                                        <div
                                            class="w-4 h-4 mr-2 rounded"
                                            :style="{ backgroundColor: event.category.color }"
                                        ></div>
                                        <span>{{ event.category.name }}</span>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="mb-2 font-semibold text-gray-700 dark:text-gray-300">Tanggal Publish</h4>
                                    <span>{{ formatDate(event.publish_date) }}</span>
                                </div>
                                <div>
                                    <h4 class="mb-2 font-semibold text-gray-700 dark:text-gray-300">Deadline</h4>
                                    <span>{{ formatDate(event.deadline) }}</span>
                                </div>
                                <div>
                                    <h4 class="mb-2 font-semibold text-gray-700 dark:text-gray-300">Status</h4>
                                    <span :class="getStatusClass(event.status)">
                                        {{ getStatusLabel(event.status) }}
                                    </span>
                                </div>
                                <div>
                                    <h4 class="mb-2 font-semibold text-gray-700 dark:text-gray-300">Dibuat Oleh</h4>
                                    <div class="flex items-center">
                                        <div class="w-6 h-6 mr-2 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
                                            <img
                                                v-if="event.creator.profile_photo_url"
                                                :src="event.creator.profile_photo_url"
                                                :alt="event.creator.name"
                                                class="w-full h-full object-cover"
                                            />
                                            <div
                                                v-else
                                                class="w-full h-full flex items-center justify-center text-gray-500 dark:text-gray-400"
                                            >
                                                <UserCircleIcon class="w-6 h-6" />
                                            </div>
                                        </div>
                                        <span>{{ event.creator.name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Assignees -->
                        <div class="mb-8">
                            <h4 class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-300">
                                Tim yang Ditugaskan
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                <div
                                    v-for="assignee in event.assignees"
                                    :key="assignee.id"
                                    class="flex items-center p-2 space-x-2 bg-gray-100 rounded-lg dark:bg-gray-700"
                                >
                                    <img
                                        :src="assignee.avatar_url"
                                        :alt="assignee.name"
                                        class="w-6 h-6 rounded-full"
                                    />
                                    <span class="text-sm">{{ assignee.name }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Comments Section -->
                        <div>
                            <h4 class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-300">
                                Komentar
                            </h4>
                            
                            <!-- Comment Form -->
                            <form @submit.prevent="submitComment" class="mb-6">
                                <div class="mb-4">
                                    <textarea
                                        v-model="form.content"
                                        rows="3"
                                        class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600"
                                        placeholder="Tulis komentar..."
                                    ></textarea>
                                </div>
                                
                                <!-- Attachment Preview -->
                                <div v-if="selectedFile" class="mb-4">
                                    <div class="flex items-center p-2 space-x-2 bg-gray-100 rounded dark:bg-gray-700">
                                        <DocumentIcon class="w-5 h-5" />
                                        <span class="text-sm">{{ selectedFile.name }}</span>
                                        <button
                                            @click="removeFile"
                                            type="button"
                                            class="text-red-500 hover:text-red-700"
                                        >
                                            <XMarkIcon class="w-5 h-5" />
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex space-x-2">
                                        <!-- File Input -->
                                        <label class="cursor-pointer">
                                            <input
                                                type="file"
                                                class="hidden"
                                                @change="handleFileChange"
                                                accept="image/*,.pdf,.doc,.docx"
                                                ref="fileInput"
                                            >
                                            <div class="flex items-center px-3 py-2 text-sm text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                                                <PaperClipIcon class="w-5 h-5 mr-2" />
                                                Lampiran
                                            </div>
                                        </label>
                                        
                                        <!-- URL Input -->
                                        <button
                                            type="button"
                                            @click="showUrlInput = !showUrlInput"
                                            class="flex items-center px-3 py-2 text-sm text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                                        >
                                            <LinkIcon class="w-5 h-5 mr-2" />
                                            Link
                                        </button>
                                    </div>
                                    
                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700"
                                    >
                                        Kirim
                                    </button>
                                </div>
                                
                                <!-- URL Input Field -->
                                <div v-if="showUrlInput" class="mt-4">
                                    <input
                                        v-model="form.link_url"
                                        type="url"
                                        class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600"
                                        placeholder="Masukkan URL..."
                                    >
                                </div>
                            </form>

                            <!-- Comments List -->
                            <div class="space-y-4">
                                <div
                                    v-for="comment in event.comments"
                                    :key="comment.id"
                                    class="p-4 bg-gray-50 rounded-lg dark:bg-gray-700"
                                >
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex items-center space-x-2">
                                            <img
                                                :src="comment.user.avatar_url"
                                                :alt="comment.user.name"
                                                class="w-8 h-8 rounded-full"
                                            />
                                            <div>
                                                <div class="font-medium">{{ comment.user.name }}</div>
                                                <div class="text-sm text-gray-500">
                                                    {{ formatDate(comment.created_at) }}
                                                </div>
                                            </div>
                                        </div>
                                        <button
                                            v-if="comment.user_id === $page.props.auth.user.id"
                                            @click="deleteComment(comment.id)"
                                            class="text-red-500 hover:text-red-700"
                                        >
                                            <TrashIcon class="w-5 h-5" />
                                        </button>
                                    </div>
                                    
                                    <p class="mb-2 text-gray-700 dark:text-gray-300">{{ comment.content }}</p>
                                    
                                    <!-- Attachment -->
                                    <div v-if="comment.attachment_path" class="mt-2">
                                        <a
                                            :href="'/storage/' + comment.attachment_path"
                                            :download="comment.attachment_name"
                                            class="flex items-center p-2 space-x-2 bg-gray-100 rounded dark:bg-gray-600"
                                        >
                                            <DocumentIcon class="w-5 h-5" />
                                            <span class="text-sm">{{ comment.attachment_name }}</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400" v-if="comment.attachment_size">
                                                ({{ formatFileSize(comment.attachment_size) }})
                                            </span>
                                        </a>
                                    </div>
                                    
                                    <!-- Link -->
                                    <div v-if="comment.link_url" class="mt-2">
                                        <a
                                            :href="comment.link_url"
                                            target="_blank"
                                            class="flex items-center p-2 space-x-2 text-blue-600 bg-blue-50 rounded dark:bg-blue-900/20 dark:text-blue-400"
                                        >
                                            <LinkIcon class="w-5 h-5" />
                                            <span class="text-sm">{{ comment.link_url }}</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg p-6">
                    <p class="text-gray-600 dark:text-gray-400">Memuat data... (Data event tidak tersedia)</p>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal :show="confirmingDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Apakah Anda yakin ingin menghapus event ini?
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Setelah event ini dihapus, semua data yang terkait akan dihapus secara permanen.
                </p>

                <div class="mt-6 flex justify-end space-x-3">
                    <SecondaryButton @click="closeModal">
                        Batal
                    </SecondaryButton>

                    <DangerButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteEvent"
                    >
                        Hapus Event
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import TextArea from '@/Components/TextArea.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import { PencilIcon, CalendarIcon, UserCircleIcon, TrashIcon, DocumentIcon, LinkIcon, XMarkIcon, PaperClipIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    event: {
        type: Object,
        required: true
    },
    auth: {
        type: Object,
        required: true
    }
});

const form = useForm({
    content: '',
    attachment: null,
    link_url: ''
});

const selectedFile = ref(null);
const fileInput = ref(null);
const showUrlInput = ref(false);
const confirmingDeletion = ref(false);

const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        selectedFile.value = file;
        form.attachment = file;
    }
};

const removeFile = () => {
    selectedFile.value = null;
    form.attachment = null;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const getStatusClass = (status) => {
    switch (status) {
        case 'planned':
            return 'text-blue-600 dark:text-blue-400';
        case 'in_progress':
            return 'text-yellow-600 dark:text-yellow-400';
        case 'published':
            return 'text-green-600 dark:text-green-400';
        case 'cancelled':
            return 'text-red-600 dark:text-red-400';
        default:
            return 'text-gray-600 dark:text-gray-400';
    }
};

const getStatusLabel = (status) => {
    switch (status) {
        case 'planned':
            return 'Direncanakan';
        case 'in_progress':
            return 'Dalam Proses';
        case 'published':
            return 'Dipublikasi';
        case 'cancelled':
            return 'Dibatalkan';
        default:
            return status;
    }
};

const confirmDelete = () => {
    confirmingDeletion.value = true;
};

const closeModal = () => {
    confirmingDeletion.value = false;
};

const deleteEvent = () => {
    router.delete(route('calendar.destroy', props.event.id), {
        onSuccess: () => closeModal(),
    });
};

const submitComment = () => {
    if (!props.event) {
        console.error('Event data not available');
        return;
    }
    
    form.post(route('calendar.comments.store', props.event.id), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            form.reset();
            selectedFile.value = null;
            showUrlInput.value = false;
            if (fileInput.value) {
                fileInput.value.value = '';
            }
        }
    });
};

const deleteComment = (commentId) => {
    if (confirm('Apakah Anda yakin ingin menghapus komentar ini?')) {
        router.delete(route('calendar.comments.destroy', [props.event.id, commentId]), {
            preserveScroll: true
        });
    }
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script> 
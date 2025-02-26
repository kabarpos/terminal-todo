<template>
    <Head title="Tasks" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Tasks Board
                </h2>
                <Link
                    :href="route('tasks.create')"
                    class="inline-flex items-center px-3 py-2 md:px-4 md:py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
                >
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        <span class="hidden md:inline ml-2">Tambah Task</span>
                    </span>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-full px-4 sm:px-6 lg:px-8">
                <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div class="col-span-2">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Cari task..."
                            class="w-full px-4 py-2 text-sm border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-400 focus:ring-primary-500 dark:focus:ring-primary-400"
                        >
                    </div>
                    <div>
                        <select
                            v-model="priorityFilter"
                            class="w-full px-4 py-2 text-sm border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-400 focus:ring-primary-500 dark:focus:ring-primary-400"
                        >
                            <option value="">Semua Prioritas</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                </div>

                <!-- Kanban Board -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 overflow-x-auto">
                    <template v-for="status in statuses" :key="status">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 ">
                            <h3 class="font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center justify-between">
                                {{ getStatusLabel(status) }}
                                <span class="bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-xs font-medium px-2 py-1 rounded-full">
                                    {{ getTasksByStatus(status).length }}
                                </span>
                            </h3>
                            <draggable 
                                :list="tasksByStatus[status]"
                                :group="'tasks'"
                                item-key="id"
                                class="min-h-[200px] space-y-3"
                                @change="(e) => handleChange(e, status)"
                                :animation="200"
                                ghost-class="sortable-ghost"
                                drag-class="sortable-drag"
                                :force-fallback="true"
                                handle=".drag-handle"
                                :delay="0"
                                :delayOnTouchOnly="true"
                                :touchStartThreshold="5"
                                :fallbackClass="'sortable-fallback'"
                                :fallbackOnBody="true"
                                :scroll="true"
                                :scrollSensitivity="100"
                                :scrollSpeed="20"
                            >
                                <template #item="{ element: task }">
                                    <div 
                                        v-if="matchesFilters(task)"
                                        class="task-card draggable-item bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm hover:shadow-md transition-all duration-500 border border-gray-100 dark:border-gray-700"
                                        :class="{
                                            'animate-highlight': props.highlight === 'new' && task.status === form.status,
                                            'opacity-50': updatingTaskId === task.id
                                        }"
                                    >
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center space-x-2">
                                                <div class="drag-handle cursor-move text-gray-400 hover:text-gray-600">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                                                    </svg>
                                                </div>
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full"
                                                    :class="{
                                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': task.priority === 'low',
                                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': task.priority === 'medium',
                                                        'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200': task.priority === 'high',
                                                        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': task.priority === 'urgent'
                                                    }"
                                                >
                                                    {{ getPriorityLabel(task.priority) }}
                                                </span>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <button
                                                    @click="() => $inertia.visit(route('tasks.edit', task.id))"
                                                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </button>
                                                <button
                                                    @click="confirmTaskDeletion(task)"
                                                    class="text-gray-400 hover:text-red-600 dark:hover:text-red-400"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="font-medium text-gray-900 dark:text-gray-100 mb-1">{{ task.title }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mb-3">{{ task.description }}</div>
                                        <div class="flex items-center justify-between">
                                            <div class="flex -space-x-2">
                                                <template v-for="assignee in task.assignees.slice(0, 3)" :key="assignee.id">
                                                    <img
                                                        v-if="assignee.avatar_url"
                                                        :src="assignee.avatar_url"
                                                        :alt="assignee.name"
                                                        class="h-6 w-6 rounded-full ring-2 ring-white dark:ring-gray-800"
                                                        :title="assignee.name"
                                                    >
                                                    <div
                                                        v-else
                                                        class="h-6 w-6 rounded-full bg-gray-200 dark:bg-gray-700 ring-2 ring-white dark:ring-gray-800 flex items-center justify-center text-xs font-medium text-gray-500 dark:text-gray-400"
                                                        :title="assignee.name"
                                                    >
                                                        {{ assignee.name.charAt(0) }}
                                                    </div>
                                                </template>
                                                <div
                                                    v-if="task.assignees.length > 3"
                                                    class="h-6 w-6 rounded-full bg-gray-200 dark:bg-gray-700 ring-2 ring-white dark:ring-gray-800 flex items-center justify-center text-xs font-medium text-gray-500 dark:text-gray-400"
                                                >
                                                    +{{ task.assignees.length - 3 }}
                                                </div>
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ formatDate(task.due_date) }}
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </draggable>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal :show="confirmingTaskDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Hapus Task
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Apakah Anda yakin ingin menghapus task ini?
                </p>

                <div class="mt-6 flex justify-end space-x-3">
                    <SecondaryButton @click="closeModal">
                        Batal
                    </SecondaryButton>

                    <PrimaryButton
                        variant="danger"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteTask"
                    >
                        {{ form.processing ? 'Menghapus...' : 'Hapus Task' }}
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import draggable from 'vuedraggable';
import axios from 'axios';

const props = defineProps({
    tasks: {
        type: Array,
        required: true
    },
    highlight: {
        type: String,
        default: null
    }
});

const search = ref('');
const priorityFilter = ref('');
const selectedTask = ref(null);
const confirmingTaskDeletion = ref(false);
const form = useForm({});
const updatingTaskId = ref(null);

const statuses = [
    'draft',
    'in_progress',
    'completed',
    'cancelled'
];

const tasksByStatus = ref({
    draft: [],
    in_progress: [],
    completed: [],
    cancelled: []
});

// Pindahkan definisi fungsi ke atas sebelum watch
const initializeTasksByStatus = () => {
    console.log('Initializing tasksByStatus with tasks:', props.tasks);
    
    // Reset semua array
    statuses.forEach(status => {
        tasksByStatus.value[status] = [];
    });

    // Distribusikan task ke masing-masing status
    if (props.tasks) {
        props.tasks.forEach(task => {
            console.log('Processing task:', task);
            if (tasksByStatus.value[task.status]) {
                tasksByStatus.value[task.status].push({...task}); // Clone task object
            } else {
                console.warn('Unknown status:', task.status);
            }
        });
    }
    
    console.log('Initialized tasksByStatus:', tasksByStatus.value);
};

// Sekarang watch bisa menggunakan fungsi yang sudah didefinisikan
watch(() => props.tasks, (newTasks) => {
    console.log('Tasks changed:', newTasks);
    initializeTasksByStatus();
}, { immediate: true, deep: true });

// Fungsi untuk mendapatkan task berdasarkan status
const getTasksByStatus = (status) => {
    return tasksByStatus.value[status] || [];
};

// Fungsi untuk memeriksa apakah task sesuai dengan filter
const matchesFilters = (task) => {
    if (search.value) {
        const searchTerm = search.value.toLowerCase();
        if (!task.title.toLowerCase().includes(searchTerm) && 
            !task.description?.toLowerCase().includes(searchTerm)) {
            return false;
        }
    }

    if (priorityFilter.value && task.priority !== priorityFilter.value) {
        return false;
    }

    return true;
};

// Fungsi untuk menangani perubahan saat drag and drop
const handleChange = async (event, newStatus) => {
    console.log('handleChange called:', { event, newStatus });
    
    if (event.added) {
        const task = event.added.element;
        const oldStatus = task.status;
        
        console.log('Updating task status:', {
            taskId: task.id,
            oldStatus,
            newStatus
        });

        updatingTaskId.value = task.id;

        try {
            // Update status task secara lokal terlebih dahulu
            task.status = newStatus;
            
            // Kirim update ke server
            const response = await axios.put(route('tasks.update-status', task.id), {
                status: newStatus
            });
            
            console.log('Status updated successfully:', response.data);
            
            // Update task dengan data dari server
            if (response.data && response.data.task) {
                Object.assign(task, response.data.task);
            }
            
        } catch (error) {
            console.error('Failed to update status:', error);
            // Jika gagal, kembalikan ke status semula
            task.status = oldStatus;
            
            // Reinisialisasi tasksByStatus
            initializeTasksByStatus();
            
            // Tampilkan pesan error
            alert('Gagal mengubah status task. Silakan coba lagi.');
        } finally {
            updatingTaskId.value = null;
        }
    }
};

const getStatusLabel = (status) => {
    const labels = {
        'draft': 'Draft',
        'in_progress': 'In Progress',
        'completed': 'Completed',
        'cancelled': 'Cancelled'
    };
    return labels[status] || status;
};

const getPriorityLabel = (priority) => {
    const labels = {
        'low': 'Low',
        'medium': 'Medium',
        'high': 'High',
        'urgent': 'Urgent'
    };
    return labels[priority] || priority;
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const confirmTaskDeletion = (task) => {
    selectedTask.value = task;
    confirmingTaskDeletion.value = true;
};

const closeModal = () => {
    confirmingTaskDeletion.value = false;
    selectedTask.value = null;
};

const deleteTask = () => {
    if (selectedTask.value) {
        form.delete(route('tasks.destroy', selectedTask.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                initializeTasksByStatus(); // Refresh task list setelah menghapus
            },
            onError: () => {
                // Handle error
            }
        });
    }
};

onMounted(() => {
    console.log('Tasks received:', props.tasks);
    console.log('Initial tasksByStatus:', tasksByStatus.value);
});

watch(() => tasksByStatus.value, (newValue) => {
    console.log('tasksByStatus changed:', newValue);
}, { deep: true });
</script>

<style scoped>
.status-button {
    transition: all 0.2s ease;
}

.status-button:hover {
    transform: translateY(-1px);
}

/* Prevent text selection during drag */
.drag-handle,
.draggable-item {
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

/* Smooth transition for dragging */
.sortable-drag {
    opacity: 0.5;
    background: #fff;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Ghost item styling */
.sortable-ghost {
    @apply bg-blue-50 border-2 border-blue-200 dark:bg-blue-900 dark:border-blue-800;
    opacity: 0.8;
}

.ghost-class {
    @apply bg-blue-50 border-2 border-blue-200 dark:bg-blue-900 dark:border-blue-800;
}

.drag-class {
    @apply opacity-50 shadow-lg;
}

@keyframes highlight {
    0% {
        background-color: rgba(59, 130, 246, 0.2);
        transform: scale(1);
    }
    50% {
        background-color: rgba(59, 130, 246, 0.1);
        transform: scale(1.02);
    }
    100% {
        background-color: transparent;
        transform: scale(1);
    }
}

.animate-highlight {
    animation: highlight 2s ease-in-out;
}

/* Smooth transitions for all cards */
.task-card {
    transition: all 0.2s ease;
}

.task-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}
</style> 
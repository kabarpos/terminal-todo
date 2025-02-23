<template>
    <Head title="Tasks" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Tasks
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
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <!-- Search and Filter Section -->
                        <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-4">
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
                                    v-model="statusFilter"
                                    class="w-full px-4 py-2 text-sm border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-400 focus:ring-primary-500 dark:focus:ring-primary-400"
                                >
                                    <option value="">Semua Status</option>
                                    <option value="draft">Draft</option>
                                    <option value="in_review">In Review</option>
                                    <option value="approved">Approved</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
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

                        <!-- Tasks Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Task
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Platform
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Kategori
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Prioritas
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Tenggat
                                        </th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <tr v-for="task in filteredTasks" :key="task.id">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {{ task.title }}
                                                    </div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ task.description }}
                                                    </div>
                                                    <div class="mt-1 flex -space-x-2">
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
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div v-if="task.platform" class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8 flex items-center justify-center text-gray-500 dark:text-gray-400">
                                                    <i :class="['text-xl fa-brands', `fa-${task.platform.icon}`]"></i>
                                                </div>
                                                <div class="ml-2">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {{ task.platform.name }}
                                                    </div>
                                                </div>
                                            </div>
                                            <span v-else class="text-sm text-gray-500 dark:text-gray-400">-</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-3 w-3 rounded-full mr-2" :style="{ backgroundColor: task.category.color }"></div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ task.category.name }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                :class="{
                                                    'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200': task.status === 'draft',
                                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': task.status === 'in_review',
                                                    'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': task.status === 'approved',
                                                    'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200': task.status === 'in_progress',
                                                    'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': task.status === 'completed',
                                                    'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': task.status === 'cancelled'
                                                }"
                                            >
                                                {{ getStatusLabel(task.status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                :class="{
                                                    'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': task.priority === 'low',
                                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': task.priority === 'medium',
                                                    'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200': task.priority === 'high',
                                                    'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': task.priority === 'urgent'
                                                }"
                                            >
                                                {{ getPriorityLabel(task.priority) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ formatDate(task.due_date) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link
                                                :href="route('tasks.show', task.id)"
                                                class="inline-flex items-center px-2 sm:px-3 py-1 sm:py-1.5 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-xs sm:text-sm font-medium rounded-lg transition-colors duration-200"
                                            >
                                                Detail
                                            </Link>
                                            <Link
                                                :href="route('tasks.edit', task.id)"
                                                class="inline-flex items-center px-2 sm:px-3 py-1 sm:py-1.5 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-xs sm:text-sm font-medium rounded-lg transition-colors duration-200"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                @click="confirmTaskDeletion(task)"
                                                class="inline-flex items-center px-2 sm:px-3 py-1 sm:py-1.5 bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white text-xs sm:text-sm font-medium rounded-lg transition-colors duration-200"
                                            >
                                                Hapus
                                            </button>
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
        <Modal :show="confirmingTaskDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Hapus Task
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Apakah Anda yakin ingin menghapus task ini?
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
                        @click="deleteTask"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-medium rounded-lg transition-colors duration-200 disabled:opacity-25"
                    >
                        {{ form.processing ? 'Menghapus...' : 'Hapus Task' }}
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

const props = defineProps({
    tasks: {
        type: Array,
        required: true
    }
});

const search = ref('');
const statusFilter = ref('');
const priorityFilter = ref('');
const selectedTask = ref(null);
const confirmingTaskDeletion = ref(false);
const form = useForm({});

// Computed property untuk filtered tasks
const filteredTasks = computed(() => {
    let filtered = props.tasks;
    
    if (search.value) {
        const searchTerm = search.value.toLowerCase();
        filtered = filtered.filter(task => 
            task.title.toLowerCase().includes(searchTerm) ||
            task.description?.toLowerCase().includes(searchTerm)
        );
    }

    if (statusFilter.value) {
        filtered = filtered.filter(task => task.status === statusFilter.value);
    }

    if (priorityFilter.value) {
        filtered = filtered.filter(task => task.priority === priorityFilter.value);
    }

    return filtered;
});

const getStatusLabel = (status) => {
    const labels = {
        'draft': 'Draft',
        'in_review': 'In Review',
        'approved': 'Approved',
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
            onSuccess: () => closeModal(),
            onError: () => {
                // Handle error
            }
        });
    }
};
</script> 
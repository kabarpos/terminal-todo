<template>
    <Head title="Tasks" />

    <AuthenticatedLayout :auth="auth" title="Detail Task">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Detail Task
                </h2>
                <div class="flex items-center space-x-2">
                    <Link
                        :href="route('tasks.edit', task.id)"
                        class="inline-flex items-center px-3 py-2 md:px-4 md:py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
                    >
                        Edit Task
                    </Link>
                    <Link
                        :href="route('tasks.index')"
                        class="inline-flex items-center px-3 py-2 md:px-4 md:py-2 bg-gradient-to-r from-gray-600 to-gray-500 hover:from-gray-700 hover:to-gray-600 text-white text-sm font-medium rounded-lg transition-colors duration-200"
                    >
                        Kembali
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <!-- Task Details -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium">{{ task.title }}</h3>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    {{ task.description }}
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Platform</h4>
                                    <div class="mt-1 flex items-center">
                                        <div v-if="task.platform" class="flex items-center">
                                            <div class="flex-shrink-0 h-6 w-6 flex items-center justify-center text-gray-500 dark:text-gray-400">
                                                <i :class="['text-lg fa-brands', `fa-${task.platform.icon}`]"></i>
                                            </div>
                                            <span class="ml-2">{{ task.platform.name }}</span>
                                        </div>
                                        <span v-else>-</span>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Kategori</h4>
                                    <div class="mt-1 flex items-center">
                                        <div class="h-3 w-3 rounded-full" :style="{ backgroundColor: task.category.color }"></div>
                                        <span class="ml-2">{{ task.category.name }}</span>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</h4>
                                    <div class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="{
                                                'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300': task.status === 'draft',
                                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': task.status === 'in_review',
                                                'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': task.status === 'approved',
                                                'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': task.status === 'in_progress',
                                                'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200': task.status === 'completed',
                                                'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': task.status === 'cancelled'
                                            }"
                                        >
                                            {{ getStatusLabel(task.status) }}
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Prioritas</h4>
                                    <div class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="{
                                                'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300': task.priority === 'low',
                                                'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': task.priority === 'medium',
                                                'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200': task.priority === 'high',
                                                'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': task.priority === 'urgent'
                                            }"
                                        >
                                            {{ getPriorityLabel(task.priority) }}
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Mulai</h4>
                                    <p class="mt-1">{{ formatDate(task.start_date) }}</p>
                                </div>

                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Tenggat Waktu</h4>
                                    <p class="mt-1">{{ formatDate(task.due_date) }}</p>
                                </div>
                            </div>

                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Assignee</h4>
                                <div class="mt-2 space-y-2">
                                    <div v-for="assignee in task.assignees" :key="assignee.id" class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <img v-if="assignee.avatar_url" :src="assignee.avatar_url" :alt="assignee.name" class="h-8 w-8 rounded-full">
                                            <div v-else class="h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-600 dark:text-gray-300">
                                                    {{ getInitials(assignee.name) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium">{{ assignee.name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ assignee.role }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Comments Section -->
                            <div class="mt-8">
                                <h3 class="text-lg font-medium">Komentar</h3>
                                <div class="mt-4 space-y-4">
                                    <div v-for="comment in task.comments" :key="comment.id" class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <img v-if="comment.user.avatar_url" :src="comment.user.avatar_url" :alt="comment.user.name" class="h-8 w-8 rounded-full">
                                                <div v-else class="h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">
                                                        {{ getInitials(comment.user.name) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-3 flex-1">
                                                <div class="flex items-center justify-between">
                                                    <p class="text-sm font-medium">{{ comment.user.name }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatDate(comment.created_at) }}</p>
                                                </div>
                                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ comment.content }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Add Comment Form -->
                                <form @submit.prevent="submitComment" class="mt-6">
                                    <div>
                                        <TextArea
                                            v-model="commentForm.content"
                                            placeholder="Tambahkan komentar..."
                                            class="mt-1 block w-full"
                                            rows="3"
                                            required
                                        />
                                        <InputError :message="commentForm.errors.content" class="mt-2" />
                                    </div>

                                    <div class="mt-4 flex justify-end">
                                        <PrimaryButton
                                            :class="{ 'opacity-25': commentForm.processing }"
                                            :disabled="commentForm.processing"
                                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium rounded-lg transition-colors duration-200"
                                        >
                                            {{ commentForm.processing ? 'Mengirim...' : 'Kirim Komentar' }}
                                        </PrimaryButton>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TextArea from '@/Components/TextArea.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { format, parseISO } from 'date-fns';
import { id } from 'date-fns/locale';

const props = defineProps({
    task: {
        type: Object,
        required: true
    },
    auth: {
        type: Object,
        required: true
    }
});

const commentForm = useForm({
    content: ''
});

const getStatusLabel = (status) => {
    const labels = {
        draft: 'Draft',
        in_review: 'In Review',
        approved: 'Approved',
        in_progress: 'In Progress',
        completed: 'Completed',
        cancelled: 'Cancelled'
    };
    return labels[status] || status;
};

const getPriorityLabel = (priority) => {
    const labels = {
        low: 'Low',
        medium: 'Medium',
        high: 'High',
        urgent: 'Urgent'
    };
    return labels[priority] || priority;
};

const formatDate = (date) => {
    if (!date) return '-';
    try {
        return format(new Date(date), 'dd MMM yyyy HH:mm', { locale: id });
    } catch (error) {
        return '-';
    }
};

const getInitials = (name) => {
    return name
        .split(' ')
        .map(word => word[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
};

const submitComment = () => {
    commentForm.post(route('tasks.comments.store', props.task.id), {
        preserveScroll: true,
        onSuccess: () => {
            commentForm.reset();
        }
    });
};
</script> 
<template>
    <Head :title="event.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Detail Event
                </h2>
                <Link
                    :href="route('calendar.edit', event.id)"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800"
                >
                    <PencilIcon class="w-4 h-4 mr-2" />
                    Edit
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6">
                        <!-- Event Header -->
                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-4">
                                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ event.title }}
                                </h1>
                                <div
                                    class="px-3 py-1 text-sm font-medium rounded-full"
                                    :class="{
                                        'bg-yellow-100 text-yellow-800': event.status === 'draft',
                                        'bg-blue-100 text-blue-800': event.status === 'scheduled',
                                        'bg-green-100 text-green-800': event.status === 'published'
                                    }"
                                >
                                    {{ statusLabel }}
                                </div>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400">
                                {{ event.description }}
                            </p>
                        </div>

                        <!-- Event Details -->
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        Informasi Publikasi
                                    </h3>
                                    <div class="mt-4 space-y-4">
                                        <div class="flex items-center">
                                            <CalendarIcon class="w-5 h-5 mr-2 text-gray-400" />
                                            <span class="text-gray-600 dark:text-gray-400">
                                                {{ formatDate(event.publish_date) }}
                                            </span>
                                        </div>
                                        <div class="flex items-center">
                                            <i
                                                :class="[
                                                    'mr-2 text-xl text-gray-400 fa-brands',
                                                    `fa-${event.platform.icon}`
                                                ]"
                                            ></i>
                                            <span class="text-gray-600 dark:text-gray-400">
                                                {{ event.platform.name }}
                                            </span>
                                        </div>
                                        <div class="flex items-center">
                                            <TagIcon class="w-5 h-5 mr-2 text-gray-400" />
                                            <span
                                                class="px-2 py-1 text-sm rounded"
                                                :style="{
                                                    backgroundColor: event.category.color + '20',
                                                    color: event.category.color
                                                }"
                                            >
                                                {{ event.category.name }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        Penanggung Jawab
                                    </h3>
                                    <div class="mt-4 space-y-2">
                                        <div
                                            v-for="assignee in event.assignees"
                                            :key="assignee.id"
                                            class="flex items-center"
                                        >
                                            <UserCircleIcon class="w-5 h-5 mr-2 text-gray-400" />
                                            <span class="text-gray-600 dark:text-gray-400">
                                                {{ assignee.name }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    Komentar
                                </h3>
                                <div class="mt-4 space-y-4">
                                    <form @submit.prevent="addComment" class="mb-6">
                                        <TextArea
                                            v-model="commentForm.content"
                                            placeholder="Tambahkan komentar..."
                                            class="w-full"
                                            rows="3"
                                            required
                                        />
                                        <div class="flex justify-end mt-2">
                                            <PrimaryButton
                                                type="submit"
                                                :disabled="commentForm.processing"
                                            >
                                                Kirim
                                            </PrimaryButton>
                                        </div>
                                    </form>

                                    <div class="space-y-4">
                                        <div
                                            v-for="comment in event.comments"
                                            :key="comment.id"
                                            class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg"
                                        >
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="flex items-center">
                                                    <span class="font-medium text-gray-900 dark:text-gray-100">
                                                        {{ comment.user.name }}
                                                    </span>
                                                    <span class="mx-2 text-gray-400">•</span>
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ formatDate(comment.created_at) }}
                                                    </span>
                                                </div>
                                                <button
                                                    v-if="comment.user_id === $page.props.auth.user.id"
                                                    @click="deleteComment(comment.id)"
                                                    class="text-red-600 hover:text-red-800"
                                                >
                                                    <TrashIcon class="w-4 h-4" />
                                                </button>
                                            </div>
                                            <p class="text-gray-600 dark:text-gray-400">
                                                {{ comment.content }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    PencilIcon,
    CalendarIcon,
    TagIcon,
    UserCircleIcon,
    TrashIcon
} from '@heroicons/vue/24/outline';
import TextArea from '@/Components/TextArea.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { format, parseISO } from 'date-fns';
import { id } from 'date-fns/locale';

const props = defineProps({
    event: {
        type: Object,
        required: true
    }
});

const statusLabel = computed(() => {
    const labels = {
        draft: 'Draft',
        scheduled: 'Terjadwal',
        published: 'Dipublikasi'
    };
    return labels[props.event.status] || props.event.status;
});

const formatDate = (date) => {
    if (!date) return '-';
    try {
        return format(parseISO(date), 'dd MMMM yyyy HH:mm', { locale: id });
    } catch (error) {
        return '-';
    }
};

const commentForm = useForm({
    content: ''
});

const addComment = () => {
    commentForm.post(route('calendar.comments.store', props.event.id), {
        onSuccess: () => {
            commentForm.reset();
        }
    });
};

const deleteComment = (commentId) => {
    if (confirm('Apakah Anda yakin ingin menghapus komentar ini?')) {
        useForm().delete(route('calendar.comments.destroy', [props.event.id, commentId]));
    }
};
</script> 
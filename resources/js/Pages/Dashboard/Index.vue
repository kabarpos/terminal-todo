<script setup>
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from '@/Components/Card.vue';
import { ref, onMounted } from 'vue';

const props = defineProps({
    auth: {
        type: Object,
        required: true
    },
    taskStats: {
        type: Object,
        required: true,
        default: () => ({
            draft: 0,
            in_progress: 0,
            completed: 0,
            cancelled: 0
        })
    },
    monthlyEvents: {
        type: Array,
        required: true,
        default: () => []
    }
});

const stats = ref({
    draft: props.taskStats.draft,
    in_progress: props.taskStats.in_progress,
    completed: props.taskStats.completed,
    cancelled: props.taskStats.cancelled
});

const eventStats = ref(props.monthlyEvents);
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout :auth="auth" title="Dashboard">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-lg md:text-xl font-semibold text-[var(--text-primary)] truncate">
                    Dashboard
                </h2>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Task Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <Card class="p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-gray-100 rounded-full">
                            <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm text-[var(--text-secondary)]">Draft</h3>
                            <p class="text-2xl font-semibold text-[var(--text-primary)]">{{ stats.draft }}</p>
                        </div>
                    </div>
                </Card>

                <Card class="p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm text-[var(--text-secondary)]">In Progress</h3>
                            <p class="text-2xl font-semibold text-[var(--text-primary)]">{{ stats.in_progress }}</p>
                        </div>
                    </div>
                </Card>

                <Card class="p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-full">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm text-[var(--text-secondary)]">Completed</h3>
                            <p class="text-2xl font-semibold text-[var(--text-primary)]">{{ stats.completed }}</p>
                        </div>
                    </div>
                </Card>

                <Card class="p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-red-100 rounded-full">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm text-[var(--text-secondary)]">Cancelled</h3>
                            <p class="text-2xl font-semibold text-[var(--text-primary)]">{{ stats.cancelled }}</p>
                        </div>
                    </div>
                </Card>
            </div>

            <!-- Monthly Events -->
            <Card class="p-6">
                <h3 class="text-lg font-semibold text-[var(--text-primary)] mb-4">Agenda Bulanan</h3>
                <div class="space-y-4">
                    <div v-for="(month, index) in eventStats" :key="index" class="flex items-center justify-between p-4 bg-[var(--bg-secondary)] rounded-lg">
                        <div class="flex items-center space-x-4">
                            <div class="p-2 bg-indigo-100 rounded-full">
                                <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-[var(--text-primary)]">{{ month.name }}</p>
                                <p class="text-sm text-[var(--text-secondary)]">{{ month.total }} agenda</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2.5 py-0.5 text-xs font-medium rounded-full" :class="{
                                'bg-green-100 text-green-800': month.status === 'completed',
                                'bg-yellow-100 text-yellow-800': month.status === 'in_progress',
                                'bg-gray-100 text-gray-800': month.status === 'upcoming'
                            }">
                                {{ month.status }}
                            </span>
                        </div>
                    </div>
                </div>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>

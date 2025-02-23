<script setup>
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from '@/Components/Card.vue';

defineProps({
    auth: {
        type: Object,
        required: true
    }
});
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

        <!-- Placeholder Content -->
        <div class="space-y-6">
            <Card class="p-6">
                <div class="text-center py-12">
                    <div class="mb-4">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-[var(--text-primary)]">
                        Mulai Membangun Aplikasi Anda
                    </h3>
                    <p class="mt-2 text-sm text-[var(--text-secondary)]">
                        Ini adalah halaman dashboard starter. Mulai tambahkan konten sesuai kebutuhan aplikasi Anda.
                    </p>
                </div>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>

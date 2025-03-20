<template>
    <Head title="Tim" />

    <AuthenticatedLayout title="Tim">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Tim
                </h2>
                <Link
                    :href="route('teams.create')"
                    class="inline-flex items-center px-3 py-2 md:px-4 md:py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
                >
                    <span class="flex items-center">
                        <PlusIcon class="h-5 w-5" />
                        <span class="hidden md:inline ml-2">Buat Tim Baru</span>
                    </span>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6 bg-white dark:bg-gray-800">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            <div v-for="team in teams" :key="team.id" 
                                class="overflow-hidden transition-shadow duration-300 bg-white border rounded-lg shadow-sm dark:bg-gray-900 dark:border-gray-700 hover:shadow-lg">
                                <div class="p-5">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div :style="{ backgroundColor: team.color }" 
                                                class="flex items-center justify-center w-10 h-10 rounded-full">
                                                <span class="text-lg font-bold text-white">
                                                    {{ team.name.charAt(0).toUpperCase() }}
                                                </span>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                    {{ team.name }}
                                                </h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ team.users.length }} Anggota
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <Link :href="route('teams.show', team.id)"
                                                class="p-2 text-gray-500 transition-colors duration-200 rounded-lg dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800"
                                            >
                                                <EyeIcon class="w-5 h-5" />
                                            </Link>
                                            <Link :href="route('teams.edit', team.id)"
                                                class="p-2 text-gray-500 transition-colors duration-200 rounded-lg dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800"
                                            >
                                                <PencilSquareIcon class="w-5 h-5" />
                                            </Link>
                                            <button @click="deleteTeam(team)"
                                                class="p-2 text-red-500 transition-colors duration-200 rounded-lg hover:bg-red-100 dark:hover:bg-red-900"
                                            >
                                                <TrashIcon class="w-5 h-5" />
                                            </button>
                                        </div>
                                    </div>
                                    <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">
                                        {{ team.description || 'Tidak ada deskripsi' }}
                                    </p>
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
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { PlusIcon, EyeIcon, PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    teams: {
        type: Array,
        required: true
    }
});

const deleteTeam = (team) => {
    if (confirm(`Apakah Anda yakin ingin menghapus tim "${team.name}"?`)) {
        router.delete(route('teams.destroy', team.id));
    }
};
</script> 
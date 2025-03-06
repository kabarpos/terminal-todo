<template>
    <Head title="Social Media Reports" />

    <AuthenticatedLayout :auth="auth" title="Create Report">
        <template #header>
            <div class="w-full">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg md:text-xl font-semibold text-[var(--text-primary)] truncate">
                        Create Social Media Report
                    </h2>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="title" value="Title" class="text-gray-900 dark:text-gray-100" />
                                <TextInput
                                    id="title"
                                    type="text"
                                    v-model="form.title"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.title" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="category" value="Category" class="text-gray-900 dark:text-gray-100" />
                                <select
                                    id="category"
                                    v-model="form.category_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                    required
                                >
                                    <option value="">Select Category</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.category_id" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="platform" value="Platform" class="text-gray-900 dark:text-gray-100" />
                                <select
                                    id="platform"
                                    v-model="form.platform_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                    required
                                >
                                    <option value="">Select Platform</option>
                                    <option v-for="platform in platforms" :key="platform.id" :value="platform.id">
                                        {{ platform.name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.platform_id" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="posting_date" value="Posting Date" class="text-gray-900 dark:text-gray-100" />
                                <input
                                    id="posting_date"
                                    type="date"
                                    v-model="form.posting_date"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                    required
                                />
                                <InputError :message="form.errors.posting_date" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="url" value="URL" class="text-gray-900 dark:text-gray-100" />
                                <TextInput
                                    id="url"
                                    type="url"
                                    v-model="form.url"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.url" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <Link
                                    :href="route('social-media-reports.index')"
                                    class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-600 focus:bg-gray-400 dark:focus:bg-gray-600 active:bg-gray-500 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:focus:ring-gray-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 mr-3"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    <svg
                                        v-if="form.processing"
                                        class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                    >
                                        <circle
                                            class="opacity-25"
                                            cx="12"
                                            cy="12"
                                            r="10"
                                            stroke="currentColor"
                                            stroke-width="4"
                                        />
                                        <path
                                            class="opacity-75"
                                            fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                        />
                                    </svg>
                                    {{ form.processing ? 'Creating...' : 'Create Report' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    categories: {
        type: Array,
        required: true,
    },
    platforms: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    title: '',
    category_id: '',
    platform_id: '',
    posting_date: '',
    url: '',
});

const submit = () => {
    form.post(route('social-media-reports.store'));
};
</script> 
<template>
    <Head title="Edit Social Media Report" />

    <AuthenticatedLayout :auth="auth">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Edit Social Media Report
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="posting_date" value="Posting Date" class="text-gray-900 dark:text-gray-100" />
                                <input
                                    id="posting_date"
                                    v-model="form.posting_date"
                                    type="date"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                    required
                                >
                                <InputError :message="form.errors.posting_date" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="category_id" value="Category" class="text-gray-900 dark:text-gray-100" />
                                <select
                                    id="category_id"
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
                                <InputLabel for="url" value="URL" class="text-gray-900 dark:text-gray-100" />
                                <TextInput
                                    id="url"
                                    type="url"
                                    v-model="form.url"
                                    class="mt-1 block w-full"
                                    required
                                    placeholder="https://"
                                />
                                <InputError :message="form.errors.url" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <SecondaryButton
                                    :href="route('social-media-reports.index')"
                                    class="mr-3"
                                >
                                    Cancel
                                </SecondaryButton>
                                <PrimaryButton
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
                                    {{ form.processing ? 'Saving...' : 'Save Changes' }}
                                </PrimaryButton>
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
    report: {
        type: Object,
        required: true
    },
    categories: {
        type: Array,
        required: true
    },
    auth: {
        type: Object,
        required: true
    }
});

const form = useForm({
    posting_date: props.report.posting_date,
    category_id: props.report.category_id,
    url: props.report.url
});

const submit = () => {
    form.put(route('social-media-reports.update', props.report.id));
};
</script> 
<template>
    <Head title="Edit Laporan Media Sosial" />

    <AuthenticatedLayout :auth="auth" title="Edit Laporan Media Sosial">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Edit Laporan Media Sosial
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="posting_date" value="Tanggal Posting" />
                                <input
                                    id="posting_date"
                                    v-model="form.posting_date"
                                    type="date"
                                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                >
                                <InputError :message="form.errors.posting_date" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="category_id" value="Jenis Konten" />
                                <select
                                    id="category_id"
                                    v-model="form.category_id"
                                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                >
                                    <option value="">Pilih Jenis Konten</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.category_id" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="url" value="Link URL" />
                                <TextInput
                                    id="url"
                                    v-model="form.url"
                                    type="url"
                                    class="mt-1 block w-full"
                                    required
                                    placeholder="https://"
                                />
                                <InputError :message="form.errors.url" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <Link
                                    :href="route('social-media-reports.index')"
                                    class="mr-3 inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                >
                                    Batal
                                </Link>
                                <PrimaryButton
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium rounded-lg transition-colors duration-200"
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
                                    {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
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
    form.put(route('social-media-reports.update', props.report.id), {
        onSuccess: () => {
            // Handle success
        },
        onError: () => {
            // Handle error
        }
    });
};
</script> 
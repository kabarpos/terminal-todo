<template>
    <Head title="Platform" />

    <AuthenticatedLayout :auth="auth" title="Edit Platform">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Edit Platform
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="name" value="Nama Platform" />
                                <TextInput
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                    autofocus
                                />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="icon" value="Icon" />
                                <TextInput
                                    id="icon"
                                    v-model="form.icon"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                    placeholder="instagram"
                                />
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Masukkan nama icon dari Font Awesome tanpa awalan "fa-". Contoh: instagram, facebook, twitter, dll.
                                </p>
                                <InputError :message="form.errors.icon" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="description" value="Deskripsi" />
                                <TextArea
                                    id="description"
                                    v-model="form.description"
                                    class="mt-1 block w-full"
                                    rows="3"
                                />
                                <InputError :message="form.errors.description" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="settings" value="Pengaturan Platform" />
                                <TextArea
                                    id="settings"
                                    v-model="form.settings"
                                    class="mt-1 block w-full font-mono"
                                    rows="5"
                                />
                                <p class="mt-1 text-sm text-gray-500">
                                    Masukkan pengaturan platform dalam format JSON.
                                </p>
                                <InputError :message="form.errors.settings" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="is_active" class="inline-flex items-center">
                                    <Checkbox
                                        id="is_active"
                                        v-model:checked="form.is_active"
                                        :value="true"
                                    />
                                    <span class="ml-2">Aktif</span>
                                </InputLabel>
                            </div>

                            <div class="flex items-center justify-end gap-4">
                                <Link
                                    :href="route('platforms.index')"
                                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                >
                                    Batal
                                </Link>

                                <PrimaryButton
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium rounded-lg transition-colors duration-200"
                                >
                                    {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
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
import { onMounted } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import Checkbox from '@/Components/Checkbox.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    platform: {
        type: Object,
        required: true
    },
    auth: {
        type: Object,
        required: true
    }
});

const form = useForm({
    name: props.platform.name,
    icon: props.platform.icon,
    description: props.platform.description,
    settings: JSON.stringify(props.platform.settings, null, 2),
    is_active: props.platform.is_active
});

const submit = () => {
    // Parse settings JSON if not empty
    if (form.settings) {
        try {
            form.settings = JSON.parse(form.settings);
        } catch (e) {
            form.setError('settings', 'Format JSON tidak valid');
            return;
        }
    }

    form.put(route('platforms.update', props.platform.id));
};
</script> 
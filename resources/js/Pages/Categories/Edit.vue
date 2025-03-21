<template>
    <Head title="Kategori" />

    <AuthenticatedLayout :auth="auth" title="Edit Kategori">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Edit Kategori
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-3 gap-6">
                                <div>
                                    <InputLabel for="name" value="Nama Kategori" />
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
                                    <InputLabel for="type" value="Tipe" />
                                    <SelectInput
                                        id="type"
                                        v-model="form.type"
                                        class="mt-1 block w-full"
                                        required
                                    >
                                        <option value="">Pilih Tipe</option>
                                        <option value="content">Konten</option>
                                        <option value="task">Task</option>
                                    </SelectInput>
                                    <InputError :message="form.errors.type" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="color" value="Warna" />
                                    <TextInput
                                        id="color"
                                        v-model="form.color"
                                        type="color"
                                        class="mt-1 block w-full h-10"
                                        required
                                    />
                                    <InputError :message="form.errors.color" class="mt-2" />
                                </div>
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
                                    :href="route('categories.index')"
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
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import SelectInput from '@/Components/SelectInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    category: {
        type: Object,
        required: true
    },
    auth: {
        type: Object,
        required: true
    }
});

const form = useForm({
    name: props.category.name,
    type: props.category.type,
    color: props.category.color,
    description: props.category.description,
    is_active: props.category.is_active
});

const submit = () => {
    form.put(route('categories.update', props.category.id));
};
</script> 
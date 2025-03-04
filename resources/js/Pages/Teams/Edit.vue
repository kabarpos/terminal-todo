<template>
    <Head title="Edit Tim" />

    <AuthenticatedLayout title="Edit Tim">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Edit Tim
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6 bg-white dark:bg-gray-800">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="name" value="Nama Tim" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="block w-full mt-1"
                                    v-model="form.name"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div>
                                <InputLabel for="description" value="Deskripsi" />
                                <TextArea
                                    id="description"
                                    class="block w-full mt-1"
                                    v-model="form.description"
                                    rows="4"
                                />
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <div>
                                <InputLabel for="color" value="Warna Tim" />
                                <div class="flex items-center space-x-3 mt-1">
                                    <input
                                        type="color"
                                        id="color"
                                        v-model="form.color"
                                        class="w-10 h-10 p-1 rounded cursor-pointer"
                                    />
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ form.color }}
                                    </span>
                                </div>
                                <InputError class="mt-2" :message="form.errors.color" />
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <Link
                                    :href="route('teams.show', team.id)"
                                    class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-600 focus:bg-gray-400 dark:focus:bg-gray-600 active:bg-gray-500 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:focus:ring-gray-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                                >
                                    Batal
                                </Link>

                                <button
                                    type="submit"
                                    class="inline-flex items-center px-4 py-2 ml-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    <PencilSquareIcon class="w-5 h-5 mr-2" />
                                    {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
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
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { PencilSquareIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    team: {
        type: Object,
        required: true
    }
});

const form = useForm({
    name: props.team.name,
    description: props.team.description,
    color: props.team.color
});

const submit = () => {
    form.put(route('teams.update', props.team.id));
};
</script> 
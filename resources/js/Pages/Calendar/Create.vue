<template>
    <Head title="Tambah Event" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Tambah Event
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6">
                        <EventForm
                            :form="form"
                            :platforms="platforms"
                            :categories="categories"
                            :users="users"
                            @submit="submit"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import EventForm from '@/Components/EventForm.vue';

const props = defineProps({
    platforms: Array,
    categories: Array,
    users: Array,
    initialDate: String
});

const form = useForm({
    title: '',
    description: '',
    publish_date: props.initialDate || '',
    deadline: '',
    platform_id: '',
    category_id: '',
    status: 'planned',
    assignees: []
});

const submit = () => {
    form.post(route('calendar.store'), {
        onSuccess: () => {
            form.reset();
        }
    });
};
</script> 
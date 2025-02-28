<template>
    <Head title="Calendar" />

    <AuthenticatedLayout :auth="auth" title="Edit Event">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-[var(--text-primary)]">
                    Edit Event
                </h2>
                <div class="flex items-center space-x-3">
                    <SecondaryButton
                        @click="() => router.get(route('calendar.index'))"
                        variant="outline"
                    >
                        <ArrowLeftIcon class="w-5 h-5 mr-2" />
                        Kembali
                    </SecondaryButton>
                    <DangerButton
                        @click="confirmDelete"
                        :disabled="form.processing"
                    >
                        <TrashIcon class="w-5 h-5 mr-2" />
                        Hapus
                    </DangerButton>
                </div>
            </div>
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

        <!-- Delete Confirmation Modal -->
        <Modal :show="confirmingDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Apakah Anda yakin ingin menghapus event ini?
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Setelah event ini dihapus, semua data yang terkait akan dihapus secara permanen.
                </p>

                <div class="mt-6 flex justify-end space-x-3">
                    <SecondaryButton @click="closeModal">
                        Batal
                    </SecondaryButton>

                    <DangerButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteEvent"
                    >
                        {{ form.processing ? 'Menghapus...' : 'Hapus Event' }}
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';
import EventForm from '@/Components/EventForm.vue';
import { ArrowLeftIcon, TrashIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    event: {
        type: Object,
        required: true
    },
    platforms: {
        type: Array,
        required: true
    },
    categories: {
        type: Array,
        required: true
    },
    users: {
        type: Array,
        required: true
    },
    auth: {
        type: Object,
        required: true
    }
});

// Debug initial data
console.log('Initial Event Data:', props.event);

const form = useForm({
    title: props.event.title,
    description: props.event.description,
    publish_date: props.event.publish_date,
    deadline: props.event.deadline,
    status: props.event.status,
    platform_id: props.event.platform_id,
    category_id: props.event.category_id,
    assignees: Array.isArray(props.event.assignees) 
        ? props.event.assignees
            .map(a => a.id?.toString())
            .filter(id => id !== undefined)
        : []
});

// Debug form initialization
console.log('Form Data after initialization:', form.data());

const confirmingDeletion = ref(false);

const confirmDelete = () => {
    confirmingDeletion.value = true;
};

const closeModal = () => {
    confirmingDeletion.value = false;
};

const deleteEvent = () => {
    form.delete(route('calendar.destroy', props.event.id), {
        onSuccess: () => closeModal(),
    });
};

const submit = () => {
    // Debug before submit
    console.log('=== DEBUG SUBMIT START ===');
    console.log('Event ID:', props.event.id);
    console.log('Form Data before submit:', {
        title: form.title,
        description: form.description,
        publish_date: form.publish_date,
        deadline: form.deadline,
        status: form.status,
        platform_id: form.platform_id,
        category_id: form.category_id,
        assignees: form.assignees
    });
    console.log('Route:', route('calendar.update', props.event.id));
    
    // Pastikan assignees adalah array valid
    const formData = form.data();
    formData.assignees = formData.assignees.filter(id => id !== undefined && id !== null);
    
    form.put(route('calendar.update', props.event.id), formData, {
        preserveScroll: true,
        onSuccess: (page) => {
            console.log('Success Response:', page);
            router.visit(route('calendar.index'));
        },
        onError: (errors) => {
            console.error('=== VALIDATION ERRORS ===');
            console.error(errors);
            console.log('Current Form State:', form.data());
            console.log('Processing State:', form.processing);
        },
        onFinish: () => {
            console.log('=== REQUEST FINISHED ===');
            console.log('Final Form State:', form.data());
        }
    });
};
</script> 
<template>
    <Head title="Edit Event" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Edit Event
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
                        Hapus Event
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import SelectInput from '@/Components/SelectInput.vue';
import MultiSelect from '@/Components/MultiSelect.vue';
import Modal from '@/Components/Modal.vue';
import EventForm from '@/Components/EventForm.vue';

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
    }
});

const form = useForm({
    title: props.event.title,
    description: props.event.description,
    publish_date: props.event.publish_date,
    deadline: props.event.deadline,
    status: props.event.status,
    platform_id: props.event.platform_id,
    category_id: props.event.category_id,
    assignees: props.event.assignees.map(a => a.id)
});

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
    form.put(route('calendar.update', props.event.id), {
        onSuccess: () => {
            // Redirect handled by controller
        }
    });
};
</script> 
<template>
    <Head title="Edit Task" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Edit Task
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="title" value="Judul Task" />
                                <TextInput
                                    id="title"
                                    v-model="form.title"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                    autofocus
                                />
                                <InputError :message="form.errors.title" class="mt-2" />
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

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="category_id" value="Kategori" />
                                    <SelectInput
                                        id="category_id"
                                        v-model="form.category_id"
                                        class="mt-1 block w-full"
                                        required
                                    >
                                        <option value="">Pilih Kategori</option>
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </SelectInput>
                                    <InputError :message="form.errors.category_id" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="platform_id" value="Platform" />
                                    <SelectInput
                                        id="platform_id"
                                        v-model="form.platform_id"
                                        class="mt-1 block w-full"
                                    >
                                        <option value="">Pilih Platform</option>
                                        <option v-for="platform in platforms" :key="platform.id" :value="platform.id">
                                            {{ platform.name }}
                                        </option>
                                    </SelectInput>
                                    <InputError :message="form.errors.platform_id" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="priority" value="Prioritas" />
                                    <SelectInput
                                        id="priority"
                                        v-model="form.priority"
                                        class="mt-1 block w-full"
                                        required
                                    >
                                        <option value="">Pilih Prioritas</option>
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                        <option value="urgent">Urgent</option>
                                    </SelectInput>
                                    <InputError :message="form.errors.priority" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="status" value="Status" />
                                    <SelectInput
                                        id="status"
                                        v-model="form.status"
                                        class="mt-1 block w-full"
                                        required
                                    >
                                        <option value="">Pilih Status</option>
                                        <option value="draft">Draft</option>
                                        <option value="in_review">In Review</option>
                                        <option value="approved">Approved</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </SelectInput>
                                    <InputError :message="form.errors.status" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="start_date" value="Tanggal Mulai" />
                                    <TextInput
                                        id="start_date"
                                        v-model="form.start_date"
                                        type="datetime-local"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError :message="form.errors.start_date" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="due_date" value="Tenggat Waktu" />
                                    <TextInput
                                        id="due_date"
                                        v-model="form.due_date"
                                        type="datetime-local"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError :message="form.errors.due_date" class="mt-2" />
                                </div>
                            </div>

                            <div>
                                <InputLabel for="assignees" value="Assignee" />
                                <select
                                    id="assignees"
                                    v-model="form.assignees"
                                    multiple
                                    class="mt-1 block w-full px-4 py-2 text-sm border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-400 focus:ring-primary-500 dark:focus:ring-primary-400"
                                    required
                                >
                                    <option v-for="user in users" :key="user.id" :value="user.id">
                                        {{ user.name }}
                                    </option>
                                </select>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Tekan CTRL (Windows) atau CMD (Mac) untuk memilih lebih dari satu
                                </p>
                                <InputError :message="form.errors.assignees" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end gap-4">
                                <SecondaryButton
                                    :href="route('tasks.index')"
                                    variant="outline"
                                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                >
                                    Batal
                                </SecondaryButton>

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
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import SelectInput from '@/Components/SelectInput.vue';

const props = defineProps({
    task: {
        type: Object,
        required: true
    },
    categories: {
        type: Array,
        required: true
    },
    platforms: {
        type: Array,
        required: true
    },
    users: {
        type: Array,
        required: true
    }
});

const form = useForm({
    title: props.task.title,
    description: props.task.description,
    category_id: props.task.category_id,
    platform_id: props.task.platform_id,
    priority: props.task.priority,
    status: props.task.status,
    start_date: props.task.start_date,
    due_date: props.task.due_date,
    assignees: props.task.assignees.map(assignee => assignee.id)
});

const submit = () => {
    form.put(route('tasks.update', props.task.id));
};
</script> 
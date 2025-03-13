<template>
    <Head title="Tasks" />

    <AuthenticatedLayout :auth="auth" title="Tambah Task">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Tambah Task
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
                                    <InputLabel for="team_id" value="Tim" />
                                    <SelectInput
                                        id="team_id"
                                        v-model="form.team_id"
                                        class="mt-1 block w-full"
                                    >
                                        <option value="">Pilih Tim</option>
                                        <option v-for="team in teams" :key="team.id" :value="team.id">
                                            {{ team.name }}
                                        </option>
                                    </SelectInput>
                                    <InputError :message="form.errors.team_id" class="mt-2" />
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
                                <InputLabel for="status" value="Status" />
                                <div class="mt-1 grid grid-cols-2 md:grid-cols-4 gap-3">
                                    <button
                                        type="button"
                                        v-for="status in statuses"
                                        :key="status.value"
                                        @click="form.status = status.value"
                                        class="p-3 rounded-lg text-sm font-medium transition-all duration-200 flex items-center justify-center gap-2"
                                        :class="[
                                            form.status === status.value
                                                ? status.activeClass
                                                : status.inactiveClass,
                                            'hover:shadow-md'
                                        ]"
                                    >
                                        <span :class="status.iconClass" v-if="status.icon">
                                            <i :class="status.icon"></i>
                                        </span>
                                        {{ status.label }}
                                    </button>
                                </div>
                                <InputError :message="form.errors.status" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="assignees" value="Assignee" />
                                <select
                                    id="assignees"
                                    v-model="form.assignees"
                                    multiple
                                    class="mt-1 block w-full px-4 py-2 text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-400 focus:ring-primary-500 dark:focus:ring-primary-400 rounded-lg shadow-sm"
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
                                <Link
                                    :href="route('tasks.index')"
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
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import SelectInput from '@/Components/SelectInput.vue';

const props = defineProps({
    categories: {
        type: Array,
        required: true
    },
    platforms: {
        type: Array,
        required: true
    },
    teams: {
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

const statuses = [
    {
        value: 'draft',
        label: 'Draft',
        activeClass: 'bg-gray-600 text-white',
        inactiveClass: 'bg-gray-100 text-gray-600 hover:bg-gray-200',
        icon: 'fas fa-file-alt'
    },
    {
        value: 'in_progress',
        label: 'In Progress',
        activeClass: 'bg-purple-500 text-white',
        inactiveClass: 'bg-purple-100 text-purple-600 hover:bg-purple-200',
        icon: 'fas fa-spinner'
    },
    {
        value: 'completed',
        label: 'Completed',
        activeClass: 'bg-green-500 text-white',
        inactiveClass: 'bg-green-100 text-green-600 hover:bg-green-200',
        icon: 'fas fa-check'
    },
    {
        value: 'cancelled',
        label: 'Cancelled',
        activeClass: 'bg-red-500 text-white',
        inactiveClass: 'bg-red-100 text-red-600 hover:bg-red-200',
        icon: 'fas fa-times'
    }
];

const form = useForm({
    title: '',
    description: '',
    category_id: '',
    platform_id: '',
    team_id: '',
    priority: '',
    status: '',
    start_date: '',
    due_date: '',
    assignees: []
});

const submit = () => {
    form.post(route('tasks.store'), {
        onSuccess: () => {
            // Redirect ke halaman index dengan filter status yang sesuai
            window.location = route('tasks.index', { 
                status: form.status,
                highlight: 'new' // Parameter tambahan untuk highlight task baru
            });
        },
    });
};
</script>

<style scoped>
.status-button {
    transition: all 0.2s ease;
}

.status-button:hover {
    transform: translateY(-1px);
}
</style> 
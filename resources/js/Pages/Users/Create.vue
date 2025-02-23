<template>
    <Head title="Create User" />

    <AuthenticatedLayout :auth="auth" title="Tambah Pengguna">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-lg md:text-xl font-semibold text-[var(--text-primary)] truncate">
                    Tambah Pengguna Baru
                </h2>
            </div>
        </template>

        <Card class="max-w-2xl mx-auto">
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <InputLabel for="name" value="Nama Lengkap" />
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
                    <InputLabel for="email" value="Email" />
                    <TextInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="mt-1 block w-full"
                        required
                    />
                    <InputError :message="form.errors.email" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="phone" value="Nomor WhatsApp" />
                    <TextInput
                        id="phone"
                        v-model="form.phone"
                        type="text"
                        class="mt-1 block w-full"
                        required
                    />
                    <InputError :message="form.errors.phone" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="password" value="Password" />
                    <TextInput
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full"
                        required
                    />
                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="password_confirmation" value="Konfirmasi Password" />
                    <TextInput
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        class="mt-1 block w-full"
                        required
                    />
                    <InputError :message="form.errors.password_confirmation" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="roles" value="Role" />
                    <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label v-for="role in roles" :key="role.id" class="inline-flex items-center space-x-3 p-2 hover:bg-[var(--bg-secondary)]/50 rounded-lg cursor-pointer">
                            <input
                                type="checkbox"
                                :value="role.name"
                                v-model="form.roles"
                                class="w-5 h-5 rounded border-[var(--border-primary)] bg-[var(--bg-secondary)] text-[var(--primary-600)] focus:ring-[var(--primary-500)]"
                            />
                            <span class="text-sm font-medium text-[var(--text-primary)]">{{ getRoleLabel(role.name) }}</span>
                        </label>
                    </div>
                    <InputError :message="form.errors.roles" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="status" value="Status" />
                    <select
                        id="status"
                        v-model="form.status"
                        class="mt-1 block w-full rounded-lg border-[var(--border-primary)] bg-[var(--bg-secondary)] text-[var(--text-primary)] focus:border-[var(--primary-500)] focus:ring-[var(--primary-500)]"
                        required
                    >
                        <option v-for="status in statusOptions" 
                                :key="status.value" 
                                :value="status.value"
                                class="bg-[var(--bg-secondary)] text-[var(--text-primary)]"
                        >
                            {{ status.label }}
                        </option>
                    </select>
                    <InputError :message="form.errors.status" class="mt-2" />
                </div>

                <div class="flex justify-end gap-3">
                    <Link
                        :href="route('admin.users.index')"
                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                    >
                        Batal
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium rounded-lg transition-colors duration-200 disabled:opacity-25"
                    >
                        {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </Card>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from '@/Components/Card.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    auth: {
        type: Object,
        required: true
    },
    roles: {
        type: Array,
        required: true,
        default: () => []
    }
});

const form = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
    roles: [],
    status: 'active'
});

// Helper untuk label role
const getRoleLabel = (roleName) => {
    const labels = {
        'admin': 'Administrator',
        'user': 'Pengguna',
    };
    return labels[roleName] || roleName;
};

// Status options dengan label yang lebih deskriptif
const statusOptions = [
    { value: 'pending', label: 'Pending' },
    { value: 'active', label: 'Aktif' },
    { value: 'inactive', label: 'Nonaktif' }
];

const submit = () => {
    form.post(route('admin.users.store'), {
        preserveScroll: true
    });
};
</script>

<style scoped>
/* Style untuk select dan option dalam dark mode */
select option {
    @apply bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100;
}

/* Hover effect untuk checkbox container */
.checkbox-container:hover {
    @apply bg-gray-50 dark:bg-gray-800;
}
</style> 
<template>
    <Head title="Create Role" />

    <AuthenticatedLayout :auth="auth">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-lg md:text-xl font-semibold text-[var(--text-primary)] truncate">
                    Tambah Role Baru
                </h2>
                <Link
                    :href="route('admin.roles.create')"
                    class="inline-flex items-center"
                >
                    <PrimaryButton>
                        <span class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            <span class="ml-2">Tambah Role</span>
                        </span>
                    </PrimaryButton>
                </Link>
            </div>
        </template>

        <Card class="max-w-6xl mx-auto">
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <InputLabel for="name" value="Nama Role" />
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

                <!-- Permission Table -->
                <div class="mt-6">
                    <h3 class="text-lg font-medium text-[var(--text-primary)] mb-4">
                        Permissions
                    </h3>
                    
                    <!-- Search & Filter -->
                    <div class="mb-4">
                        <TextInput
                            v-model="searchQuery"
                            type="text"
                            placeholder="Cari permission..."
                            class="w-full md:w-64"
                        />
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-200/50 dark:border-gray-700/25">
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[var(--text-secondary)] uppercase tracking-wider">
                                        Module
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[var(--text-secondary)] uppercase tracking-wider">
                                        Permissions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(moduleConfig, moduleName) in modules" 
                                    :key="moduleName" 
                                    v-show="showModule(moduleName)" 
                                    class="border-b border-gray-200/50 dark:border-gray-700/25 hover:bg-[var(--bg-secondary)]/50"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-[var(--text-primary)]">
                                        {{ moduleConfig.label }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-3">
                                            <div v-for="action in moduleConfig.permissions" 
                                                :key="action"
                                                class="flex items-center gap-2"
                                            >
                                                <Checkbox
                                                    v-model:checked="form.permissions"
                                                    :value="getPermissionValue(moduleName, action)"
                                                    :name="getPermissionValue(moduleName, action)"
                                                />
                                                <span class="text-sm text-[var(--text-primary)]">
                                                    {{ formatPermissionLabel(action) }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 mt-6">
                    <Link
                        :href="route('admin.roles.index')"
                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                    >
                        Batal
                    </Link>
                    <button
                        type="submit"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium rounded-lg transition-colors duration-200 disabled:opacity-25"
                    >
                        Simpan
                    </button>
                </div>
            </form>
        </Card>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from '@/Components/Card.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    auth: {
        type: Object,
        required: true
    }
});

const form = useForm({
    name: '',
    permissions: []
});

const searchQuery = ref('');

// Definisi modul dan permission yang tersedia
const modules = {
    users: {
        label: 'Users',
        permissions: ['view users', 'create users', 'edit users', 'delete users']
    },
    roles: {
        label: 'Roles',
        permissions: ['view roles', 'create roles', 'edit roles', 'delete roles']
    },
    tasks: {
        label: 'Tasks',
        permissions: ['view tasks', 'create tasks', 'edit tasks', 'delete tasks', 'assign tasks', 'change task status']
    },
    content: {
        label: 'Content',
        permissions: ['view content', 'create content', 'edit content', 'delete content', 'approve content']
    },
    teams: {
        label: 'Teams',
        permissions: ['view teams', 'create teams', 'edit teams', 'delete teams', 'manage team members']
    },
    calendar: {
        label: 'Calendar',
        permissions: ['view calendar', 'create calendar events', 'edit calendar events', 'delete calendar events']
    },
    assets: {
        label: 'Assets',
        permissions: ['view assets', 'upload assets', 'edit assets', 'delete assets']
    },
    reports: {
        label: 'Reports',
        permissions: ['view reports', 'export reports']
    },
    analytics: {
        label: 'Analytics',
        permissions: ['view analytics']
    },
    admin: {
        label: 'Admin',
        permissions: ['access admin', 'manage settings', 'manage all']
    }
};

// Helper untuk format permission
const formatPermission = (permission) => {
    return permission; // Tidak perlu format khusus karena sudah dalam format yang benar
};

// Method untuk filter module berdasarkan search query
const showModule = (moduleName) => {
    if (!searchQuery.value) return true;
    return moduleName.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
           form.permissions.some(permission => 
               permission.toLowerCase().includes(searchQuery.value.toLowerCase())
           );
};

// Update checkbox values dengan format yang benar
const getPermissionValue = (moduleName, action) => {
    return action; // Gunakan action langsung karena sudah include nama module
};

// Tambahkan fungsi formatPermissionLabel
const formatPermissionLabel = (action) => {
    const labels = {
        'view users': 'Lihat Pengguna',
        'create users': 'Tambah Pengguna',
        'edit users': 'Edit Pengguna',
        'delete users': 'Hapus Pengguna',
        'view roles': 'Lihat Role',
        'create roles': 'Tambah Role',
        'edit roles': 'Edit Role',
        'delete roles': 'Hapus Role',
        'view tasks': 'Lihat Tugas',
        'create tasks': 'Tambah Tugas',
        'edit tasks': 'Edit Tugas',
        'delete tasks': 'Hapus Tugas',
        'assign tasks': 'Tugaskan',
        'change task status': 'Ubah Status Tugas',
        'view content': 'Lihat Konten',
        'create content': 'Tambah Konten',
        'edit content': 'Edit Konten',
        'delete content': 'Hapus Konten',
        'approve content': 'Setujui Konten',
        'view teams': 'Lihat Tim',
        'create teams': 'Tambah Tim',
        'edit teams': 'Edit Tim',
        'delete teams': 'Hapus Tim',
        'manage team members': 'Kelola Anggota Tim',
        'view calendar': 'Lihat Kalender',
        'create calendar events': 'Tambah Event Kalender',
        'edit calendar events': 'Edit Event Kalender',
        'delete calendar events': 'Hapus Event Kalender',
        'view assets': 'Lihat Aset',
        'upload assets': 'Unggah Aset',
        'edit assets': 'Edit Aset',
        'delete assets': 'Hapus Aset',
        'view reports': 'Lihat Laporan',
        'export reports': 'Ekspor Laporan',
        'view analytics': 'Lihat Analitik',
        'access admin': 'Akses Admin',
        'manage settings': 'Kelola Pengaturan',
        'manage all': 'Kelola Semua'
    };
    return labels[action] || action;
};

const submit = () => {
    // Validasi
    if (!form.name) {
        form.setError('name', 'Nama role wajib diisi');
        return;
    }

    if (form.permissions.length === 0) {
        form.setError('permissions', 'Minimal pilih satu permission');
        return;
    }

    const formData = {
        name: form.name,
        permissions: form.permissions // Kirim permissions apa adanya karena sudah dalam format yang benar
    };
    
    form.post(route('admin.roles.store'), formData, {
        onSuccess: () => {
            console.log('Role berhasil dibuat dengan permissions:', form.permissions);
        },
        onError: (errors) => {
            console.error('Error saat membuat role:', errors);
        }
    });
};
</script> 
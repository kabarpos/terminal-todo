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
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-[var(--text-primary)]">
                            Permissions
                        </h3>
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                @click="selectAllPermissions"
                                class="px-3 py-1 text-sm font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100 dark:text-blue-400 dark:bg-blue-900/50 dark:hover:bg-blue-900/75"
                            >
                                Select All
                            </button>
                            <button
                                type="button"
                                @click="unselectAllPermissions"
                                class="px-3 py-1 text-sm font-medium text-red-600 bg-red-50 rounded-md hover:bg-red-100 dark:text-red-400 dark:bg-red-900/50 dark:hover:bg-red-900/75"
                            >
                                Unselect All
                            </button>
                        </div>
                    </div>
                    
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
                                <tr v-for="(permissions, group) in filteredPermissions" 
                                    :key="group"
                                    class="border-b border-gray-200/50 dark:border-gray-700/25 hover:bg-[var(--bg-secondary)]/50"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-[var(--text-primary)]">
                                        {{ formatGroupLabel(group) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-3">
                                            <div v-for="permission in permissions" 
                                                :key="permission.id"
                                                class="flex items-center gap-2"
                                            >
                                                <Checkbox
                                                    v-model:checked="form.permissions"
                                                    :value="permission.name"
                                                    :name="permission.name"
                                                />
                                                <span class="text-sm text-[var(--text-primary)]">
                                                    {{ formatPermissionLabel(permission) }}
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
                        {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
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
    },
    permissions: {
        type: Object,
        required: true
    }
});

const form = useForm({
    name: '',
    permissions: []
});

const searchQuery = ref('');

// Filter permissions berdasarkan search query
const filteredPermissions = computed(() => {
    if (!searchQuery.value) return props.permissions;

    const query = searchQuery.value.toLowerCase();
    const filtered = {};

    Object.entries(props.permissions).forEach(([group, permissions]) => {
        const matchingPermissions = permissions.filter(permission => 
            permission.name.toLowerCase().includes(query)
        );

        if (matchingPermissions.length > 0) {
            filtered[group] = matchingPermissions;
        }
    });

    return filtered;
});

// Format label permission untuk tampilan yang lebih baik
const formatPermissionLabel = (permission) => {
    if (!permission || !permission.name) return '';
    
    // Normalisasi format permission - bisa dengan dash atau spasi
    let permName = permission.name;
    let formattedPermission = permName;
    
    if (permName.includes('-')) {
        formattedPermission = permName.replace(/-/g, ' ');
    }
    
    // Split permission menjadi action dan resource
    let parts = formattedPermission.split(' ');
    if (parts.length < 2) return formattedPermission; // Return as is jika tidak bisa dipisah
    
    let action = parts[0];
    let resource = parts.slice(1).join(' ');
    
    // Mapping untuk label yang lebih baik
    const actionLabels = {
        'view': 'Lihat',
        'create': 'Tambah',
        'edit': 'Edit',
        'delete': 'Hapus',
        'manage': 'Kelola',
        'export': 'Ekspor',
        'import': 'Impor'
    };

    const moduleLabels = {
        'users': 'Pengguna',
        'roles': 'Role',
        'tasks': 'Tugas',
        'teams': 'Tim',
        'calendar': 'Kalender',
        'category': 'Kategori',
        'platform': 'Platform',
        'newsfeed': 'Newsfeed',
        'social media report': 'Laporan Media Sosial',
        'asset': 'Aset',
        'metric data': 'Data Metrik',
        'analytics': 'Analitik',
        'settings': 'Pengaturan',
        'dashboard': 'Dashboard'
    };
    
    const actionLabel = actionLabels[action] || action;
    const moduleLabel = moduleLabels[resource] || resource;
    
    return `${actionLabel} ${moduleLabel}`;
};

// Format label group untuk tampilan yang lebih baik
const formatGroupLabel = (group) => {
    return group.charAt(0).toUpperCase() + group.slice(1);
};

// Tambahkan methods untuk select/unselect all
const selectAllPermissions = () => {
    // Pastikan mengambil semua permission name dari props
    const allPermissions = Object.values(props.permissions)
        .flat()
        .map(permission => permission.name);
    form.permissions = [...new Set(allPermissions)]; // Hapus duplikat jika ada
    console.log('Selected all permissions:', form.permissions);
};

const unselectAllPermissions = () => {
    form.permissions = [];
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

    // Log untuk debugging
    console.log('Submitting role with permissions:', form.permissions);

    form.post(route('admin.roles.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            // Redirect ke halaman index setelah berhasil
            window.location = route('admin.roles.index');
        },
        onError: (errors) => {
            console.error('Error creating role:', errors);
        }
    });
};
</script> 
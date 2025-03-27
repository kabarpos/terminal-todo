<template>
    <Head title="Create Role" />

    <AuthenticatedLayout :auth="auth" title="Tambah Role Baru">
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

                <!-- Permission Section -->
                <div class="mt-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-[var(--text-primary)]">
                                Permissions
                            </h3>
                            <p class="mt-1 text-sm text-[var(--text-secondary)]">
                                Pilih permission yang akan diberikan untuk role ini
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <TextInput
                                v-model="searchQuery"
                                type="search"
                                placeholder="Cari permission..."
                                class="w-full md:w-64"
                            />
                            <button
                                type="button"
                                @click="selectAllPermissions"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-700 bg-blue-50 rounded-md hover:bg-blue-100 dark:text-blue-400 dark:bg-blue-900/50 dark:hover:bg-blue-900/75 transition-colors duration-200"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                Pilih Semua
                            </button>
                            <button
                                type="button"
                                @click="unselectAllPermissions"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-700 bg-red-50 rounded-md hover:bg-red-100 dark:text-red-400 dark:bg-red-900/50 dark:hover:bg-red-900/75 transition-colors duration-200"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                Hapus Semua
                            </button>
                        </div>
                    </div>

                    <!-- Simple Permission List -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <template v-for="(permissions, group) in filteredPermissions" :key="group">
                                    <div v-for="permission in permissions" 
                                        :key="permission.id"
                                        class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200"
                                    >
                                        <Checkbox
                                            v-model:checked="form.permissions"
                                            :value="permission.name"
                                            :id="`perm-${permission.id}`"
                                            class="rounded-md"
                                        />
                                        <label :for="`perm-${permission.id}`" class="text-sm text-[var(--text-primary)] cursor-pointer select-none">
                                            {{ formatPermissionLabel(permission) }}
                                        </label>
                                    </div>
                                </template>
                            </div>
                        </div>
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
const expandedGroups = ref([]);

// Filter permissions berdasarkan search query
const filteredPermissions = computed(() => {
    if (!searchQuery.value) return props.permissions;

    const query = searchQuery.value.toLowerCase();
    const filtered = {};

    Object.entries(props.permissions).forEach(([group, permissions]) => {
        const matchingPermissions = permissions.filter(permission => 
            permission.name.toLowerCase().includes(query) || 
            formatPermissionLabel(permission).toLowerCase().includes(query)
        );

        if (matchingPermissions.length > 0) {
            filtered[group] = matchingPermissions;
            // Auto-expand groups when searching
            if (!expandedGroups.value.includes(group)) {
                expandedGroups.value.push(group);
            }
        }
    });

    return filtered;
});

// Toggle accordion group
const toggleGroup = (group) => {
    const index = expandedGroups.value.indexOf(group);
    if (index === -1) {
        expandedGroups.value.push(group);
    } else {
        expandedGroups.value.splice(index, 1);
    }
};

// Check if all permissions in a group are selected
const isGroupChecked = (permissions) => {
    if (!permissions.length) return false;
    return permissions.every(permission => form.permissions.includes(permission.name));
};

// Get count of selected permissions in a group
const getSelectedCount = (permissions) => {
    return permissions.filter(permission => form.permissions.includes(permission.name)).length;
};

// Toggle all permissions in a group
const toggleGroupPermissions = (permissions, checked) => {
    const permissionNames = permissions.map(p => p.name);
    
    if (checked) {
        // Add all permissions from this group
        permissionNames.forEach(name => {
            if (!form.permissions.includes(name)) {
                form.permissions.push(name);
            }
        });
    } else {
        // Remove all permissions from this group
        form.permissions = form.permissions.filter(name => !permissionNames.includes(name));
    }
};

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
        'social platform': 'Platform Sosial',
        'social account': 'Akun Sosial',
        'media': 'Media',
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

    form.post(route('admin.roles.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            // Redirect ke halaman index setelah berhasil
            window.location = route('admin.roles.index');
        }
    });
};
</script> 
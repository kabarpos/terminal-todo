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

                    <!-- Permission Accordion -->
                    <div class="space-y-4">
                        <div v-for="(permissions, group) in filteredPermissions" :key="group" 
                            class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                            <div @click="toggleGroup(group)" 
                                class="flex items-center justify-between bg-gray-50 dark:bg-gray-800 px-6 py-3 cursor-pointer">
                                <div class="flex items-center gap-3">
                                    <Checkbox 
                                        :id="`group-${group}`"
                                        :checked="isGroupChecked(permissions)"
                                        @update:checked="toggleGroupPermissions(permissions, $event)"
                                        @click.stop 
                                    />
                                    <span class="font-medium text-[var(--text-primary)]">{{ formatGroupLabel(group) }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-[var(--text-secondary)]">
                                        {{ getSelectedCount(permissions) }}/{{ permissions.length }} permissions
                                    </span>
                                    <svg :class="{'rotate-180': expandedGroups.includes(group)}" 
                                        class="w-5 h-5 transition-transform duration-200 text-gray-500 dark:text-gray-400" 
                                        xmlns="http://www.w3.org/2000/svg" 
                                        viewBox="0 0 24 24" 
                                        fill="none" 
                                        stroke="currentColor" 
                                        stroke-width="2" 
                                        stroke-linecap="round" 
                                        stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </div>
                            </div>
                            
                            <div v-show="expandedGroups.includes(group)" class="px-6 py-4 bg-white dark:bg-gray-900">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div v-for="permission in permissions" 
                                        :key="permission.id"
                                        class="flex items-center gap-2 p-2 hover:bg-gray-50 dark:hover:bg-gray-800 rounded"
                                    >
                                        <Checkbox
                                            v-model:checked="form.permissions"
                                            :value="permission.name"
                                            :id="`perm-${permission.id}`"
                                        />
                                        <label :for="`perm-${permission.id}`" class="text-sm text-[var(--text-primary)] cursor-pointer">
                                            {{ formatPermissionLabel(permission) }}
                                        </label>
                                    </div>
                                </div>
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
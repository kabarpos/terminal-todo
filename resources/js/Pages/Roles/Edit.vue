<template>
    <Head title="Edit Role" />

    <AuthenticatedLayout :auth="auth" title="Edit Role">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-lg md:text-xl font-semibold text-[var(--text-primary)] truncate">
                    Edit Role
                </h2>
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

                    <!-- Permission Groups -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <template v-for="(permissions, group) in groupedPermissions" :key="group">
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                                <!-- Group Header -->
                                <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-sm font-medium text-[var(--text-primary)]">
                                            {{ formatGroupLabel(group) }}
                                        </h4>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-[var(--text-secondary)]">
                                                {{ getSelectedCount(permissions) }}/{{ permissions.length }}
                                            </span>
                                            <button
                                                type="button"
                                                @click="toggleGroupPermissions(permissions, !isGroupChecked(permissions))"
                                                class="p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700/50"
                                            >
                                                <svg 
                                                    class="w-4 h-4" 
                                                    :class="isGroupChecked(permissions) ? 'text-blue-500' : 'text-gray-400'"
                                                    fill="none" 
                                                    stroke="currentColor" 
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path 
                                                        stroke-linecap="round" 
                                                        stroke-linejoin="round" 
                                                        stroke-width="2" 
                                                        d="M5 13l4 4L19 7"
                                                    />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Permission List -->
                                <div class="p-4 space-y-2">
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
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 mt-6">
                    <Link
                        :href="route('admin.roles.index')"
                        class="px-4 py-2 text-sm font-medium text-[var(--text-primary)] bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                    >
                        Batal
                    </Link>
                    <PrimaryButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white transition-all duration-200"
                    >
                        Simpan Perubahan
                    </PrimaryButton>
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
    role: {
        type: Object,
        required: true
    },
    permissions: {
        type: Object,
        required: true
    }
});

const form = useForm({
    name: props.role.name,
    permissions: props.role.permissions || []
});

const searchQuery = ref('');
const expandedGroups = ref([]);

// Tambahkan computed property untuk mengelompokkan permission
const groupedPermissions = computed(() => {
    if (!searchQuery.value) {
        // Kelompokkan permission berdasarkan fitur
        const groups = {};
        
        Object.entries(props.permissions).forEach(([group, permissions]) => {
            permissions.forEach(permission => {
                const feature = getFeatureFromPermission(permission.name);
                if (!groups[feature]) {
                    groups[feature] = [];
                }
                groups[feature].push(permission);
            });
        });
        
        return groups;
    }

    // Jika ada pencarian, tampilkan hasil pencarian dengan pengelompokan yang sama
    const query = searchQuery.value.toLowerCase();
    const filtered = {};

    Object.entries(props.permissions).forEach(([group, permissions]) => {
        permissions.forEach(permission => {
            if (
                permission.name.toLowerCase().includes(query) || 
                formatPermissionLabel(permission).toLowerCase().includes(query)
            ) {
                const feature = getFeatureFromPermission(permission.name);
                if (!filtered[feature]) {
                    filtered[feature] = [];
                }
                filtered[feature].push(permission);
            }
        });
    });

    return filtered;
});

// Fungsi untuk mendapatkan nama fitur dari permission
const getFeatureFromPermission = (permissionName) => {
    // Mapping khusus untuk menu di sidebar
    const sidebarMapping = {
        'dashboard': 'Dashboard',
        'calendar': 'Calendar',
        'task': 'Tasks',
        'team': 'Teams',
        'category': 'Categories',
        'platform': 'Platforms',
        'social-platform': 'Social Platforms',
        'social-account': 'Social Accounts',
        'newsfeed': 'News Feed',
        'social-media-report': 'Reports',
        'media': 'Media Library',
        'metric-data': 'Input Metrik',
        'analytic': 'Analytics',
        'user': 'User Management',
        'role': 'Role Management',
        'setting': 'Settings'
    };
    
    // Cek apakah permission name cocok dengan salah satu menu sidebar
    for (const [key, value] of Object.entries(sidebarMapping)) {
        if (permissionName.includes(key)) {
            return value;
        }
    }
    
    // Cek khusus untuk social media report
    if (permissionName.includes('social-media-report')) {
        return 'Reports';
    }
    
    // Cek khusus untuk metric-data
    if (permissionName.includes('metric-data')) {
        return 'Input Metrik';
    }
    
    // Ekstrak nama fitur dari permission
    const parts = permissionName.split('-');
    if (parts.length < 2) return 'Other';
    
    // Mapping fitur yang lebih lengkap
    const featureMap = {
        // Manajemen Konten
        'task': 'Tasks',
        'comment': 'Comments',
        'post': 'Posts',
        'content': 'Contents',
        'article': 'Articles',
        
        // Media Library
        'media': 'Media Library',
        
        // Manajemen Pengguna & Akses
        'user': 'User Management',
        'role': 'Role Management',
        'permission': 'Permissions',
        'team': 'Teams',
        'member': 'Members',
        'profile': 'Profiles',
        'account': 'Social Accounts',
        
        // Pengaturan & Konfigurasi
        'setting': 'Settings',
        'config': 'Configurations',
        'preference': 'Preferences',
        'notification': 'Notifications',
        
        // Kategori & Platform
        'category': 'Categories',
        'platform': 'Platforms',
        'channel': 'Channels',
        
        // Analytics & Reports
        'report': 'Reports',
        'analytic': 'Analytics',
        'metric': 'Input Metrik',
        'statistic': 'Reports',
        'dashboard': 'Dashboard',
        'insight': 'Analytics',
        'export': 'Reports',
        
        // Social Media
        'social': 'Social Platforms',
        'newsfeed': 'News Feed',
        'feed': 'News Feed',
        
        // System & Tools
        'system': 'System',
        'tool': 'Tools',
        'log': 'Logs',
        'backup': 'Backups',
        'import': 'Input Metrik',
        'export': 'Reports',
        'api': 'API',
        
        // Calendar & Schedule
        'calendar': 'Calendar',
        'schedule': 'Calendar',
        'event': 'Calendar',
    };
    
    // Cari kata kunci fitur dalam permission dengan prioritas
    // 1. Cek exact match untuk full permission name (e.g., "manage-tasks")
    const fullMatch = Object.entries(featureMap).find(([key, value]) => 
        permissionName.includes(`-${key}`) || permissionName.includes(`${key}-`)
    );
    if (fullMatch) return fullMatch[1];
    
    // 2. Cek setiap kata dalam permission name
    for (const word of parts) {
        const match = Object.entries(featureMap).find(([key]) => 
            word.toLowerCase() === key.toLowerCase()
        );
        if (match) return match[1];
    }
    
    // 3. Cek substring dalam permission name
    for (const [key, value] of Object.entries(featureMap)) {
        if (permissionName.toLowerCase().includes(key.toLowerCase())) {
            return value;
        }
    }
    
    // Jika masih tidak ada yang cocok, coba ekstrak resource name
    const resourceName = parts.slice(1).join('-');
    if (resourceName) {
        // Capitalize resource name
        return resourceName.split('-')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    }
    
    return 'Other';
};

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
        'dashboard': 'Dashboard',
        'calendar': 'Kalender',
        'task': 'Tugas',
        'team': 'Tim',
        'category': 'Kategori',
        'platform': 'Platform',
        'social platform': 'Platform Sosial',
        'social account': 'Akun Sosial',
        'newsfeed': 'News Feed',
        'social media report': 'Laporan Media Sosial',
        'media': 'Media Library',
        'metric data': 'Data Metrik',
        'metric': 'Metrik',
        'analytics': 'Analitik',
        'users': 'Pengguna',
        'user': 'Pengguna',
        'roles': 'Role',
        'role': 'Role',
        'settings': 'Pengaturan',
        'setting': 'Pengaturan',
        'news feed': 'News Feed',
        'reports': 'Laporan',
        'report': 'Laporan',
        'social platforms': 'Platform Sosial',
        'social accounts': 'Akun Sosial',
        'input metrik': 'Input Metrik'
    };
    
    const actionLabel = actionLabels[action] || action;
    const moduleLabel = moduleLabels[resource.toLowerCase()] || resource;
    
    return `${actionLabel} ${moduleLabel}`;
};

// Format label group untuk tampilan yang lebih baik
const formatGroupLabel = (group) => {
    const groupLabels = {
        'Dashboard': 'Dashboard',
        'Calendar': 'Kalender',
        'Tasks': 'Tugas',
        'Teams': 'Tim',
        'Categories': 'Kategori',
        'Platforms': 'Platform',
        'Social Platforms': 'Platform Sosial',
        'Social Accounts': 'Akun Sosial',
        'News Feed': 'News Feed',
        'Reports': 'Laporan',
        'Media Library': 'Media Library',
        'Input Metrik': 'Input Metrik',
        'Analytics': 'Analitik',
        'User Management': 'Manajemen User',
        'Role Management': 'Manajemen Role',
        'Settings': 'Pengaturan',
        'Other': 'Lainnya'
    };
    
    return groupLabels[group] || group;
};

// Perbaiki fungsi selectAllPermissions
const selectAllPermissions = () => {
    const allPermissions = Object.values(props.permissions)
        .flat()
        .map(permission => permission.name);
    form.permissions = [...new Set(allPermissions)];
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

    form.put(route('admin.roles.update', props.role.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Redirect ke halaman index setelah berhasil
            window.location = route('admin.roles.index');
        }
    });
};
</script> 
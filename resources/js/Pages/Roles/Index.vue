<template>
    <Head title="Roles" />

    <AuthenticatedLayout :auth="auth" title="Manajemen Role">
        <template #header>
            <div class="w-full">
                <div class="flex items-center justify-between">
                    <!-- Title dengan ukuran yang lebih kecil -->
                    <h2 class="text-lg md:text-xl font-semibold text-[var(--text-primary)] truncate">
                        Manajemen Role
                    </h2>
                    
                    <!-- Tombol responsif -->
                    <Link
                        :href="route('admin.roles.create')"
                        class="inline-flex items-center px-3 py-2 md:px-4 md:py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
                    >
                        <span class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            <span class="hidden md:inline ml-2">Tambah Role</span>
                        </span>
                    </Link>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Search and Filter Section -->
            <Card class="p-4">
                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Search bar -->
                    <div class="flex-1">
                        <TextInput
                            v-model="search"
                            type="search"
                            class="w-full"
                            placeholder="Cari role..."
                        />
                    </div>
                </div>
            </Card>

            <!-- Roles Table -->
            <Card>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-200/50 dark:border-gray-700/25">
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[var(--text-secondary)] uppercase tracking-wider">
                                    Nama Role
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[var(--text-secondary)] uppercase tracking-wider">
                                    Permissions
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[var(--text-secondary)] uppercase tracking-wider">
                                    Total User
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="role in filteredRoles" :key="role.id" class="border-b border-gray-200/50 dark:border-gray-700/25 hover:bg-[var(--bg-secondary)]/50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-[var(--text-primary)]">
                                        {{ role.name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        <Badge 
                                            v-for="permission in role.permissions" 
                                            :key="permission"
                                            variant="primary"
                                            size="sm"
                                            class="whitespace-nowrap"
                                        >
                                            {{ formatPermissionLabel(permission) }}
                                        </Badge>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <Badge variant="success">
                                        {{ role.users_count }} User
                                    </Badge>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-3">
                                        <div class="inline-flex rounded-md">
                                            <Link
                                                :href="route('admin.roles.edit', role.id)"
                                                class="inline-flex items-center px-2 sm:px-3 py-1 sm:py-1.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-xs sm:text-sm font-medium rounded-l-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
                                            >
                                                <PencilSquareIcon class="w-4 h-4 mr-1" />
                                                Edit
                                            </Link>
                                            <button
                                                v-if="role.name !== 'admin'"
                                                @click="confirmRoleDeletion(role)"
                                                class="inline-flex items-center px-2 sm:px-3 py-1 sm:py-1.5 bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white text-xs sm:text-sm font-medium rounded-r-lg border-l-0 transition-colors duration-200"
                                            >
                                                <TrashIcon class="w-4 h-4 mr-1" />
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </Card>
        </div>

        <!-- Delete Role Modal -->
        <Modal :show="confirmingRoleDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-[var(--text-primary)]">
                    Hapus Role
                </h2>

                <p class="mt-3 text-sm text-[var(--text-secondary)]">
                    Apakah Anda yakin ingin menghapus role ini? Semua user yang terkait dengan role ini akan kehilangan akses yang diberikan oleh role ini.
                </p>

                <div class="mt-6 flex justify-end gap-4">
                    <button 
                        @click="closeModal" 
                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                    >
                        Batal
                    </button>

                    <button
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteRole"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-medium rounded-lg transition-colors duration-200 disabled:opacity-25"
                    >
                        Hapus Role
                    </button>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';
import TextInput from '@/Components/TextInput.vue';
import { PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    auth: {
        type: Object,
        required: true
    },
    roles: {
        type: Array,
        required: true
    }
});

const confirmingRoleDeletion = ref(false);
const selectedRole = ref(null);
const search = ref('');

const form = useForm({});

// Fungsi untuk memformat label permission
const formatPermissionLabel = (permission) => {
    if (!permission) return '';
    
    // Handle undefined case
    if (permission.startsWith('undefined ')) {
        permission = permission.replace('undefined ', '');
    }
    
    // Normalisasi format permission - bisa dengan dash atau spasi
    let formattedPermission = permission;
    if (permission.includes('-')) {
        formattedPermission = permission.replace(/-/g, ' ');
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
        'import': 'Impor',
        'assign': 'Tugaskan',
        'approve': 'Setujui'
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

// Helper untuk mengelompokkan permission berdasarkan modul
const groupPermissionsByModule = (permissions) => {
    if (!permissions) return {};
    
    const grouped = {};
    permissions.forEach(permission => {
        let module;
        if (permission.includes('.')) {
            [module] = permission.split('.');
        } else if (permission.includes(' ')) {
            const parts = permission.split(' ');
            module = parts[parts.length - 1];
        } else {
            module = 'other';
        }
        
        if (!grouped[module]) {
            grouped[module] = [];
        }
        grouped[module].push(permission);
    });
    return grouped;
};

// Computed property untuk filtered roles dengan format yang benar
const filteredRoles = computed(() => {
    if (!search.value) return props.roles;
    
    const searchTerm = search.value.toLowerCase();
    return props.roles.filter(role => {
        if (!role) return false;
        
        const nameMatch = role.name.toLowerCase().includes(searchTerm);
        const permissionMatch = role.permissions && role.permissions.some(permission => {
            const formattedLabel = formatPermissionLabel(permission).toLowerCase();
            return formattedLabel.includes(searchTerm);
        });
        
        return nameMatch || permissionMatch;
    });
});

const confirmRoleDeletion = (role) => {
    selectedRole.value = role;
    confirmingRoleDeletion.value = true;
};

const closeModal = () => {
    confirmingRoleDeletion.value = false;
};

const deleteRole = () => {
    if (selectedRole.value) {
        if (selectedRole.value.name.toLowerCase() === 'admin') {
            alert('Role admin tidak dapat dihapus');
            return;
        }

        form.delete(route('admin.roles.destroy', selectedRole.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                console.log('Role berhasil dihapus');
            },
            onError: (error) => {
                console.error('Error saat menghapus role:', error);
            }
        });
    }
};
</script> 
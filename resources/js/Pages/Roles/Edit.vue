<template>
    <Head title="Edit Role" />

    <AuthenticatedLayout :auth="auth">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-[var(--text-primary)]">
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
                                                    :value="action"
                                                    :name="action"
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
import { ref, onMounted } from 'vue';
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
    }
});

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

// Fungsi untuk debug yang lebih detail
const logPermissions = (stage, permissions) => {
    console.log(`\n=== ${stage} ===`);
    console.log('Type:', typeof permissions);
    console.log('Is Array:', Array.isArray(permissions));
    console.log('Raw Value:', permissions);
    if (typeof permissions === 'object') {
        console.log('Keys:', Object.keys(permissions));
        console.log('Values:', Object.values(permissions));
    }
    console.log('=============\n');
};

// Fungsi untuk memformat permission dari backend
const formatPermissionsFromBackend = (permissions) => {
    logPermissions('Input', permissions);
    
    if (!permissions) {
        console.log('No permissions provided');
        return [];
    }

    // Log format yang diharapkan
    const expectedPermissions = Object.keys(modules).map(module => 
        modules[module].permissions
    ).flat();
    console.log('Expected format for each permission:', expectedPermissions);

    let formattedPermissions = [];
    
    try {
        if (Array.isArray(permissions)) {
            formattedPermissions = permissions.reduce((acc, p) => {
                // Pastikan p adalah string atau bisa dikonversi ke string
                const permString = String(p?.name || p);
                if (permString.includes('.')) {
                    const [module, action] = permString.split('.');
                    acc.push(`${action} ${module}`);
                } else {
                    acc.push(permString);
                }
                return acc;
            }, []);
        } else if (typeof permissions === 'object' && permissions !== null) {
            // Handle kasus dimana permissions adalah object
            const permArray = permissions.name ? [permissions.name] : Object.values(permissions);
            formattedPermissions = permArray.reduce((acc, p) => {
                const permString = String(p);
                if (permString.includes('.')) {
                    const [module, action] = permString.split('.');
                    acc.push(`${action} ${module}`);
                } else {
                    acc.push(permString);
                }
                return acc;
            }, []);
        } else if (typeof permissions === 'string') {
            if (permissions.includes('.')) {
                const [module, action] = permissions.split('.');
                formattedPermissions = [`${action} ${module}`];
            } else {
                formattedPermissions = [permissions];
            }
        }
    } catch (error) {
        console.error('Error formatting permissions:', error);
        console.log('Problematic permissions:', permissions);
    }

    console.log('Pre-filtered permissions:', formattedPermissions);
    
    // Filter hanya permission yang valid dan unik
    const validPermissions = [...new Set(formattedPermissions)].filter(p => 
        expectedPermissions.includes(p)
    );
    
    console.log('Final formatted permissions:', validPermissions);
    return validPermissions;
};

const searchQuery = ref('');

const form = useForm({
    name: props.role.name || '',
    permissions: formatPermissionsFromBackend(props.role.permissions)
});

// Log initial state
logPermissions('Initial Form', form.permissions);

onMounted(() => {
    logPermissions('Mounted', form.permissions);
    console.log('Role data:', props.role);
});

// Method untuk filter module berdasarkan search query
const showModule = (moduleName) => {
    if (!searchQuery.value) return true;
    return moduleName.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
           form.permissions.some(permission => 
               permission.toLowerCase().includes(searchQuery.value.toLowerCase())
           );
};

// Fungsi untuk memformat label permission
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
    logPermissions('Submit', form.permissions);
    console.log('Form Data:', {
        name: form.name,
        permissions: form.permissions
    });
    
    form.put(route('admin.roles.update', props.role.id), {
        preserveScroll: true,
        onSuccess: (response) => {
            logPermissions('Success', form.permissions);
            console.log('Response:', response);
        },
        onError: (errors) => {
            console.error('Error updating role:', errors);
        }
    });
};
</script> 
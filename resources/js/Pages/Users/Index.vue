<template>
    <Head title="Users" />

    <AuthenticatedLayout :auth="auth" title="Manajemen Pengguna">
        <template #header>
            <div class="w-full">
                <div class="flex items-center justify-between">
                    <!-- Title dengan ukuran yang lebih kecil -->
                    <h2 class="text-lg md:text-xl font-semibold text-[var(--text-primary)] truncate">
                        Manajemen Pengguna
                    </h2>
                    
                    <!-- Tombol responsif -->
                    <Link
                        :href="route('admin.users.create')"
                        class="inline-flex items-center px-3 py-2 md:px-4 md:py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
                    >
                        <span class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            <span class="hidden md:inline ml-2">Tambah Pengguna</span>
                        </span>
                    </Link>
                </div>
            </div>
        </template>

        <div class="space-y-4 sm:space-y-6">
            <!-- Search and Filter Section -->
            <Card class="p-4">
                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Search bar -->
                    <div class="w-full md:flex-1">
                        <TextInput
                            v-model="search"
                            type="search"
                            class="w-full"
                            placeholder="Cari pengguna..."
                        />
                    </div>
                    
                    <!-- Filter buttons -->
                    <div class="flex flex-col md:flex-row gap-3">
                        <div class="relative">
                            <select
                                v-model="filters.role"
                                class="w-full md:w-40 appearance-none rounded-lg border border-gray-200/50 dark:border-gray-700/25 bg-[var(--bg-primary)] text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] focus:border-[var(--primary-500)] focus:ring-[var(--primary-500)] pl-3 pr-10 py-2 cursor-pointer transition-colors duration-200"
                            >
                                <option value="" class="py-2">Semua Role</option>
                                <option 
                                    v-for="role in roles" 
                                    :key="role" 
                                    :value="role" 
                                    class="py-2"
                                >
                                    {{ getRoleLabel(role) }}
                                </option>
                            </select>
                           
                        </div>
                        <div class="relative">
                            <select
                                v-model="filters.status"
                                class="w-full md:w-40 appearance-none rounded-lg border border-gray-200/50 dark:border-gray-700/25 bg-[var(--bg-primary)] text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] focus:border-[var(--primary-500)] focus:ring-[var(--primary-500)] pl-3 pr-10 py-2 cursor-pointer transition-colors duration-200"
                            >
                                <option value="" class="py-2">Semua Status</option>
                                <option value="pending" class="py-2">Pending</option>
                                <option value="active" class="py-2">Aktif</option>
                                <option value="rejected" class="py-2">Ditolak</option>
                                <option value="banned" class="py-2">Diblokir</option>
                                <option value="inactive" class="py-2">Nonaktif</option>
                            </select>
                           
                        </div>
                    </div>
                </div>
            </Card>

            <!-- Users Table -->
            <Card>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <!-- Responsive table headers -->
                        <thead>
                            <tr class="border-b border-gray-200/50 dark:border-gray-700/25">
                                <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-[var(--text-secondary)] uppercase tracking-wider">
                                    Pengguna
                                </th>
                                <th scope="col" class="hidden sm:table-cell px-6 py-3 text-left text-xs font-medium text-[var(--text-secondary)] uppercase tracking-wider">
                                    Role
                                </th>
                                <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-[var(--text-secondary)] uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="hidden sm:table-cell px-6 py-3 text-left text-xs font-medium text-[var(--text-secondary)] uppercase tracking-wider">
                                    Bergabung
                                </th>
                                <th scope="col" class="relative px-4 sm:px-6 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>

                        <!-- Responsive table body -->
                        <tbody>
                            <tr v-for="user in filteredUsers" :key="user.id" class="border-b border-gray-200/50 dark:border-gray-700/25 hover:bg-[var(--bg-secondary)]/50">
                                <!-- User info - simplified for mobile -->
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                        <div class="h-8 w-8 sm:h-10 sm:w-10 flex-shrink-0">
                                                    <img 
                                                :src="user.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=random&color=fff`"
                                                        :alt="user.name"
                                                class="h-full w-full rounded-full object-cover"
                                            />
                                                </div>
                                        <div class="ml-3 sm:ml-4">
                                            <div class="text-sm font-medium text-[var(--text-primary)]">
                                                {{ user.name }}
                                            </div>
                                            <div class="text-xs sm:text-sm text-[var(--text-secondary)]">
                                                {{ user.email }}
                                            </div>
                                                </div>
                                            </div>
                                        </td>

                                <!-- Role - hidden on mobile -->
                                <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-wrap gap-2">
                                        <Badge 
                                                    v-for="role in user.roles" 
                                                    :key="role"
                                            variant="primary"
                                                >
                                                    {{ role }}
                                        </Badge>
                                            </div>
                                        </td>

                                <!-- Status -->
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                    <Badge 
                                        :variant="getStatusVariant(user.status)"
                                    >
                                        {{ getStatusLabel(user.status) }}
                                    </Badge>
                                </td>

                                <!-- Join date - hidden on mobile -->
                                <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm text-[var(--text-secondary)]">
                                    {{ formatDate(user.created_at) }}
                                        </td>

                                <!-- Actions -->
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2 sm:gap-3">
                                        <div class="inline-flex rounded-md">
                                            <Link
                                                :href="route('admin.users.edit', user.id)"
                                                class="inline-flex items-center px-2 sm:px-3 py-1 sm:py-1.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-xs sm:text-sm font-medium rounded-l-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
                                            >
                                                <PencilSquareIcon class="w-4 h-4 mr-1" />
                                                Edit
                                            </Link>
                                            <button
                                                @click="confirmUserDeletion(user)"
                                                class="inline-flex items-center px-2 sm:px-3 py-1 sm:py-1.5 bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white text-xs sm:text-sm font-medium rounded-r-lg border-l-0 transition-colors duration-200"
                                            >
                                                <TrashIcon class="w-4 h-4 mr-1" />
                                                Delete
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

        <!-- Delete User Modal -->
        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-[var(--text-primary)]">
                    Hapus Pengguna
                </h2>

                <p class="mt-3 text-sm text-[var(--text-secondary)]">
                    Apakah Anda yakin ingin menghapus pengguna ini? Semua data yang terkait dengan pengguna ini akan dihapus secara permanen.
                </p>

                <div class="mt-6 flex justify-end gap-4">
                    <SecondaryButton 
                        @click="closeModal"
                        variant="outline"
                        type="button"
                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                    >
                        Batal
                    </SecondaryButton>

                    <PrimaryButton
                        variant="danger"
                        :class="{ 'opacity-25': processing }"
                        :disabled="processing"
                        @click="deleteUser"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-medium rounded-lg transition-colors duration-200 disabled:opacity-25"
                    >
                        {{ processing ? 'Menghapus...' : 'Hapus Pengguna' }}
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Head } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';
import TextInput from '@/Components/TextInput.vue';
import Badge from '@/Components/Badge.vue';
import { useForm } from '@inertiajs/vue3';
import { PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    auth: {
        type: Object,
        required: true
    },
    users: {
        type: Array,
        required: true
    },
    roles: {
        type: Array,
        default: () => []
    }
});

const search = ref('');
const selectedUser = ref(null);
const confirmingUserDeletion = ref(false);
const processing = ref(false);

// Tambahkan state filters
const filters = ref({
    role: '',
    status: ''
});

// Update computed property untuk filtered users dengan filter
const filteredUsers = computed(() => {
    let result = props.users;
    
    // Filter berdasarkan pencarian
    if (search.value) {
    const searchTerm = search.value.toLowerCase();
        result = result.filter(user => 
        user.name.toLowerCase().includes(searchTerm) ||
        user.email.toLowerCase().includes(searchTerm) ||
        user.phone?.toLowerCase().includes(searchTerm) ||
        user.roles.some(role => role.toLowerCase().includes(searchTerm)) ||
        user.status.toLowerCase().includes(searchTerm)
    );
    }
    
    // Filter berdasarkan role
    if (filters.value.role) {
        result = result.filter(user => 
            user.roles.includes(filters.value.role)
        );
    }
    
    // Filter berdasarkan status
    if (filters.value.status) {
        result = result.filter(user => 
            user.status === filters.value.status
        );
    }
    
    return result;
});

const closeModal = () => {
    confirmingUserDeletion.value = false;
    selectedUser.value = null;
    processing.value = false;
};

const confirmUserDeletion = (user) => {
    selectedUser.value = user;
    confirmingUserDeletion.value = true;
};

const deleteUser = () => {
    if (selectedUser.value) {
        processing.value = true;
        router.delete(route('admin.users.destroy', selectedUser.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
            },
            onError: () => {
                processing.value = false;
            },
            onFinish: () => {
                processing.value = false;
            }
        });
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

// Tambahkan fungsi helper untuk status
const getStatusVariant = (status) => {
    const variants = {
        'pending': 'warning',
        'active': 'success',
        'rejected': 'danger',
        'banned': 'danger',
        'inactive': 'warning'
    };
    return variants[status] || 'warning';
};

const getStatusLabel = (status) => {
    const labels = {
        'pending': 'Pending',
        'active': 'Aktif',
        'rejected': 'Ditolak',
        'banned': 'Diblokir',
        'inactive': 'Nonaktif'
    };
    return labels[status] || status;
};

// Tambahkan helper function untuk label role
const getRoleLabel = (role) => {
    const labels = {
        'admin': 'Administrator',
        'user': 'Pengguna',
        // Tambahkan label untuk role lainnya jika ada
    };
    return labels[role] || role;
};
</script>

<style scoped>
select {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}

select::-ms-expand {
    display: none;
}

select option {
    background-color: var(--bg-primary);
    color: var(--text-primary);
    padding: 0.5rem 1rem;
}

.dark select option {
    background-color: #1f2937;
    color: #fff;
}

/* Transisi halus untuk perubahan tema */
.theme-transition {
    transition: all 0.3s ease;
}
</style> 
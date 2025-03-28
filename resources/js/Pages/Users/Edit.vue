<template>
    <Head title="Edit User" />

    <AuthenticatedLayout :auth="auth" title="Edit Pengguna">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-lg md:text-xl font-semibold text-[var(--text-primary)] truncate">
                    Edit Pengguna
                </h2>
            </div>
        </template>

        <Card class="max-w-2xl mx-auto">
            <!-- Alert untuk aturan bisnis -->
            <div class="mb-6 p-4 border rounded-md bg-yellow-50 border-yellow-300 text-yellow-800">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium">Perhatian:</span>
                </div>
                <p class="ml-7">Untuk mengubah data user, Anda harus menonaktifkan user terlebih dahulu (ubah status menjadi 'Nonaktif').</p>
            </div>

            <form @submit.prevent="submitForm" class="space-y-6">
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
                    <InputLabel for="roles" value="Role" />
                    <div class="mt-2 grid grid-cols-2 gap-4">
                        <label v-for="role in roles" :key="role" class="relative flex items-start p-2 hover:bg-[var(--bg-secondary)]/50 rounded-lg cursor-pointer">
                            <div class="flex items-center h-5">
                                <input
                                    type="checkbox"
                                    :value="role"
                                    v-model="form.roles"
                                    class="w-5 h-5 rounded border-[var(--border-primary)] bg-[var(--bg-secondary)] text-[var(--primary-600)] focus:ring-[var(--primary-500)] dark:bg-gray-700 dark:border-gray-600 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-blue-400"
                                    style="accent-color: var(--primary-600);"
                                />
                            </div>
                            <div class="ml-3 text-sm">
                                <span class="font-medium text-[var(--text-primary)]">{{ role }}</span>
                            </div>
                        </label>
                    </div>
                    <InputError :message="form.errors.roles" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="status" value="Status" />
                    <select 
                        id="status" 
                        v-model="form.status" 
                        class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400"
                        :class="{ 'border-red-500': form.errors.status }"
                        required
                    >
                        <option 
                            v-for="option in statusOptions" 
                            :key="option.value"
                            :value="option.value"
                            class="py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200"
                        >
                            {{ option.label }}
                        </option>
                    </select>
                    <InputError :message="form.errors.status" class="mt-2" />
                </div>

                <!-- Status Reason Field -->
                <div v-if="isStatusReasonRequired" class="mt-4">
                    <InputLabel for="status_reason" :value="getStatusReasonLabel()" />
                    <textarea
                        id="status_reason"
                        v-model="form.status_reason"
                        class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400"
                        rows="3"
                        required
                        :placeholder="getStatusReasonPlaceholder()"
                    ></textarea>
                    <InputError :message="form.errors.status_reason" class="mt-2" />
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
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { computed, onMounted } from 'vue';

const props = defineProps({
    auth: {
        type: Object,
        required: true
    },
    user: {
        type: Object,
        required: true
    },
    roles: {
        type: Array,
        required: true
    }
});

// Computed untuk memastikan roles berbentuk array string
const userRoles = computed(() => {
    const roles = props.user.roles || [];
    
    // Jika roles adalah array string, gunakan langsung
    if (roles.length > 0 && typeof roles[0] === 'string') {
        return roles;
    }
    
    // Jika roles adalah array objek, ambil properti name
    return roles.map(role => {
        if (typeof role === 'string') return role;
        return role.name || ''; 
    }).filter(Boolean);
});

// Status options dari database enum
const statusOptions = [
    { value: 'pending', label: 'Pending' },
    { value: 'active', label: 'Aktif' },
    { value: 'rejected', label: 'Ditolak' },
    { value: 'banned', label: 'Diblokir' },
    { value: 'inactive', label: 'Nonaktif' }
];

const form = useForm({
    name: props.user.name || '',
    email: props.user.email || '',
    phone: props.user.phone || '',
    roles: userRoles.value,
    status: props.user.status || 'pending',
    status_reason: props.user.status_reason || '',
    previous_status: props.user.status || 'pending'
});

onMounted(() => {
    console.log('Form initialized with:', form);
    console.log('Available roles:', props.roles);
    console.log('User roles:', userRoles.value);
});

// Computed property untuk mengecek apakah status_reason diperlukan
const isStatusReasonRequired = computed(() => {
    return form.status && form.status !== 'active';
});

// Helper untuk label status reason
const getStatusReasonLabel = () => {
    const labels = {
        'pending': 'Alasan Pending',
        'rejected': 'Alasan Penolakan',
        'banned': 'Alasan Pemblokiran',
        'inactive': 'Alasan Penonaktifan'
    };
    return labels[form.status] || 'Alasan Perubahan Status';
};

// Helper untuk placeholder status reason
const getStatusReasonPlaceholder = () => {
    const placeholders = {
        'pending': 'Masukkan alasan kenapa user masih pending...',
        'rejected': 'Masukkan alasan kenapa user ditolak...',
        'banned': 'Masukkan alasan kenapa user diblokir...',
        'inactive': 'Masukkan alasan kenapa user dinonaktifkan...'
    };
    return placeholders[form.status] || 'Masukkan alasan perubahan status...';
};

// Function untuk handle submit form
const submitForm = () => {
    // Debug log
    console.log('Submitting form with data:', {
        name: form.name,
        email: form.email,
        phone: form.phone,
        roles: form.roles,
        status: form.status,
        status_reason: form.status_reason
    });

    // Validasi roles
    if (!form.roles || !Array.isArray(form.roles) || form.roles.length === 0) {
        form.setError('roles', 'Minimal pilih satu role untuk user');
        return;
    }

    // Validasi status
    if (!form.status) {
        form.status = 'pending';
    }

    // Validasi perubahan data hanya jika status bukan inactive
    const originalUser = props.user;
    const hasDataChanges = 
        form.name !== originalUser.name || 
        form.email !== originalUser.email || 
        form.phone !== originalUser.phone || 
        !areRolesEqual(form.roles, userRoles.value);
        
    console.log('Data changes detected:', hasDataChanges);
    console.log('Current status:', form.status);
    
    // Jika ada perubahan data tetapi status bukan inactive
    if (hasDataChanges && form.status !== 'inactive') {
        form.setError('status', 'User harus dinonaktifkan terlebih dahulu sebelum data dapat diubah.');
        return;
    }

    // Validasi status reason jika diperlukan
    if (form.status !== 'active' && !form.status_reason) {
        form.setError('status_reason', 'Alasan perubahan status wajib diisi');
        return;
    }

    // Kirim data ke server
    form.put(route('admin.users.update', props.user.id), {
        onSuccess: () => {
            console.log('User berhasil diperbarui');
        },
        onError: (errors) => {
            console.error('Error updating user:', errors);
        }
    });
};

// Helper untuk membandingkan apakah dua array roles sama
const areRolesEqual = (roles1, roles2) => {
    if (!Array.isArray(roles1) || !Array.isArray(roles2)) return false;
    if (roles1.length !== roles2.length) return false;
    
    // Urutkan dan bandingkan
    const sorted1 = [...roles1].sort();
    const sorted2 = [...roles2].sort();
    
    return sorted1.every((val, idx) => val === sorted2[idx]);
};
</script>

<style scoped>
/* Styling untuk select dropdown */
select option {
    padding: 8px;
    margin: 4px;
}

/* Dark mode styling untuk dropdown */
.dark select option {
    background-color: #1f2937; /* dark:bg-gray-800 */
    color: #e5e7eb; /* dark:text-gray-200 */
}

/* Memastikan checkbox memiliki ukuran yang konsisten */
input[type="checkbox"] {
    min-width: 1rem;
    min-height: 1rem;
}

/* Transisi halus untuk perubahan tema */
.theme-transition {
    transition: all 0.3s ease;
}
</style> 
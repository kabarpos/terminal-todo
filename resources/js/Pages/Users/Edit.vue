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
                    <InputLabel for="roles" value="Role" />
                    <div class="mt-2 grid grid-cols-2 gap-4">
                        <label v-for="role in roles" :key="role.id || role" class="relative flex items-start p-2 hover:bg-[var(--bg-secondary)]/50 rounded-lg cursor-pointer">
                            <div class="flex items-center h-5">
                                <input
                                    type="checkbox"
                                    :value="getRoleName(role)"
                                    v-model="form.roles"
                                    class="w-5 h-5 rounded border-[var(--border-primary)] bg-[var(--bg-secondary)] text-[var(--primary-600)] focus:ring-[var(--primary-500)]"
                                />
                            </div>
                            <div class="ml-3 text-sm">
                                <span class="font-medium text-[var(--text-primary)]">{{ getRoleName(role) }}</span>
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
import { computed } from 'vue';

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

// Helper untuk mendapatkan nama role yang lebih robust
const getRoleName = (role) => {
    if (!role) return '';
    if (typeof role === 'string') return role;
    if (typeof role === 'object' && role !== null) {
        return role.name || role.id || '';
    }
    return '';
};

// Format roles untuk form dengan penanganan yang lebih baik
const formattedRoles = computed(() => {
    const userRoles = props.user.roles || [];
    console.log('User roles sebelum format:', userRoles);
    const formatted = userRoles
        .map(role => getRoleName(role))
        .filter(name => name !== '');
    console.log('User roles setelah format:', formatted);
    return formatted;
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
    roles: formattedRoles.value,
    status: props.user.status || 'pending',
    status_reason: props.user.status_reason || null,
    previous_status: props.user.status || 'pending' // Tambahkan previous_status
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

const submit = () => {
    // Debug log untuk melihat data yang akan dikirim
    console.log('Data yang akan dikirim:', {
        name: form.name,
        email: form.email,
        phone: form.phone,
        roles: form.roles,
        status: form.status,
        status_reason: form.status_reason,
        previous_status: form.previous_status
    });

    // Validasi roles
    if (!form.roles || form.roles.length === 0) {
        form.setError('roles', 'Minimal pilih satu role untuk user');
        return;
    }

    // Validasi status sebelum submit
    if (!form.status) {
        form.status = 'pending';
    }

    // Validasi perubahan status
    if (form.previous_status === 'pending' && form.status === 'active') {
        form.setError('status', 'User baru tidak bisa langsung diaktifkan. Silakan ubah ke status lain terlebih dahulu.');
        return;
    }

    // Persiapkan data yang akan dikirim
    const formData = {
        name: form.name,
        email: form.email,
        phone: form.phone,
        roles: form.roles,
        status: form.status,
        status_reason: form.status === 'active' ? null : (form.status_reason || ''),
        previous_status: form.previous_status
    };

    // Validasi status_reason jika status bukan active
    if (form.status !== 'active' && !formData.status_reason) {
        form.setError('status_reason', 'Alasan perubahan status wajib diisi');
        return;
    }
    
    // Kirim data yang sudah diformat
    form.put(route('admin.users.update', props.user.id), formData, {
        preserveScroll: true,
        onSuccess: () => {
            console.log('User berhasil diperbarui:', formData);
            // Update previous_status setelah berhasil
            form.previous_status = form.status;
        },
        onError: (errors) => {
            console.error('Detail error:', errors);
            Object.keys(errors).forEach(key => {
                console.error(`Error pada field ${key}:`, errors[key]);
            });
        }
    });
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
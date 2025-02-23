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
                        class="mt-1 block w-full rounded-lg border-[var(--border-primary)] bg-[var(--bg-secondary)] text-[var(--text-primary)] focus:border-[var(--primary-500)] focus:ring-[var(--primary-500)]"
                        required
                    >
                        <option 
                            v-for="option in statusOptions" 
                            :key="option.value"
                            :value="option.value"
                            class="bg-[var(--bg-secondary)] text-[var(--text-primary)]"
                        >
                            {{ option.label }}
                        </option>
                    </select>
                    <InputError :message="form.errors.status" class="mt-2" />
                </div>

                <!-- Status Reason Field -->
                <div v-if="form.status && form.status !== 'active'">
                    <InputLabel for="status_reason" :value="getStatusReasonLabel()" />
                    <textarea
                        id="status_reason"
                        v-model="form.status_reason"
                        class="mt-1 block w-full rounded-lg border-[var(--border-primary)] bg-[var(--bg-secondary)] text-[var(--text-primary)] focus:border-[var(--primary-500)] focus:ring-[var(--primary-500)]"
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

// Format roles untuk form
const formattedRoles = computed(() => {
    return props.user.roles.map(role => {
        return typeof role === 'object' ? role.name : role;
    });
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    phone: props.user.phone,
    roles: formattedRoles.value.length ? formattedRoles.value : ['user'],
    status: props.user.status || 'pending',
    status_reason: props.user.status_reason || ''
});

const submit = () => {
    // Validasi status sebelum submit
    if (!form.status) {
        form.status = 'pending';
    }

    // Validasi status_reason jika status bukan active
    if (form.status !== 'active' && !form.status_reason) {
        form.setError('status_reason', 'Alasan perubahan status wajib diisi');
        return;
    }
    
    form.put(route('admin.users.update', props.user.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Feedback sukses
            console.log('User berhasil diperbarui dengan status:', form.status);
        },
        onError: (errors) => {
            console.error('Error saat memperbarui user:', errors);
        }
    });
};

// Helper untuk mendapatkan nama role
const getRoleName = (role) => {
    return typeof role === 'object' ? role.name : role;
};

// Status options dari database enum
const statusOptions = [
    { value: 'pending', label: 'Pending' },
    { value: 'active', label: 'Aktif' },
    { value: 'rejected', label: 'Ditolak' },
    { value: 'banned', label: 'Diblokir' },
    { value: 'inactive', label: 'Nonaktif' }
];

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
</script>

<style scoped>
/* Styling untuk select dropdown */
select option {
    padding: 8px;
    margin: 4px;
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
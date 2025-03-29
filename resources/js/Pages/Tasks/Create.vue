<template>
    <Head title="Buat Task Baru">
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
        <meta http-equiv="Pragma" content="no-cache" />
        <meta http-equiv="Expires" content="0" />
    </Head>

    <AuthenticatedLayout :auth="auth" title="Tambah Task">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Tambah Task
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Pesan error -->
                            <div v-if="errorMessage" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                                <p class="font-bold">Error</p>
                                <p>{{ errorMessage }}</p>
                            </div>
                            
                            <!-- Pesan sukses -->
                            <div v-if="successMessage" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                                <p class="font-bold">Sukses</p>
                                <p>{{ successMessage }}</p>
                            </div>
                            
                            <div>
                                <InputLabel for="title" value="Judul Task" />
                                <TextInput
                                    id="title"
                                    v-model="form.title"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                    autofocus
                                />
                                <InputError :message="form.errors.title" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="description" value="Deskripsi" />
                                <TextArea
                                    id="description"
                                    v-model="form.description"
                                    class="mt-1 block w-full"
                                    rows="3"
                                />
                                <InputError :message="form.errors.description" class="mt-2" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="category_id" value="Kategori" />
                                    <SelectInput
                                        id="category_id"
                                        v-model="form.category_id"
                                        class="mt-1 block w-full"
                                        required
                                    >
                                        <option value="">Pilih Kategori</option>
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </SelectInput>
                                    <InputError :message="form.errors.category_id" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="platform_id" value="Platform" />
                                    <SelectInput
                                        id="platform_id"
                                        v-model="form.platform_id"
                                        class="mt-1 block w-full"
                                    >
                                        <option value="">Pilih Platform</option>
                                        <option v-for="platform in platforms" :key="platform.id" :value="platform.id">
                                            {{ platform.name }}
                                        </option>
                                    </SelectInput>
                                    <InputError :message="form.errors.platform_id" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="team_id" value="Tim" />
                                    <SelectInput
                                        id="team_id"
                                        v-model="form.team_id"
                                        class="mt-1 block w-full"
                                    >
                                        <option value="">Pilih Tim</option>
                                        <option v-for="team in teams" :key="team.id" :value="team.id">
                                            {{ team.name }}
                                        </option>
                                    </SelectInput>
                                    <InputError :message="form.errors.team_id" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="priority" value="Prioritas" />
                                    <SelectInput
                                        id="priority"
                                        v-model="form.priority"
                                        class="mt-1 block w-full"
                                        required
                                    >
                                        <option value="">Pilih Prioritas</option>
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                        <option value="urgent">Urgent</option>
                                    </SelectInput>
                                    <InputError :message="form.errors.priority" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="start_date" value="Tanggal Mulai" />
                                    <TextInput
                                        id="start_date"
                                        v-model="form.start_date"
                                        type="datetime-local"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError :message="form.errors.start_date" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="due_date" value="Tenggat Waktu" />
                                    <TextInput
                                        id="due_date"
                                        v-model="form.due_date"
                                        type="datetime-local"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError :message="form.errors.due_date" class="mt-2" />
                                </div>
                            </div>

                            <div>
                                <InputLabel for="status" value="Status" />
                                <div class="mt-1 grid grid-cols-2 md:grid-cols-4 gap-3">
                                    <button
                                        type="button"
                                        v-for="status in statuses"
                                        :key="status.value"
                                        @click="form.status = status.value"
                                        class="p-3 rounded-lg text-sm font-medium transition-all duration-200 flex items-center justify-center gap-2"
                                        :class="[
                                            form.status === status.value
                                                ? status.activeClass
                                                : status.inactiveClass,
                                            'hover:shadow-md'
                                        ]"
                                    >
                                        <span :class="status.iconClass" v-if="status.icon">
                                            <i :class="status.icon"></i>
                                        </span>
                                        {{ status.label }}
                                    </button>
                                </div>
                                <InputError :message="form.errors.status" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="assignees" value="Assignee" />
                                <select
                                    id="assignees"
                                    v-model="form.assignees"
                                    multiple
                                    class="mt-1 block w-full px-4 py-2 text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-400 focus:ring-primary-500 dark:focus:ring-primary-400 rounded-lg shadow-sm"
                                    required
                                >
                                    <option v-for="user in users" :key="user.id" :value="user.id">
                                        {{ user.name }}
                                    </option>
                                </select>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Tekan CTRL (Windows) atau CMD (Mac) untuk memilih lebih dari satu
                                </p>
                                <InputError :message="form.errors.assignees" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end gap-4">
                                <Link
                                    :href="route('tasks.index')"
                                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                >
                                    Batal
                                </Link>

                                <PrimaryButton
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium rounded-lg transition-colors duration-200"
                                >
                                    {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import SelectInput from '@/Components/SelectInput.vue';
import axios from 'axios';

const props = defineProps({
    categories: {
        type: Array,
        required: true
    },
    platforms: {
        type: Array,
        required: true
    },
    teams: {
        type: Array,
        required: true
    },
    users: {
        type: Array,
        required: true
    },
    auth: {
        type: Object,
        required: true
    }
});

const statuses = [
    {
        value: 'draft',
        label: 'Draft',
        activeClass: 'bg-gray-600 text-white',
        inactiveClass: 'bg-gray-100 text-gray-600 hover:bg-gray-200',
        icon: 'fas fa-file-alt'
    },
    {
        value: 'in_progress',
        label: 'In Progress',
        activeClass: 'bg-purple-500 text-white',
        inactiveClass: 'bg-purple-100 text-purple-600 hover:bg-purple-200',
        icon: 'fas fa-spinner'
    },
    {
        value: 'completed',
        label: 'Completed',
        activeClass: 'bg-green-500 text-white',
        inactiveClass: 'bg-green-100 text-green-600 hover:bg-green-200',
        icon: 'fas fa-check'
    },
    {
        value: 'cancelled',
        label: 'Cancelled',
        activeClass: 'bg-red-500 text-white',
        inactiveClass: 'bg-red-100 text-red-600 hover:bg-red-200',
        icon: 'fas fa-times'
    }
];

const form = useForm({
    title: '',
    description: '',
    category_id: '',
    platform_id: '',
    team_id: '',
    priority: 'medium',
    status: 'draft',
    start_date: '',
    due_date: formatDate(new Date(Date.now() + 7 * 24 * 60 * 60 * 1000)),
    assignees: []
});

const isSubmitting = ref(false);
const successMessage = ref('');
const errorMessage = ref('');
const lastSubmitTime = ref(0);

// Fungsi helper untuk format date untuk input datetime-local
function formatDate(date) {
    const d = new Date(date);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    
    return `${year}-${month}-${day}T${hours}:${minutes}`;
}

const submit = () => {
    // BAGIAN 1: PENGECEKAN AWAL & PENGUNCIAN
    // Double check untuk pencegahan duplikasi submisi
    if (isSubmitting.value) {
        console.log('[ANTI-DUPLIKAT] Submission dalam proses, menolak permintaan baru');
        return false;
    }
    
    // Cek waktu antara submisi untuk mencegah double-click
    const now = Date.now();
    if (now - lastSubmitTime.value < 3000) { // 3 detik
        console.log('[ANTI-DUPLIKAT] Submisi terlalu cepat, minimal jeda 3 detik dibutuhkan');
        errorMessage.value = 'Mohon tunggu beberapa detik sebelum mencoba lagi';
        return false;
    }
    
    // PRE-VALIDATION: Cek dulu field wajib
    if (!form.category_id) {
        errorMessage.value = 'Kategori harus dipilih';
        return false;
    }
    
    if (!form.assignees || form.assignees.length === 0) {
        errorMessage.value = 'Minimal satu assignee harus dipilih';
        return false;
    }
    
    if (!form.due_date) {
        errorMessage.value = 'Tenggat waktu harus diisi';
        return false;
    }
    
    // Periksa localStorage untuk deteksi duplikasi per sesi
    const submissionKey = `task_submission_${form.title.toLowerCase().replace(/[^a-z0-9]/g, '_')}_${now}`;
    const recentSubmissions = JSON.parse(localStorage.getItem('recent_task_submissions') || '[]');
    
    // Cek apakah ada task serupa dalam 60 detik terakhir
    const similarTask = recentSubmissions.find(sub => {
        return sub.title.toLowerCase() === form.title.toLowerCase() && 
               (now - sub.timestamp) < 60000; // 60 detik
    });
    
    if (similarTask) {
        console.log('[ANTI-DUPLIKAT] Task serupa sudah dibuat dalam 60 detik terakhir:', similarTask);
        errorMessage.value = 'Task serupa baru saja dibuat. Mohon tunggu atau gunakan judul berbeda.';
        return false;
    }
    
    // BAGIAN 2: SETUP SUBMISSION
    // Tandai waktu submit & status
    lastSubmitTime.value = now;
    isSubmitting.value = true;
    
    // Tambahkan submisi ini ke localStorage
    recentSubmissions.push({
        title: form.title,
        timestamp: now,
        key: submissionKey
    });
    
    // Batasi hanya menyimpan 5 submisi terakhir
    while (recentSubmissions.length > 5) {
        recentSubmissions.shift();
    }
    
    localStorage.setItem('recent_task_submissions', JSON.stringify(recentSubmissions));
    
    // Log form data untuk debugging
    console.log('[DEBUG] Form data yang akan dikirim:', form.data());
    
    // Clear pesan sebelumnya
    successMessage.value = '';
    errorMessage.value = '';
    
    // Disable tombol submit secara langsung untuk double-safety
    const submitButtons = document.querySelectorAll('button[type="submit"]');
    submitButtons.forEach(btn => {
        btn.disabled = true;
        btn.setAttribute('disabled', 'disabled');
    });
    
    // BAGIAN 3: PERSIAPAN DATA
    // Tambahkan metadata anti-duplikasi
    const formData = {
        ...form.data(),
        timestamp: now,
        _prevent_duplicate: submissionKey,
        _sessionId: localStorage.getItem('session_id') || Math.random().toString(36).substring(2)
    };
    
    // Set session ID jika belum ada
    if (!localStorage.getItem('session_id')) {
        localStorage.setItem('session_id', formData._sessionId);
    }

    // Pastikan assignees dalam format yang benar (array numerik)
    if (Array.isArray(formData.assignees)) {
        // Pastikan semua item adalah number bukan string
        formData.assignees = formData.assignees.map(id => 
            typeof id === 'string' ? parseInt(id, 10) : id
        );
        
        // Jika assignees kosong, tampilkan peringatan
        if (formData.assignees.length === 0) {
            errorMessage.value = 'Minimal satu assignee harus dipilih';
            isSubmitting.value = false;
            return;
        }
    }

    // Pastikan category_id adalah number
    if (formData.category_id) {
        formData.category_id = parseInt(formData.category_id, 10);
    }

    // Pastikan platform_id dan team_id adalah number jika tidak kosong
    if (formData.platform_id) {
        formData.platform_id = parseInt(formData.platform_id, 10);
    }

    if (formData.team_id) {
        formData.team_id = parseInt(formData.team_id, 10);
    }

    // BAGIAN 4: SUBMIT KE SERVER
    console.log('[DEBUG] Task submission started at', new Date().toISOString());
    
    // Gunakan XHR alih-alih form.post untuk lebih banyak kontrol
    axios.post(route('tasks.store') + `?_nocache=${Date.now()}`, formData, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Cache-Control': 'no-cache, no-store, must-revalidate',
            'Pragma': 'no-cache',
            'Expires': '0',
            'X-Requested-With': 'XMLHttpRequest',
            'X-Submission-ID': submissionKey
        }
    })
    .then(response => {
        console.log('[SUCCESS] Task berhasil dibuat:', response.data);
        successMessage.value = 'Task berhasil dibuat!';
        
        // Reset form & hapus submissionKey dari localStorage
        form.reset();
        
        // Tandai sebagai berhasil di localStorage untuk mencegah duplikasi
        const completedSubmissions = JSON.parse(localStorage.getItem('completed_task_submissions') || '[]');
        completedSubmissions.push(submissionKey);
        localStorage.setItem('completed_task_submissions', JSON.stringify(completedSubmissions));
        
        // Tunggu sebentar untuk memastikan server selesai memproses
        setTimeout(() => {
            // Redirect ke task index dengan highlight
            const redirectUrl = response.data?.redirect || route('tasks.index', {
                highlight: response.data?.task?.id || response.data?.id,
                status: response.data?.task?.status || formData.status || 'draft',
                timestamp: Date.now(),
                nocache: Math.random().toString(36).substring(2)
            });
            
            console.log('[REDIRECT] Navigasi ke:', redirectUrl);
            window.location.href = redirectUrl;
        }, 500);
    })
    .catch(error => {
        console.error('[ERROR] Task submission gagal:', error);
        
        // Log lebih detail untuk debug
        if (error.response) {
            console.error('[ERROR] Server response:', {
                status: error.response.status,
                headers: error.response.headers,
                data: error.response.data
            });
        } else if (error.request) {
            console.error('[ERROR] Request made but no response received');
        } else {
            console.error('[ERROR] Error setting up request:', error.message);
        }
        
        // Hapus dari daftar submisi di localStorage
        const updatedSubmissions = recentSubmissions.filter(sub => sub.key !== submissionKey);
        localStorage.setItem('recent_task_submissions', JSON.stringify(updatedSubmissions));
        
        // Detail error validasi dari Laravel
        if (error.response?.status === 422 && error.response?.data?.errors) {
            console.error('[VALIDATION] Errors:', error.response.data.errors);
            
            // Set validation errors ke form
            Object.keys(error.response.data.errors).forEach(field => {
                form.errors[field] = error.response.data.errors[field][0];
            });
            
            errorMessage.value = 'Validasi form gagal. Silakan periksa input Anda.';
        } else if (error.response?.data?.is_duplicate) {
            // Tangani kasus task duplikat yang terdeteksi server
            console.warn('[DUPLICATE] Server mendeteksi duplikat:', error.response.data);
            
            // Jika server memberikan ID task yang sudah ada, redirect ke sana
            if (error.response.data?.task?.id) {
                const redirectUrl = route('tasks.index', {
                    highlight: error.response.data.task.id,
                    status: error.response.data.task.status || 'draft',
                    timestamp: Date.now(),
                    nocache: Math.random().toString(36).substring(2)
                });
                
                successMessage.value = 'Task dengan judul serupa sudah ada. Mengalihkan...';
                setTimeout(() => window.location.href = redirectUrl, 1500);
            } else {
                errorMessage.value = 'Task dengan judul serupa sudah ada.';
            }
        } else if (error.response?.status === 500) {
            // Handle error 500 (Internal Server Error)
            console.error('[SERVER ERROR] 500 Internal Server Error:', error.response?.data);
            
            // Tambahkan tombol retry
            errorMessage.value = 'Terjadi kesalahan server. Silakan coba lagi dalam beberapa saat.';
            
            // Simpan data form ke local storage untuk recovery
            const recoveryKey = 'task_form_recovery_' + Date.now();
            localStorage.setItem(recoveryKey, JSON.stringify(formData));
            console.log('[RECOVERY] Form data tersimpan dengan key:', recoveryKey);
            
            // Tambahkan log debugging untuk membantu troubleshooting
            console.log('[DEBUG] Form data yang dikirim:', formData);
            console.log('[DEBUG] CSRF token:', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 'TIDAK ADA');
            
            // Tambahkan opsi untuk retry menggunakan Inertia
            setTimeout(() => {
                if (confirm('Gagal mengirim dengan AJAX. Coba kirim dengan metode form biasa?')) {
                    submitUsingInertia();
                }
            }, 1000);
        } else {
            errorMessage.value = 'Terjadi kesalahan: ' + (error.response?.data?.message || error.message);
        }
        
        // Re-enable tombol submit
        submitButtons.forEach(btn => {
            btn.disabled = false;
            btn.removeAttribute('disabled');
        });
        
        isSubmitting.value = false;
    });
};

// Fallback method menggunakan Inertia langsung jika axios gagal
const submitUsingInertia = () => {
    console.log('[FALLBACK] Mengirim task menggunakan Inertia form...');
    
    // Buat objek form data yang sesuai dengan requirements
    const inertiaFormData = {
        title: form.title,
        description: form.description,
        category_id: parseInt(form.category_id),
        platform_id: form.platform_id ? parseInt(form.platform_id) : null,
        team_id: form.team_id ? parseInt(form.team_id) : null,
        priority: form.priority,
        status: form.status,
        start_date: form.start_date,
        due_date: form.due_date,
        assignees: Array.isArray(form.assignees) ? form.assignees.map(id => typeof id === 'string' ? parseInt(id) : id) : [],
        _prevent_duplicate: `inertia_fallback_${Date.now()}_${Math.random().toString(36).substring(2)}`,
        timestamp: Date.now()
    };
    
    // Submit form langsung menggunakan Inertia
    form.clearErrors();
    form.post(route('tasks.store'), {
        preserveScroll: true,
        onSuccess: () => {
            successMessage.value = 'Task berhasil dibuat! Mengalihkan...';
            setTimeout(() => {
                window.location.href = route('tasks.index', {
                    timestamp: Date.now(),
                    nocache: Math.random().toString(36).substring(2)
                });
            }, 500);
        },
        onError: (errors) => {
            console.error('[FALLBACK ERROR] Inertia submission error:', errors);
            errorMessage.value = 'Validasi form gagal. Silakan periksa input Anda.';
        }
    });
};
</script>

<style scoped>
.status-button {
    transition: all 0.2s ease;
}

.status-button:hover {
    transform: translateY(-1px);
}
</style> 
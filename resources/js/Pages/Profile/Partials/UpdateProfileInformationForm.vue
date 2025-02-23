<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;
const avatarPreview = ref(null);

const form = useForm({
    name: user.name,
    email: user.email,
    phone: user.phone || '',
    avatar: null,
    _method: 'PATCH'
});

const selectNewAvatar = (e) => {
    const file = e.target.files[0];
    if (file) {
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
        if (!allowedTypes.includes(file.type)) {
            alert('File harus berupa gambar (JPG, PNG, atau GIF)');
            e.target.value = '';
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file tidak boleh lebih dari 2MB');
            e.target.value = '';
            return;
        }

        form.avatar = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            avatarPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const submit = () => {
    if (form.avatar) {
        form.post(route('profile.update'), {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: () => {
                form.reset('password', 'password_confirmation');
                form.avatar = null;
            },
            onError: (errors) => {
                if (errors.avatar) {
                    alert(errors.avatar);
                }
            }
        });
    } else {
        form.post(route('profile.update'), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset('password', 'password_confirmation');
            }
        });
    }
};
</script>

<template>
    <section>
        <form @submit.prevent="submit" class="space-y-8" enctype="multipart/form-data">
            <!-- Avatar Section -->
            <div class="flex flex-col items-center space-y-4">
                <div class="relative group">
                    <div class="relative">
                        <img 
                            v-if="avatarPreview || user.avatar_url" 
                            :src="avatarPreview || user.avatar_url" 
                            class="h-32 w-32 rounded-full object-cover ring-4 ring-white dark:ring-gray-700 shadow-lg"
                            alt="Avatar Preview"
                        />
                        <div 
                            v-else 
                            class="h-32 w-32 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center shadow-lg"
                        >
                            <svg class="h-16 w-16 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        
                        <!-- Overlay for hover effect -->
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <label 
                                for="avatar"
                                class="absolute inset-0 w-full h-full cursor-pointer rounded-full bg-black bg-opacity-40 flex items-center justify-center text-white"
                            >
                                <span class="text-sm font-medium">Ubah Foto</span>
                                <input 
                                    type="file" 
                                    id="avatar" 
                                    name="avatar"
                                    @change="selectNewAvatar" 
                                    class="sr-only"
                                    accept="image/*"
                                />
                            </label>
                        </div>
                    </div>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Format: PNG, JPG, GIF (Maks. 2MB)
                </p>
                <InputError :message="form.errors.avatar" />
            </div>

            <!-- Form Fields -->
            <div class="grid grid-cols-1 gap-6">
                <div class="space-y-6">
                    <!-- Name -->
                    <div>
                        <InputLabel for="name" value="Nama Lengkap" class="text-gray-700 dark:text-gray-300" />
                        <TextInput
                            id="name"
                            type="text"
                            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="Masukkan nama lengkap"
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <!-- Email -->
                    <div>
                        <InputLabel for="email" value="Email" class="text-gray-700 dark:text-gray-300" />
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            placeholder="Masukkan alamat email"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <!-- Phone -->
                    <div>
                        <InputLabel for="phone" value="Nomor WhatsApp" class="text-gray-700 dark:text-gray-300" />
                        <TextInput
                            id="phone"
                            type="text"
                            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                            v-model="form.phone"
                            required
                            placeholder="Contoh: 081234567890"
                        />
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Format: 08xx-xxxx-xxxx (format Indonesia)</p>
                        <InputError class="mt-2" :message="form.errors.phone" />
                    </div>
                </div>
            </div>

            <!-- Email Verification Notice -->
            <div v-if="mustVerifyEmail && user.email_verified_at === null" 
                class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg">
                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                    Email Anda belum diverifikasi.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="font-medium text-yellow-600 dark:text-yellow-400 hover:text-yellow-500 dark:hover:text-yellow-300 underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500"
                    >
                        Klik di sini untuk mengirim ulang email verifikasi.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600 dark:text-green-400"
                >
                    Link verifikasi baru telah dikirim ke alamat email Anda.
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                <PrimaryButton 
                    :disabled="form.processing"
                    variant="primary"
                    size="md"
                    class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700"
                >
                    <span v-if="form.processing">Menyimpan...</span>
                    <span v-else>Simpan Perubahan</span>
                </PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out duration-300"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out duration-300"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-green-600 dark:text-green-400"
                    >
                        Tersimpan.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>

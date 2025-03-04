<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const form = useForm({
    name: "",
    email: "",
    phone: "",
    password: "",
    password_confirmation: "",
});

const validateForm = () => {
    let isValid = true;
    form.clearErrors();

    // Name validation
    if (!form.name) {
        form.setError("name", "Name is required");
        isValid = false;
    } else if (form.name.length < 2) {
        form.setError("name", "Name must be at least 2 characters");
        isValid = false;
    }

    // Email validation
    if (!form.email) {
        form.setError("email", "Email is required");
        isValid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
        form.setError("email", "Invalid email format");
        isValid = false;
    }

    // Phone validation
    if (form.phone && !/^\+?\d{10,13}$/.test(form.phone)) {
        form.setError("phone", "Invalid phone number format");
        isValid = false;
    }

    // Password validation
    if (!form.password) {
        form.setError("password", "Password is required");
        isValid = false;
    } else if (form.password.length < 8) {
        form.setError("password", "Password must be at least 8 characters");
        isValid = false;
    }

    // Password confirmation
    if (form.password !== form.password_confirmation) {
        form.setError(
            "password_confirmation",
            "Password confirmation does not match"
        );
        isValid = false;
    }

    return isValid;
};

const validatePhone = () => {
    // Hapus karakter non-digit
    form.phone = form.phone.replace(/\D/g, '');
    
    // Pastikan dimulai dengan '08'
    if (form.phone && !form.phone.startsWith('08')) {
        form.phone = '08';
    }
    
    // Batasi panjang maksimal 13 digit
    if (form.phone.length > 13) {
        form.phone = form.phone.slice(0, 13);
    }
};

const submit = () => {
    if (validateForm()) {
        form.post("/register", {
            onFinish: () => form.reset("password", "password_confirmation"),
        });
    }
};
</script>

<template>
        <Head title="Register" />

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
            <div class="w-full sm:max-w-md mt-6 px-8 py-8 bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg shadow-xl rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <!-- Logo Section -->
                <div class="flex justify-center mb-8">
                    <ApplicationLogo class="w-20 h-20 transition-transform hover:scale-105" />
                </div>

                <!-- Welcome Text -->
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent dark:from-blue-400 dark:to-purple-400">
                        Buat Akun Baru
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">Daftar untuk mulai menggunakan aplikasi</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="relative">
                        <InputLabel for="name" value="Nama" class="text-gray-700 dark:text-gray-300" />
                        <div class="mt-1 relative rounded-lg shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <TextInput
                                id="name"
                                type="text"
                                class="pl-10 mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 focus:border-blue-500 focus:ring-blue-500 transition-colors"
                                v-model="form.name"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="Masukkan nama lengkap Anda"
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="relative">
                        <InputLabel for="email" value="Email" class="text-gray-700 dark:text-gray-300" />
                        <div class="mt-1 relative rounded-lg shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <TextInput
                                id="email"
                                type="email"
                                class="pl-10 mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 focus:border-blue-500 focus:ring-blue-500 transition-colors"
                                v-model="form.email"
                                required
                                autocomplete="username"
                                placeholder="Masukkan alamat email Anda"
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="relative">
                        <InputLabel for="phone" value="Nomor WhatsApp" class="text-gray-700 dark:text-gray-300" />
                        <div class="mt-1 relative rounded-lg shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <TextInput
                                id="phone"
                                type="tel"
                                class="pl-10 mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 focus:border-blue-500 focus:ring-blue-500 transition-colors"
                                v-model="form.phone"
                                required
                                @input="validatePhone"
                                placeholder="08xxxxxxxxxx"
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.phone" />
                    </div>

                    <div class="relative">
                        <InputLabel for="password" value="Password" class="text-gray-700 dark:text-gray-300" />
                        <div class="mt-1 relative rounded-lg shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <TextInput
                                id="password"
                                type="password"
                                class="pl-10 mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 focus:border-blue-500 focus:ring-blue-500 transition-colors"
                                v-model="form.password"
                                required
                                autocomplete="new-password"
                                placeholder="Buat password baru"
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="relative">
                        <InputLabel for="password_confirmation" value="Konfirmasi Password" class="text-gray-700 dark:text-gray-300" />
                        <div class="mt-1 relative rounded-lg shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <TextInput
                                id="password_confirmation"
                                type="password"
                                class="pl-10 mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 focus:border-blue-500 focus:ring-blue-500 transition-colors"
                                v-model="form.password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="Masukkan ulang password"
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>

                    <div class="space-y-4">
                        <PrimaryButton 
                            :class="{ 'opacity-75 cursor-not-allowed': form.processing }" 
                            :disabled="form.processing"
                            class="w-full justify-center py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-all duration-200"
                        >
                            <span v-if="form.processing" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memproses...
                            </span>
                            <span v-else>Daftar</span>
                        </PrimaryButton>

                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400">atau</span>
                            </div>
                        </div>

                        <div class="text-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Sudah punya akun?</span>
                            <Link
                                :href="route('login')"
                                class="ml-1 text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors font-medium"
                            >
                                Masuk sekarang
                            </Link>
                        </div>
                    </div>
                </form>
            </div>
        </div>
</template>

<style scoped>
.backdrop-blur-lg {
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
}
</style>

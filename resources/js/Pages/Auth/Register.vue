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
    <GuestLayout>
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
                    <p class="text-gray-600 dark:text-gray-400 mt-2">Daftar untuk memulai perjalanan Anda</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Name -->
                    <div>
                        <InputLabel for="name" value="Nama Lengkap" class="text-gray-700 dark:text-gray-300" />
                        <TextInput
                            id="name"
                            type="text"
                            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 focus:border-blue-500 focus:ring-blue-500 transition-colors"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="Masukkan nama lengkap Anda"
                        />
                        <InputError :message="form.errors.name" class="mt-1" />
                    </div>

                    <!-- Email -->
                    <div>
                        <InputLabel for="email" value="Email" class="text-gray-700 dark:text-gray-300" />
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 focus:border-blue-500 focus:ring-blue-500 transition-colors"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            placeholder="Masukkan alamat email Anda"
                        />
                        <InputError :message="form.errors.email" class="mt-1" />
                    </div>

                    <!-- Phone -->
                    <div>
                        <InputLabel for="phone" value="Nomor WhatsApp" class="text-gray-700 dark:text-gray-300" />
                        <TextInput
                            id="phone"
                            type="text"
                            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 focus:border-blue-500 focus:ring-blue-500 transition-colors"
                            v-model="form.phone"
                            required
                            placeholder="Contoh: 081234567890"
                        />
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Format: 08xx-xxxx-xxxx (format Indonesia)</p>
                        <InputError :message="form.errors.phone" class="mt-1" />
                    </div>

                    <!-- Password -->
                    <div>
                        <InputLabel for="password" value="Password" class="text-gray-700 dark:text-gray-300" />
                        <TextInput
                            id="password"
                            type="password"
                            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 focus:border-blue-500 focus:ring-blue-500 transition-colors"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                            placeholder="Minimal 8 karakter"
                        />
                        <InputError :message="form.errors.password" class="mt-1" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <InputLabel for="password_confirmation" value="Konfirmasi Password" class="text-gray-700 dark:text-gray-300" />
                        <TextInput
                            id="password_confirmation"
                            type="password"
                            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 focus:border-blue-500 focus:ring-blue-500 transition-colors"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Ulangi password Anda"
                        />
                        <InputError :message="form.errors.password_confirmation" class="mt-1" />
                    </div>

                    <div class="space-y-4">
                        <PrimaryButton 
                            :class="{ 'opacity-75 cursor-not-allowed': form.processing }" 
                            :disabled="form.processing"
                            class="w-full justify-center py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-all duration-200"
                        >
                            <span v-if="form.processing">Memproses...</span>
                            <span v-else>Daftar</span>
                        </PrimaryButton>

                        <div class="text-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Sudah punya akun?</span>
                            <Link
                                :href="route('login')"
                                class="ml-1 text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                            >
                                Masuk sekarang
                            </Link>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
.backdrop-blur-lg {
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
}
</style>

<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: "",
    password_confirmation: "",
});

const validateForm = () => {
    let isValid = true;
    form.clearErrors();

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

const submit = () => {
    if (validateForm()) {
        form.post("/reset-password", {
            onFinish: () => form.reset("password", "password_confirmation"),
        });
    }
};
</script>

<template>
    <GuestLayout>
        <Head title="Reset Password" />

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
            <div class="w-full sm:max-w-md mt-6 px-8 py-8 bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg shadow-xl rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <!-- Logo Section -->
                <div class="flex justify-center mb-8">
                    <ApplicationLogo class="w-20 h-20 transition-transform hover:scale-105" />
                </div>

                <!-- Welcome Text -->
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent dark:from-blue-400 dark:to-purple-400">
                        Reset Password
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">
                        Buat password baru untuk akun Anda
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="email" value="Email" class="text-gray-700 dark:text-gray-300" />
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 focus:border-blue-500 focus:ring-blue-500 transition-colors"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Masukkan alamat email Anda"
                        />
                        <InputError :message="form.errors.email" class="mt-1" />
                    </div>

                    <div>
                        <InputLabel for="password" value="Password Baru" class="text-gray-700 dark:text-gray-300" />
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

                    <div>
                        <InputLabel for="password_confirmation" value="Konfirmasi Password" class="text-gray-700 dark:text-gray-300" />
                        <TextInput
                            id="password_confirmation"
                            type="password"
                            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 focus:border-blue-500 focus:ring-blue-500 transition-colors"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Ulangi password baru Anda"
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
                            <span v-else>Reset Password</span>
                        </PrimaryButton>

                        <div class="text-center">
                            <Link
                                :href="route('login')"
                                class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                            >
                                Kembali ke halaman login
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

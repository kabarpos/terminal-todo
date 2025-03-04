<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
});

const validateForm = () => {
    let isValid = true;
    form.clearErrors();

    // Email validation
    if (!form.email) {
        form.setError("email", "Email is required");
        isValid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
        form.setError("email", "Invalid email format");
        isValid = false;
    }

    return isValid;
};

const submit = () => {
    if (validateForm()) {
        form.post("/forgot-password");
    }
};
</script>

<template>
        <Head title="Lupa Password" />

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
            <div class="w-full sm:max-w-md mt-6 px-8 py-8 bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg shadow-xl rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <!-- Logo Section -->
                <div class="flex justify-center mb-8">
                    <ApplicationLogo class="w-20 h-20 transition-transform hover:scale-105" />
                </div>

                <!-- Welcome Text -->
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent dark:from-blue-400 dark:to-purple-400">
                        Lupa Password?
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">
                        Masukkan email Anda dan kami akan mengirimkan link untuk mengatur ulang password
                    </p>
                </div>

                <div v-if="status" class="mb-6 p-4 rounded-lg bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-sm font-medium">
                    {{ status }}
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

                    <div class="space-y-4">
                        <PrimaryButton 
                            :class="{ 'opacity-75 cursor-not-allowed': form.processing }" 
                            :disabled="form.processing"
                            class="w-full justify-center py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-all duration-200"
                        >
                            <span v-if="form.processing">Memproses...</span>
                            <span v-else>Kirim Link Reset Password</span>
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
</template>

<style scoped>
.backdrop-blur-lg {
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
}
</style>

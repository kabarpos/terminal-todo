<script setup>
import { computed } from "vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post("/email/verification-notification");
};

const verificationLinkSent = computed(
    () => props.status === "verification-link-sent"
);
</script>

<template>
        <Head title="Verifikasi Email" />

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
            <div class="w-full sm:max-w-md mt-6 px-8 py-8 bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg shadow-xl rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <!-- Logo Section -->
                <div class="flex justify-center mb-8">
                    <ApplicationLogo class="w-20 h-20 transition-transform hover:scale-105" />
                </div>

                <!-- Welcome Text -->
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent dark:from-blue-400 dark:to-purple-400">
                        Verifikasi Email
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">
                        Terima kasih telah mendaftar! Sebelum memulai, mohon verifikasi alamat email Anda dengan mengklik link yang telah kami kirimkan melalui email.
                    </p>
                </div>

                <div v-if="verificationLinkSent" class="mb-6 p-4 rounded-lg bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-sm font-medium">
                    Link verifikasi baru telah dikirim ke alamat email Anda.
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-4">
                        <PrimaryButton 
                            :class="{ 'opacity-75 cursor-not-allowed': form.processing }" 
                            :disabled="form.processing"
                            class="w-full justify-center py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-all duration-200"
                        >
                            <span v-if="form.processing">Memproses...</span>
                            <span v-else>Kirim Ulang Email Verifikasi</span>
                        </PrimaryButton>

                        <div class="text-center">
                            <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                            >
                                Keluar
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

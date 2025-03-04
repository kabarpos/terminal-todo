<script setup>
import { Head, Link } from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
</script>

<template>
    <Head title="Terminal Todo - Internal Task Management" />

        <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
            <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <!-- Logo -->
                <div class="mb-8">
                    <ApplicationLogo class="w-24 h-24 mx-auto transform hover:scale-105 transition-transform duration-300" />
                </div>

                <!-- Welcome Text -->
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white">
                    <span class="block mb-2">Selamat Datang di</span>
                    <span class="block bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        Terminal Todo
                    </span>
                </h1>

                <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">
                    Platform manajemen tugas internal untuk meningkatkan produktivitas dan kolaborasi tim.
                </p>

                <!-- Action Buttons -->
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <Link
                        href="/login"
                        class="w-full sm:w-auto px-8 py-3 text-base font-medium rounded-lg text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 transition-all duration-200"
                    >
                        Masuk ke Aplikasi
                    </Link>
                    <Link
                        href="/register"
                        class="w-full sm:w-auto px-8 py-3 text-base font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200"
                    >
                        Daftar Akun Baru
                    </Link>
                </div>

                <!-- Company Info -->
                <p class="mt-8 text-sm text-gray-500 dark:text-gray-400">
                    © {{ new Date().getFullYear() }} Terminal Todo. Internal Company Platform
                </p>
            </div>
        </div>
</template>

<style scoped>
.backdrop-blur-lg {
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
}
</style>

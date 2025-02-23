<script setup>
import { Link } from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <nav class="bg-white border-b border-gray-100">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 sm:max-w-7xl">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <Link href="/">
                                <ApplicationLogo class="block h-9 w-auto" />
                            </Link>
                        </div>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 sm:flex sm:items-center">
                        <Link
                            href="/login"
                            class="text-sm text-gray-700 hover:text-gray-900"
                        >
                            Log in
                        </Link>

                        <Link
                            href="/register"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                        >
                            Register
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            <slot />
        </main>
    </div>
</template>

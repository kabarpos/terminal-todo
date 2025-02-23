<script setup>
import { Head, Link } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";

const features = [
    {
        title: "Easy to Use",
        description:
            "Our platform is designed to be intuitive and user-friendly",
        icon: "UserIcon",
    },
    {
        title: "Secure",
        description: "Built with security in mind to protect your data",
        icon: "ShieldCheckIcon",
    },
    // ... tambahkan fitur lainnya
];
</script>

<template>
    <Head title="Home" />

    <GuestLayout>
        <!-- Hero Section -->
        <div class="relative overflow-hidden">
            <div class="max-w-full mx-auto sm:max-w-7xl">
                <div
                    class="relative z-10 pb-8 sm:pb-16 md:pb-20 lg:w-full lg:pb-28 xl:pb-32"
                >
                    <main
                        class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28"
                    >
                        <div class="sm:text-center lg:text-left">
                            <h1
                                class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl"
                            >
                                <span class="block">Welcome to</span>
                                <span class="block text-indigo-600"
                                    >Your Application</span
                                >
                            </h1>
                            <p
                                class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0"
                            >
                                Description of your application and its main
                                benefits.
                            </p>
                            <div
                                class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start"
                            >
                                <div class="rounded-md shadow">
                                    <Link
                                        href="/register"
                                        class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:py-4 md:text-lg md:px-10"
                                    >
                                        Get Started
                                    </Link>
                                </div>
                                <div class="mt-3 sm:mt-0 sm:ml-3">
                                    <Link
                                        href="/login"
                                        class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-gray-50 md:py-4 md:text-lg md:px-10"
                                    >
                                        Login
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="py-12 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:text-center">
                    <h2
                        class="text-base text-indigo-600 font-semibold tracking-wide uppercase"
                    >
                        Features
                    </h2>
                    <p
                        class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl"
                    >
                        Everything you need
                    </p>
                </div>

                <div class="mt-10">
                    <div
                        class="space-y-10 md:space-y-0 md:grid md:grid-cols-2 md:gap-x-8 md:gap-y-10"
                    >
                        <div
                            v-for="feature in features"
                            :key="feature.title"
                            class="relative"
                        >
                            <dt>
                                <div
                                    class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white"
                                >
                                    <component
                                        :is="feature.icon"
                                        class="h-6 w-6"
                                        aria-hidden="true"
                                    />
                                </div>
                                <p
                                    class="ml-0 sm:ml-16 text-lg leading-6 font-medium text-gray-900"
                                >
                                    {{ feature.title }}
                                </p>
                            </dt>
                            <dd
                                class="mt-2 ml-0 sm:ml-16 text-base text-gray-500"
                            >
                                {{ feature.description }}
                            </dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

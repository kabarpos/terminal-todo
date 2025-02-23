<template>
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
        <!-- Primary Navigation Menu -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <Link :href="route('dashboard')">
                            <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                        </Link>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </NavLink>

                        <NavLink :href="route('tasks.index')" :active="route().current('tasks.*')">
                            Tasks
                        </NavLink>

                        <!-- Content Management Links -->
                        <template v-if="hasRole(['Super Admin', 'Content Manager'])">
                            <div class="hidden sm:flex sm:items-center">
                                <div class="relative" @click.away="openContent = false">
                                    <button
                                        @click="openContent = !openContent"
                                        class="inline-flex items-center px-3 py-2 text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"
                                        :class="{ 'text-gray-900 dark:text-gray-100': route().current('categories.*') || route().current('platforms.*') }"
                                    >
                                        Content Management

                                        <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    <div v-show="openContent" class="absolute z-50 mt-2 w-48 rounded-md shadow-lg origin-top-right right-0">
                                        <div class="rounded-md ring-1 ring-black ring-opacity-5 py-1 bg-white dark:bg-gray-700">
                                            <Link
                                                :href="route('categories.index')"
                                                class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 transition duration-150 ease-in-out"
                                                :class="{ 'bg-gray-100 dark:bg-gray-800': route().current('categories.*') }"
                                            >
                                                Categories
                                            </Link>
                                            <Link
                                                :href="route('platforms.index')"
                                                class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 transition duration-150 ease-in-out"
                                                :class="{ 'bg-gray-100 dark:bg-gray-800': route().current('platforms.*') }"
                                            >
                                                Platforms
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

const showingNavigationDropdown = ref(false);
const openContent = ref(false);
const openAdmin = ref(false);

const hasRole = (roles) => {
    const userRoles = usePage().props.auth.user.roles.map(role => role.name);
    return roles.some(role => userRoles.includes(role));
};
</script> 
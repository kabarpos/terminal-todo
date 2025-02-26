<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import AppNavigation from '@/Components/Navigation/AppNavigation.vue';
import { Head } from "@inertiajs/vue3";

const isSidebarOpen = ref(false);
const isProfileMenuOpen = ref(false);
const showingNavigationDropdown = ref(false);

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    auth: {
        type: Object,
        required: true
    },
    settings: {
        type: Object,
        default: () => ({})
    }
});

// Gunakan usePage untuk mendapatkan data auth
const page = usePage();
const auth = computed(() => page.props.auth);

// Computed untuk user yang sudah terautentikasi
const authenticatedUser = computed(() => auth.value?.user);

// Dark Mode State
const isDark = ref(false);

// Fungsi untuk mengupdate tema
const updateTheme = (dark) => {
    if (dark) {
        document.documentElement.classList.add('dark');
        document.documentElement.style.colorScheme = 'dark';
        localStorage.theme = 'dark';
    } else {
        document.documentElement.classList.remove('dark');
        document.documentElement.style.colorScheme = 'light';
        localStorage.theme = 'light';
    }
};

// Watch perubahan isDark
watch(isDark, (newValue) => {
    updateTheme(newValue);
});

onMounted(() => {
    // Check sistem preferensi dan localStorage
    const darkMode = localStorage.theme === 'dark' || 
        (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
    
    isDark.value = darkMode;
    updateTheme(darkMode);

    // Listen untuk perubahan preferensi sistem
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        if (!localStorage.theme) { // Hanya update jika user belum set preferensi
            isDark.value = e.matches;
        }
    });

    console.log('Auth Data:', auth.value);
    console.log('User Data:', auth.value?.user);
    console.log('User Roles:', auth.value?.user?.roles);
});

const logout = () => {
    router.post(route('logout'));
};

// Computed untuk mengecek role admin
const isAdmin = computed(() => {
    const roles = auth.value?.user?.roles || [];
    console.log('Checking admin role:', roles);
    
    if (!roles || !Array.isArray(roles)) {
        console.log('No roles or invalid roles format');
        return false;
    }
    
    const hasAdminRole = roles.includes('admin');
    console.log('Has admin role:', hasAdminRole);
    return hasAdminRole;
});

// Computed untuk user data
const userData = computed(() => {
    return auth.value?.user;
});

const toggleDarkMode = () => {
    isDark.value = !isDark.value;
};

// Computed untuk title dan logo dari settings yang diberikan
const websiteSettings = computed(() => ({
    title: props.settings?.site_title || usePage().props.settings?.site_title || 'Application',
    logo: props.settings?.site_logo || usePage().props.settings?.site_logo || null,
    favicon: props.settings?.site_favicon || usePage().props.settings?.site_favicon || null
}));

const getAssetUrl = (path) => {
    if (!path) return null;
    return `/storage/${path}`;
};

// Watch untuk perubahan settings
watch(() => usePage().props.settings, (newSettings) => {
    if (newSettings) {
        // Update favicon jika berubah
        if (newSettings.site_favicon) {
            const link = document.querySelector("link[rel~='icon']");
            if (link) {
                link.href = `/storage/${newSettings.site_favicon}`;
            }
        }
    }
}, { deep: true });
</script>

<template>
    <Head :title="`${title} - ${websiteSettings.title}`" replace />

    <div class="min-h-screen bg-light-bg dark:bg-dark-bg theme-transition">
        <!-- Sidebar -->
        <div
            class="fixed inset-y-0 w-64 flex-col bg-white dark:bg-dark-bg border-r border-light-border dark:border-dark-border shadow-lg transition-all duration-300 z-50"
            :class="{ 'hidden md:flex': !isSidebarOpen, flex: isSidebarOpen }"
        >
            <!-- Sidebar header -->
            <div class="flex h-16 shrink-0 items-center px-6 border-b border-light-border dark:border-dark-border">
                <img 
                    v-if="websiteSettings.logo" 
                    :src="getAssetUrl(websiteSettings.logo)" 
                    class="h-8 w-auto" 
                    :alt="websiteSettings.title"
                />
                <ApplicationLogo v-else class="h-8 w-auto" />
                <span class="ml-2 text-xl font-semibold text-primary-500">
                    {{ websiteSettings.title }}
                </span>
            </div>

            <!-- Sidebar content -->
            <div class="flex flex-1 flex-col overflow-y-auto custom-scrollbar">
                <nav class="flex-1 px-4 py-4">
                    <AppNavigation v-if="authenticatedUser" :user="authenticatedUser" />
                </nav>

                <!-- Bottom Section with User Profile -->
                <div class="mt-auto space-y-4 p-4 border-t border-light-border dark:border-dark-border">
                    <!-- Dark Mode Toggle -->
                        <button
                        @click="toggleDarkMode"
                        class="w-full flex items-center px-2 py-2 text-sm text-light-text dark:text-dark-text rounded-lg hover:bg-light-card dark:hover:bg-dark-card transition-colors"
                    >
                        <div class="p-1.5 rounded-lg bg-light-card dark:bg-dark-card">
                            <svg v-if="!isDark" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </div>
                        <span class="ml-3">{{ isDark ? 'Dark Mode' : 'Light Mode' }}</span>
                        </button>

                    <!-- User Profile -->
                    <div v-if="authenticatedUser" class="relative">
                        <button 
                            @click="isProfileMenuOpen = !isProfileMenuOpen"
                            class="flex items-center w-full px-2 py-2 text-sm text-light-text dark:text-dark-text rounded-lg hover:bg-light-card dark:hover:bg-dark-card transition-colors"
                        >
                            <img
                                :src="authenticatedUser.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(authenticatedUser.name)}&background=random&color=fff`"
                                :alt="authenticatedUser.name"
                                class="h-8 w-8 rounded-full object-cover"
                            />
                            <div class="ml-3 flex-1 text-left">
                                <p class="text-sm font-medium text-light-text dark:text-dark-text">{{ authenticatedUser.name }}</p>
                                <p class="text-xs text-light-text/60 dark:text-dark-text/60">{{ authenticatedUser.email }}</p>
                                        </div>
                                        <svg 
                                class="h-5 w-5 text-light-text/60 dark:text-dark-text/60 transition-transform duration-200" 
                                            :class="{ 'rotate-180': isProfileMenuOpen }"
                                            xmlns="http://www.w3.org/2000/svg" 
                                            viewBox="0 0 20 20" 
                                            fill="currentColor"
                                        >
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    
                                    <!-- Profile Dropdown Menu -->
                                    <div 
                                        v-show="isProfileMenuOpen"
                            class="absolute bottom-full left-0 right-0 mb-2 bg-light-card dark:bg-dark-card rounded-lg shadow-lg py-1 border border-light-border dark:border-dark-border"
                                    >
                                        <Link
                                            :href="route('profile.edit')"
                                class="flex items-center px-4 py-2 text-sm text-light-text dark:text-dark-text hover:bg-light-bg dark:hover:bg-dark-bg hover:text-primary-500 transition-colors"
                                        >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                Edit Profile
                                        </Link>
                                        <button
                                            @click="logout"
                                class="w-full flex items-center px-4 py-2 text-sm text-light-text dark:text-dark-text hover:bg-light-bg dark:hover:bg-dark-bg hover:text-red-500 transition-colors"
                                        >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                                </svg>
                                                Logout
                                        </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overlay untuk mobile saat sidebar terbuka -->
        <div 
            v-if="isSidebarOpen" 
            class="fixed inset-0 bg-black/50 md:hidden z-40"
            @click="isSidebarOpen = false"
        ></div>

        <!-- Main content -->
        <div class="md:pl-64 min-h-screen bg-light-bg dark:bg-dark-bg">
            <!-- Top header -->
            <div class="sticky top-0 z-40 bg-white/80 dark:bg-dark-bg/80 border-b border-light-border dark:border-dark-border backdrop-blur-lg">
                <div class="flex h-16 items-center justify-between px-4 md:px-6">
                    <!-- Mobile menu button and title -->
                    <div class="flex items-center">
                        <button
                            @click="isSidebarOpen = !isSidebarOpen"
                            class="md:hidden rounded-lg p-2 text-light-text/60 dark:text-dark-text/60 hover:text-light-text dark:hover:text-dark-text hover:bg-light-card dark:hover:bg-dark-card focus:outline-none"
                        >
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                    
                    <div class="flex-1 px-4">
                        <!-- Slot untuk header content -->
                        <slot name="header"></slot>
                    </div>
                </div>
            </div>

            <!-- Page content -->
            <main class="py-6 px-4 md:px-6">
                <div class="w-full mx-auto">
                <slot />
                </div>
            </main>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: rgba(107, 114, 128, 0.3) transparent;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: rgba(107, 114, 128, 0.3);
    border-radius: 2px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: rgba(107, 114, 128, 0.5);
}

.theme-transition {
    transition: background-color 0.3s ease,
                color 0.3s ease,
                border-color 0.3s ease;
}
</style>

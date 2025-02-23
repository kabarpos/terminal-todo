<script setup>
import { Link } from "@inertiajs/vue3";
import { computed } from 'vue';
import {
    ChartBarIcon,
    UserGroupIcon,
    ShieldCheckIcon,
    Cog6ToothIcon,
    HomeIcon
} from "@heroicons/vue/24/outline";

const props = defineProps({
    user: {
        type: Object,
        required: true,
        validator(value) {
            return value && typeof value === 'object';
        }
    }
});

// Check apakah user adalah admin
const isAdmin = computed(() => {
    return Array.isArray(props.user.roles) && props.user.roles.includes('admin');
});

// Definisi seluruh menu aplikasi
const navigationConfig = {
    // Menu yang bisa diakses semua user yang login
    user: [
        {
            name: "Dashboard",
            href: route('dashboard'),
            icon: ChartBarIcon
        }
    ],
    
    // Menu khusus admin
    admin: [
        {
            name: "User Management",
            href: route('admin.users.index'),
            icon: UserGroupIcon
        },
        {
            name: "Role Management",
            href: route('admin.roles.index'),
            icon: ShieldCheckIcon
        },
        {
            name: "Website Settings",
            href: route('admin.settings.index'),
            icon: Cog6ToothIcon
        }
    ]
};

// Mendapatkan menu berdasarkan role
const getNavigationMenus = computed(() => {
    let menus = {
        user: navigationConfig.user
    };

    // Jika user adalah admin, tambahkan menu admin
    if (isAdmin.value) {
        menus.admin = navigationConfig.admin;
    }

    return menus;
});
</script>

<template>
    <nav class="space-y-6">
        <!-- User Menu Section -->
        <div>
            <div class="px-3 mb-2">
                <p class="text-xs font-medium text-light-text/60 dark:text-dark-text/60 uppercase tracking-wider">
                    Menu
                </p>
            </div>
            <div class="space-y-1">
                <Link
                    v-for="item in getNavigationMenus.user"
                    :key="item.name"
                    :href="item.href"
                    class="flex items-center px-3 py-2 text-sm text-light-text dark:text-dark-text hover:text-primary-500 hover:bg-primary-50 dark:hover:bg-primary-500/10 rounded-lg transition-colors"
                    :class="{ 'bg-primary-50 dark:bg-primary-500/10 text-primary-500': route().current(item.href) }"
                >
                    <component :is="item.icon" class="h-5 w-5 mr-2" />
                    {{ item.name }}
                </Link>
            </div>
        </div>

        <!-- Admin Menu Section -->
        <div v-if="isAdmin">
            <div class="px-3 mb-2">
                <p class="text-xs font-medium text-light-text/60 dark:text-dark-text/60 uppercase tracking-wider">
                    Admin Area
                </p>
            </div>
            <div class="space-y-1">
                <Link
                    v-for="item in getNavigationMenus.admin"
                    :key="item.name"
                    :href="item.href"
                    class="flex items-center px-3 py-2 text-sm text-light-text dark:text-dark-text hover:text-primary-500 hover:bg-primary-50 dark:hover:bg-primary-500/10 rounded-lg transition-colors"
                    :class="{ 'bg-primary-50 dark:bg-primary-500/10 text-primary-500': route().current(item.href) }"
                >
                    <component :is="item.icon" class="h-5 w-5 mr-2" />
                    {{ item.name }}
                </Link>
            </div>
        </div>
    </nav>
</template> 
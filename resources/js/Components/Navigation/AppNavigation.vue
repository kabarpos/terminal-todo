<script setup>
import { Link } from "@inertiajs/vue3";
import { computed } from 'vue';
import {
    ChartBarIcon,
    UserGroupIcon,
    ShieldCheckIcon,
    Cog6ToothIcon,
    HomeIcon,
    ClipboardDocumentListIcon,
    TagIcon,
    ShareIcon,
    CalendarIcon,
    NewspaperIcon,
    UsersIcon,
    ChartPieIcon,
    GlobeAltIcon,
    PhotoIcon
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

// Check roles
const isAdmin = computed(() => {
    return props.user.roles && props.user.roles.includes('Super Admin');
});
const isContentManager = computed(() => {
    return props.user.roles && props.user.roles.includes('Content Manager');
});

// Check permissions
const hasPermission = (permission) => {
    return props.user.permissions && props.user.permissions.includes(permission);
};

// Definisi seluruh menu aplikasi
const navigationConfig = {
    // Menu yang bisa diakses semua user yang login
    user: [
        {
            name: "Dashboard",
            href: route('dashboard'),
            icon: HomeIcon,
            permission: "view-dashboard"
        },
        {
            name: "Calendar",
            href: route('calendar.index'),
            icon: CalendarIcon,
            permission: "view-calendar"
        },
        {
            name: "Tasks",
            href: route('tasks.index'),
            icon: ClipboardDocumentListIcon,
            permission: "view-task"
        },
        {
            name: "Teams",
            href: route('teams.index'),
            icon: UserGroupIcon,
            permission: "view-team"
        },
        {
            name: "Categories",
            href: route('categories.index'),
            icon: TagIcon,
            permission: "view-category"
        },
        {
            name: "Platforms",
            href: route('platforms.index'),
            icon: GlobeAltIcon,
            permission: "view-platform"
        },
        {
            name: "Social Accounts",
            href: route('social-accounts.index'),
            icon: UsersIcon,
            permission: "view-social-account"
        },
        {
            name: "News Feed",
            href: route('news-feeds.index'),
            icon: NewspaperIcon,
            permission: "view-newsfeed"
        },
        {
            name: "Social Media Reports",
            href: route('social-media-reports.index'),
            icon: ChartBarIcon,
            permission: "view-social-media-report"
        },
        {
            name: "Media Library",
            href: route('media.index'),
            icon: PhotoIcon,
            permission: "view-media"
        },
        {
            name: "Analytics",
            href: route('social-analytics.index'),
            icon: ChartPieIcon,
            permission: "view-analytics"
        },
        {
            name: "Input Metrik",
            href: route('metric-data.create'),
            icon: ChartBarIcon,
            permission: "create-metric"
        }
    ],

    // Menu khusus admin
    admin: [
        {
            name: "User Management",
            href: route('admin.users.index'),
            icon: UserGroupIcon,
            permission: 'view-users'
        },
        {
            name: "Role Management",
            href: route('admin.roles.index'),
            icon: ShieldCheckIcon,
            permission: 'view-roles'
        },
        {
            name: "Settings",
            href: route('admin.settings.index'),
            icon: Cog6ToothIcon,
            permission: 'manage-settings'
        }
    ]
};

// Mendapatkan menu berdasarkan role dan permission
const getNavigationMenus = computed(() => {
    return {
        user: navigationConfig.user,
        admin: navigationConfig.admin
    };
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
        <div>
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
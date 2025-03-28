<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { usePermission } from '@/Composables/usePermission';
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
        default: () => ({}),
        validator(value) {
            // Lebih lenient, hanya memastikan value adalah object
            return value && typeof value === 'object';
        }
    }
});

const page = usePage();
const { hasPermission } = usePermission();

// Check roles
const isAdmin = computed(() => {
    // Validasi bahwa roles ada dan memiliki metode includes
    return props.user?.roles && Array.isArray(props.user.roles) && props.user.roles.includes('Super Admin');
});

const isContentManager = computed(() => {
    // Validasi bahwa roles ada dan memiliki metode includes
    return props.user?.roles && Array.isArray(props.user.roles) && props.user.roles.includes('Content Manager');
});

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
            name: "Posting Reports",
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
            name: "Input Metrik",
            href: route('metric-data.index'),
            icon: ChartBarIcon,
            permission: "view-metric-data"
        },
        {
            name: "Analytics",
            href: route('social-analytics.index'),
            icon: ChartPieIcon,
            permission: "view-analytics"
        }
    ],

    // Menu khusus admin
    admin: [
        {
            name: "User Management",
            href: route('admin.users.index'),
            icon: UserGroupIcon,
            permission: 'manage-users'
        },
        {
            name: "Role Management",
            href: route('admin.roles.index'),
            icon: ShieldCheckIcon,
            permission: 'manage-roles'
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
    // Filter menu berdasarkan permission - kita tidak perlu cek isAdmin.value 
    // karena sudah ditangani di dalam fungsi hasPermission
    const filteredUserMenu = navigationConfig.user.filter(item => 
        hasPermission(item.permission)
    );

    const filteredAdminMenu = navigationConfig.admin.filter(item => 
        hasPermission(item.permission)
    );

    return {
        user: filteredUserMenu,
        admin: filteredAdminMenu
    };
});

// Memeriksa apakah ada menu admin yang tersedia
const hasAdminMenu = computed(() => {
    return getNavigationMenus.value.admin.length > 0;
});

const navigationItems = computed(() => [
    {
        name: 'Dashboard',
        route: 'dashboard',
        icon: 'fas fa-home',
        permission: 'view-dashboard'
    },
    {
        name: 'Tasks',
        route: 'tasks.index',
        icon: 'fas fa-tasks',
        permission: 'view-task'
    },
    {
        name: 'Calendar',
        route: 'calendar.index',
        icon: 'fas fa-calendar',
        permission: 'view-calendar'
    },
    {
        name: 'News Feed',
        route: 'news-feeds.index',
        icon: 'fas fa-newspaper',
        permission: 'view-newsfeed'
    },
    {
        name: 'Teams',
        route: 'teams.index',
        icon: 'fas fa-users',
        permission: 'view-team'
    },
    {
        name: 'Media',
        route: 'media.index',
        icon: 'fas fa-photo-video',
        permission: 'view-media'
    },
    {
        name: 'Social Accounts',
        route: 'social-accounts.index',
        icon: 'fas fa-user-circle',
        permission: 'view-social-account'
    },
    {
        name: 'Analytics',
        route: 'analytics.index',
        icon: 'fas fa-chart-line',
        permission: 'view-analytics'
    }
]);

const adminItems = computed(() => [
    {
        name: 'Users',
        route: 'admin.users.index',
        icon: 'fas fa-user',
        permission: 'manage-users'
    },
    {
        name: 'Roles',
        route: 'admin.roles.index',
        icon: 'fas fa-user-shield',
        permission: 'manage-roles'
    },
    {
        name: 'Settings',
        route: 'admin.settings.index',
        icon: 'fas fa-cog',
        permission: 'manage-settings'
    }
]);

const contentItems = computed(() => [
    {
        name: 'Categories',
        route: 'categories.index',
        icon: 'fas fa-folder',
        permission: 'manage-category'
    },
    {
        name: 'Platforms',
        route: 'platforms.index',
        icon: 'fas fa-share-alt',
        permission: 'manage-platform'
    }
]);

const currentRouteName = computed(() => page.component);
</script>

<template>
    <nav class="space-y-6">
        <!-- User Menu Section -->
        <div v-if="getNavigationMenus.user.length > 0">
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
        <div v-if="hasAdminMenu">
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
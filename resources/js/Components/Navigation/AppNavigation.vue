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
        default: () => ({}),
        validator(value) {
            // Lebih lenient, hanya memastikan value adalah object
            return value && typeof value === 'object';
        }
    }
});

// Check roles
const isAdmin = computed(() => {
    // Validasi bahwa roles ada dan memiliki metode includes
    return props.user?.roles && Array.isArray(props.user.roles) && props.user.roles.includes('Super Admin');
});

const isContentManager = computed(() => {
    // Validasi bahwa roles ada dan memiliki metode includes
    return props.user?.roles && Array.isArray(props.user.roles) && props.user.roles.includes('Content Manager');
});

// Check permissions
const hasPermission = (permission) => {
    // Logger untuk debugging permission
    console.log('Checking permission:', permission, 'for user:', props.user?.name);
    console.log('User roles:', props.user?.roles);
    console.log('User permissions:', props.user?.permissions?.length);
    
    // Jika pengguna adalah Super Admin, izinkan semua
    if (isAdmin.value) {
        console.log('User is Super Admin, granting permission');
        return true;
    }
    
    // Jika tidak ada permission yang dibutuhkan, izinkan akses
    if (!permission) {
        console.log('No permission required, granting access');
        return true;
    }

    // Jika user tidak memiliki permissions, tolak akses
    if (!props.user?.permissions || !Array.isArray(props.user.permissions)) {
        console.log('User has no permissions array, denying access');
        return false;
    }

    // Helper untuk menghasilkan semua kemungkinan format dari permission
    const generatePermissionVariants = (perm) => {
        let variants = new Set();
        
        // Simpan versi asli
        variants.add(perm);
        
        // Versi dengan spasi dan tanpa spasi
        const withoutSpaces = perm.replace(/\s+/g, '-');
        const withSpaces = perm.replace(/-/g, ' ');
        
        variants.add(withoutSpaces);
        variants.add(withSpaces);
        
        // Versi dengan huruf kecil
        variants.add(perm.toLowerCase());
        variants.add(withoutSpaces.toLowerCase());
        variants.add(withSpaces.toLowerCase());
        
        // Jika permission memiliki spasi, coba balik kata-katanya dan buat format dengan tanda -
        if (perm.includes(' ')) {
            const parts = perm.split(' ');
            if (parts.length === 2) {
                const reversed = `${parts[1]}-${parts[0]}`;
                variants.add(reversed);
                variants.add(reversed.toLowerCase());
            }
        }
        
        // Jika permission memiliki tanda -, coba balik kata-katanya dan buat format dengan spasi
        if (perm.includes('-')) {
            const parts = perm.split('-');
            if (parts.length === 2) {
                const reversed = `${parts[1]} ${parts[0]}`;
                variants.add(reversed);
                variants.add(reversed.toLowerCase());
            }
        }
        
        return Array.from(variants);
    };
    
    // Normalisasi permission yang diminta
    const requestedVariants = generatePermissionVariants(permission);
    
    // Periksa apakah user memiliki salah satu dari varian permission
    for (const userPerm of props.user.permissions) {
        const userPermVariants = generatePermissionVariants(userPerm);
        
        // Periksa apakah ada intersection antara requested variants dan user permission variants
        const hasMatch = requestedVariants.some(reqVar => userPermVariants.includes(reqVar));
        
        if (hasMatch) {
            console.log('Permission match found:', { 
                requested: permission, 
                matched: userPerm,
                requestedVariants,
                userPermVariants 
            });
            return true;
        }
    }
    
    console.log('No permission match found for:', permission);
    return false;
};

// Definisi seluruh menu aplikasi
const navigationConfig = {
    // Menu yang bisa diakses semua user yang login
    user: [
        {
            name: "Dashboard",
            href: route('dashboard'),
            icon: HomeIcon,
            permission: "view dashboard"
        },
        {
            name: "Calendar",
            href: route('calendar.index'),
            icon: CalendarIcon,
            permission: "view calendar"
        },
        {
            name: "Tasks",
            href: route('tasks.index'),
            icon: ClipboardDocumentListIcon,
            permission: "view task"
        },
        {
            name: "Teams",
            href: route('teams.index'),
            icon: UserGroupIcon,
            permission: "view team"
        },
        {
            name: "Categories",
            href: route('categories.index'),
            icon: TagIcon,
            permission: "view category"
        },
        {
            name: "Platforms",
            href: route('platforms.index'),
            icon: GlobeAltIcon,
            permission: "view platform"
        },
        {
            name: "Social Accounts",
            href: route('social-accounts.index'),
            icon: UsersIcon,
            permission: "view social account"
        },
        {
            name: "News Feed",
            href: route('news-feeds.index'),
            icon: NewspaperIcon,
            permission: "view newsfeed"
        },
        {
            name: "Reports",
            href: route('social-media-reports.index'),
            icon: ChartBarIcon,
            permission: "view social media report"
        },
        {
            name: "Media Library",
            href: route('media.index'),
            icon: PhotoIcon,
            permission: "view asset"
        },
        {
            name: "Input Metrik",
            href: route('metric-data.index'),
            icon: ChartBarIcon,
            permission: "manage-metric-data"
        },
        {
            name: "Analytics",
            href: route('social-analytics.index'),
            icon: ChartPieIcon,
            permission: "view analytics"
        }
    ],

    // Menu khusus admin
    admin: [
        {
            name: "User Management",
            href: route('admin.users.index'),
            icon: UserGroupIcon,
            permission: 'view users'
        },
        {
            name: "Role Management",
            href: route('admin.roles.index'),
            icon: ShieldCheckIcon,
            permission: 'view roles'
        },
        {
            name: "Settings",
            href: route('admin.settings.index'),
            icon: Cog6ToothIcon,
            permission: 'manage settings'
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
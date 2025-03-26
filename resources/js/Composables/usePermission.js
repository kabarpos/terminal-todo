import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export function usePermission() {
    const page = usePage();

    const hasPermission = (permission) => {
        const user = page.props.auth.user;
        
        // Super Admin bypass all permissions
        if (user.roles.includes('Super Admin')) {
            return true;
        }

        // Normalize permission format
        const normalizePermission = (perm) => {
            return perm.toLowerCase().replace(/\s+/g, '-');
        };

        const normalizedRequestedPerm = normalizePermission(permission);
        const userPermissions = user.permissions || [];

        // Check exact match first
        if (userPermissions.includes(normalizedRequestedPerm)) {
            return true;
        }

        // Check if user has 'manage-' permission when checking for 'view-'
        if (normalizedRequestedPerm.startsWith('view-')) {
            const managePermission = normalizedRequestedPerm.replace('view-', 'manage-');
            return userPermissions.includes(managePermission);
        }

        return false;
    };

    const userPermissions = computed(() => {
        return page.props.auth.user?.permissions || [];
    });

    return {
        hasPermission,
        userPermissions
    };
} 
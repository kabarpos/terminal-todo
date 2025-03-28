import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

// Composable untuk mengelola permission
export function usePermission() {
    // Helper untuk mengecek apakah user memiliki permission tertentu
    const hasPermission = (permission) => {
        if (!permission) return false;
        
        const userPermissions = usePage().props.auth?.user?.permissions || [];
        
        // Jika user adalah Admin, berikan semua akses
        const userRoles = usePage().props.auth?.user?.roles || [];
        if (userRoles.includes('Admin')) {
            return true;
        }
        
        // Handle multiple permissions dengan separator pipe (|)
        if (typeof permission === 'string' && permission.includes('|')) {
            // Jika format permission-nya "a|b|c", cukup memiliki salah satunya
            const permissions = permission.split('|');
            return permissions.some(p => hasPermission(p));
        }
        
        const normalizedRequestedPerm = normalizePermission(permission);
        
        // Normalisasi dan cek exact match
        const normalizedUserPermissions = userPermissions.map(p => normalizePermission(p));
        if (normalizedUserPermissions.includes(normalizedRequestedPerm)) {
            return true;
        }
        
        // Untuk permission 'view-', cek juga apakah user memiliki 'manage-' setara
        if (normalizedRequestedPerm.startsWith('view-')) {
            const managePermission = normalizedRequestedPerm.replace('view-', 'manage-');
            return normalizedUserPermissions.includes(managePermission);
        }
        
        return false;
    };
    
    // Helper untuk menormalisasi format permission (dash atau spasi)
    const normalizePermission = (perm) => {
        if (!perm) return '';
        
        // Ubah ke lowercase dan normalisasi format dengan dash
        let normalized = perm.toLowerCase();
        
        // Ubah spasi ke dash jika ada
        normalized = normalized.replace(/\s+/g, '-');
        
        // Pastikan tidak ada dash berlebih
        normalized = normalized.replace(/--+/g, '-');
        
        // Hapus dash di awal atau akhir
        normalized = normalized.replace(/^-+|-+$/g, '');
        
        return normalized;
    };
    
    // Mengembalikan fungsi-fungsi yang diperlukan
    return {
        hasPermission,
        normalizePermission
    };
} 
<script setup>
import { computed, useSlots } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    permission: {
        type: [String, Array],
        required: true
    },
    requireAll: {
        type: Boolean,
        default: false
    }
});

const page = usePage();
const auth = computed(() => page.props.auth);
const authenticatedUser = computed(() => auth.value.user);

// Helper function to normalize permission format
const normalizePermission = (permission) => {
    const withDash = permission.replace(/\s+/g, '-');
    const withSpace = permission.replace(/-/g, ' ');
    return [permission, withDash, withSpace];
};

// Check if user has required permissions
const hasPermission = computed(() => {
    // No user or no permissions, deny access
    if (!authenticatedUser.value || !authenticatedUser.value.permissions) {
        return false;
    }
    
    // Super Admin has all permissions
    if (authenticatedUser.value.roles && authenticatedUser.value.roles.includes('Super Admin')) {
        return true;
    }
    
    // Get user permissions
    const userPermissions = authenticatedUser.value.permissions || [];
    
    // Convert single permission to array
    const requiredPermissions = Array.isArray(props.permission) 
        ? props.permission 
        : [props.permission];
    
    // Check if user has required permissions
    if (props.requireAll) {
        // User must have ALL permissions
        return requiredPermissions.every(permission => {
            const normalizedPermissions = normalizePermission(permission);
            return normalizedPermissions.some(p => userPermissions.includes(p));
        });
    } else {
        // User must have AT LEAST ONE permission
        return requiredPermissions.some(permission => {
            const normalizedPermissions = normalizePermission(permission);
            return normalizedPermissions.some(p => userPermissions.includes(p));
        });
    }
});

// Check if we have default slot
const slots = useSlots();
const hasDefaultSlot = computed(() => Boolean(slots.default));
</script>

<template>
    <!-- Render slot content only if user has required permissions -->
    <template v-if="hasPermission && hasDefaultSlot">
        <slot />
    </template>
</template> 
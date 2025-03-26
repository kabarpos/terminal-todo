<script setup>
import { computed, useSlots } from 'vue';
import { usePermission } from '@/Composables/usePermission';

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

const slots = useSlots();
const { hasPermission } = usePermission();

const hasDefaultSlot = computed(() => {
    return !!slots.default;
});

const checkPermission = computed(() => {
    if (Array.isArray(props.permission)) {
        return props.requireAll
            ? props.permission.every(p => hasPermission(p))
            : props.permission.some(p => hasPermission(p));
    }
    return hasPermission(props.permission);
});
</script>

<template>
    <template v-if="checkPermission && hasDefaultSlot">
        <slot />
    </template>
</template> 
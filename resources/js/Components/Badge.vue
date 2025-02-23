<script setup>
defineProps({
    variant: {
        type: String,
        default: 'default', // 'default', 'primary', 'success', 'warning', 'danger'
    },
    size: {
        type: String,
        default: 'md', // 'sm', 'md', 'lg'
    },
});
</script>

<template>
    <span
        :class="[
            'inline-flex items-center justify-center font-medium',
            // Base styles
            'rounded-full',
            // Variants
            variant === 'default' && 'bg-neutral-100 text-neutral-700 dark:bg-neutral-700 dark:text-neutral-200',
            variant === 'primary' && 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400',
            variant === 'success' && 'bg-success-100 text-success-700 dark:bg-success-900/30 dark:text-success-400',
            variant === 'warning' && 'bg-warning-100 text-warning-700 dark:bg-warning-900/30 dark:text-warning-400',
            variant === 'danger' && 'bg-error-100 text-error-700 dark:bg-error-900/30 dark:text-error-400',
            // Sizes
            size === 'sm' && 'text-xs px-2 py-0.5',
            size === 'md' && 'text-sm px-2.5 py-0.5',
            size === 'lg' && 'text-base px-3 py-1',
        ]"
    >
        <slot />
    </span>
</template> 
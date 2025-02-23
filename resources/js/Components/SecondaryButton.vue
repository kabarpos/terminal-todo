<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
    variant: {
        type: String,
        default: 'neutral', // 'neutral', 'outline', 'ghost'
    },
    size: {
        type: String,
        default: 'md', // 'sm', 'md', 'lg'
    },
});
</script>

<template>
    <button
        :type="type"
        :class="[
            'inline-flex items-center justify-center font-semibold transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-75 disabled:cursor-not-allowed',
            // Variants
            variant === 'neutral' && 'bg-[var(--neutral-100)] dark:bg-[var(--neutral-800)] text-[var(--text-primary)] hover:bg-[var(--neutral-200)] dark:hover:bg-[var(--neutral-700)] focus:ring-[var(--neutral-500)]',
            variant === 'outline' && 'bg-transparent border border-[var(--border-primary)] text-[var(--text-primary)] hover:bg-[var(--neutral-100)] dark:hover:bg-[var(--neutral-800)] focus:ring-[var(--neutral-500)]',
            variant === 'ghost' && 'bg-transparent text-[var(--text-primary)] hover:bg-[var(--neutral-100)] dark:hover:bg-[var(--neutral-800)] focus:ring-[var(--neutral-500)]',
            // Sizes
            size === 'sm' && 'px-3 py-1.5 text-sm rounded-lg',
            size === 'md' && 'px-4 py-2 text-sm rounded-lg',
            size === 'lg' && 'px-6 py-3 text-base rounded-xl',
        ]"
    >
        <slot />
    </button>
</template>

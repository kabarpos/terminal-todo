<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
    variant: {
        type: String,
        default: 'primary', // 'primary', 'secondary', 'danger', 'neutral', 'outline', 'ghost'
    },
    size: {
        type: String,
        default: 'md', // 'sm', 'md', 'lg'
    },
    rounded: {
        type: String,
        default: 'full', // 'full', 'lg'
    },
});
</script>

<template>
    <button
        :type="type"
        :class="[
            'inline-flex items-center justify-center font-semibold transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-75 disabled:cursor-not-allowed',
            // Variants
            variant === 'primary' && 'bg-gradient-to-r from-[var(--primary-600)] to-[var(--accent-600)] hover:from-[var(--primary-700)] hover:to-[var(--accent-700)] text-white focus:ring-[var(--primary-500)] dark:focus:ring-offset-[var(--neutral-800)]',
            variant === 'secondary' && 'bg-[var(--card-gradient)] border border-[var(--border-primary)] text-[var(--text-primary)] hover:border-[var(--primary-400)] focus:ring-[var(--neutral-500)]',
            variant === 'danger' && 'bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white focus:ring-red-500 dark:focus:ring-offset-[var(--neutral-800)]',
            variant === 'neutral' && 'bg-[var(--neutral-100)] dark:bg-[var(--neutral-800)] text-[var(--text-primary)] hover:bg-[var(--neutral-200)] dark:hover:bg-[var(--neutral-700)] focus:ring-[var(--neutral-500)]',
            variant === 'outline' && 'bg-transparent border border-[var(--border-primary)] text-[var(--text-primary)] hover:bg-[var(--neutral-100)] dark:hover:bg-[var(--neutral-800)] focus:ring-[var(--neutral-500)]',
            variant === 'ghost' && 'bg-transparent text-[var(--text-primary)] hover:bg-[var(--neutral-100)] dark:hover:bg-[var(--neutral-800)] focus:ring-[var(--neutral-500)]',
            // Sizes
            size === 'sm' && 'p-1.5 text-sm',
            size === 'md' && 'p-2 text-base',
            size === 'lg' && 'p-3 text-lg',
            // Rounded
            rounded === 'full' && 'rounded-full',
            rounded === 'lg' && 'rounded-lg',
        ]"
    >
        <slot />
    </button>
</template> 
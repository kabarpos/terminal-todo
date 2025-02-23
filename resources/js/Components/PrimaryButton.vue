<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
    variant: {
        type: String,
        default: 'primary', // 'primary', 'secondary', 'danger'
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
            'inline-flex items-center justify-center font-semibold transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-75 disabled:cursor-not-allowed shadow-sm hover:shadow-md',
            // Variants
            variant === 'primary' && 'bg-gradient-to-r from-[var(--primary-600)] to-[var(--accent-600)] hover:from-[var(--primary-700)] hover:to-[var(--accent-700)] text-white focus:ring-[var(--primary-500)] dark:focus:ring-offset-[var(--neutral-800)]',
            variant === 'secondary' && 'bg-[var(--card-gradient)] border border-[var(--border-primary)] text-[var(--text-primary)] hover:border-[var(--primary-400)] focus:ring-[var(--neutral-500)]',
            variant === 'danger' && 'bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white focus:ring-red-500 dark:focus:ring-offset-[var(--neutral-800)]',
            // Sizes
            size === 'sm' && 'px-3 py-1.5 text-sm rounded-lg',
            size === 'md' && 'px-4 py-2 text-sm rounded-lg',
            size === 'lg' && 'px-6 py-3 text-base rounded-xl',
        ]"
    >
        <slot />
    </button>
</template>

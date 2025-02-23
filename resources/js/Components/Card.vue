<script setup>
defineProps({
    variant: {
        type: String,
        default: 'default', // 'default', 'primary', 'success', 'warning', 'danger'
    },
    hover: {
        type: Boolean,
        default: false,
    },
    as: {
        type: String,
        default: 'div',
    },
});
</script>

<template>
    <div 
        :class="[
            'theme-transition backdrop-blur-xl border shadow-lg rounded-lg',
            // Base styles
            'bg-light-card dark:bg-dark-card',
            'border-light-border dark:border-dark-border',
            'text-light-text dark:text-dark-text',
            // Variants
            variant === 'default' && [
                'border-light-border dark:border-dark-border',
                hover && 'hover:border-primary-400 dark:hover:border-primary-400 hover:shadow-md hover:scale-[1.02]'
            ],
            variant === 'primary' && [
                'gradient-primary text-white',
                'border-transparent',
                hover && 'hover:shadow-lg hover:scale-[1.02] gradient-primary-hover'
            ],
            variant === 'success' && [
                'bg-gradient-to-br from-success-500 to-success-600 text-white',
                'border-transparent',
                hover && 'hover:shadow-lg hover:scale-[1.02] hover:from-success-600 hover:to-success-700'
            ],
            variant === 'warning' && [
                'bg-gradient-to-br from-warning-500 to-warning-600 text-white',
                'border-transparent',
                hover && 'hover:shadow-lg hover:scale-[1.02] hover:from-warning-600 hover:to-warning-700'
            ],
            variant === 'danger' && [
                'bg-gradient-to-br from-error-500 to-error-600 text-white',
                'border-transparent',
                hover && 'hover:shadow-lg hover:scale-[1.02] hover:from-error-600 hover:to-error-700'
            ],
            // Hover Effect
            hover && 'cursor-pointer'
        ]"
    >
        <div class="p-6">
            <slot />
        </div>
    </div>
</template>

<style scoped>
.backdrop-blur-xl {
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
}

.theme-transition {
    transition: all 0.3s ease;
}
</style> 
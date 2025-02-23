<script setup>
import { XMarkIcon } from '@heroicons/vue/24/outline';
import { ref } from 'vue';

const props = defineProps({
    variant: {
        type: String,
        default: 'default', // 'default', 'primary', 'success', 'warning', 'danger'
    },
    dismissible: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: '',
    },
});

const isVisible = ref(true);

const dismiss = () => {
    isVisible.value = false;
};
</script>

<template>
    <div v-if="isVisible"
        :class="[
            'rounded-lg p-4 mb-4 flex items-start gap-3 transition-all duration-200',
            // Variants
            variant === 'default' && 'bg-neutral-50 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300',
            variant === 'primary' && 'bg-primary-50 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300',
            variant === 'success' && 'bg-success-50 text-success-700 dark:bg-success-900/30 dark:text-success-300',
            variant === 'warning' && 'bg-warning-50 text-warning-700 dark:bg-warning-900/30 dark:text-warning-300',
            variant === 'danger' && 'bg-error-50 text-error-700 dark:bg-error-900/30 dark:text-error-300',
        ]"
    >
        <!-- Icon Slot -->
        <div class="flex-shrink-0 h-5 w-5">
            <slot name="icon" />
        </div>

        <!-- Content -->
        <div class="flex-1">
            <h3 v-if="title" class="text-sm font-medium mb-1">{{ title }}</h3>
            <div class="text-sm">
                <slot />
            </div>
        </div>

        <!-- Dismiss Button -->
        <button 
            v-if="dismissible"
            @click="dismiss"
            class="flex-shrink-0 ml-auto -mx-1.5 -my-1.5 p-1.5 rounded-lg opacity-50 hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-offset-2 transition-opacity"
            :class="[
                variant === 'default' && 'hover:bg-neutral-200 dark:hover:bg-neutral-700 focus:ring-neutral-500',
                variant === 'primary' && 'hover:bg-primary-200 dark:hover:bg-primary-800 focus:ring-primary-500',
                variant === 'success' && 'hover:bg-success-200 dark:hover:bg-success-800 focus:ring-success-500',
                variant === 'warning' && 'hover:bg-warning-200 dark:hover:bg-warning-800 focus:ring-warning-500',
                variant === 'danger' && 'hover:bg-error-200 dark:hover:bg-error-800 focus:ring-error-500',
            ]"
        >
            <span class="sr-only">Dismiss</span>
            <XMarkIcon class="h-5 w-5" />
        </button>
    </div>
</template> 
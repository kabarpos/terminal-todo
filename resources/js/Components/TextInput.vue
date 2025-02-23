<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: {
        type: [String, Number],
        required: true,
    },
    type: {
        type: String,
        default: 'text',
    },
    size: {
        type: String,
        default: 'md', // 'sm', 'md', 'lg'
    },
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        ref="input"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :type="type"
        :class="[
            'w-full theme-transition',
            'border-light-border dark:border-dark-border',
            'bg-light-bg dark:bg-dark-bg',
            'text-light-text dark:text-dark-text',
            'placeholder-neutral-400 dark:placeholder-neutral-500',
            'focus:border-primary-500 focus:ring-primary-500 dark:focus:border-primary-500 dark:focus:ring-primary-500',
            // Sizes
            size === 'sm' && 'px-2.5 py-1.5 text-sm rounded-lg',
            size === 'md' && 'px-3 py-2 text-base rounded-lg',
            size === 'lg' && 'px-4 py-2.5 text-lg rounded-xl',
        ]"
    />
</template>

<style scoped>
.theme-transition {
    transition: all 0.3s ease;
}
</style>

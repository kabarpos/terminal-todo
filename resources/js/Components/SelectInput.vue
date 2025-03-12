<template>
    <select
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.value)"
        class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-400 focus:ring-primary-500 dark:focus:ring-primary-400 rounded-lg shadow-sm w-full"
    >
        <slot></slot>
    </select>
</template>

<script setup>
defineProps({
    modelValue: {
        type: [String, Number],
        required: true,
    },
});

defineEmits(['update:modelValue']);
</script> 
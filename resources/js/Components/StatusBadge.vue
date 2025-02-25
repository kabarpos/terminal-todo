<template>
    <span
        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
        :class="statusClasses"
    >
        <span class="w-2 h-2 mr-1.5 rounded-full" :class="dotClasses"></span>
        {{ statusLabel }}
    </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    status: {
        type: String,
        required: true
    }
});

const statusClasses = computed(() => {
    const classes = {
        'planned': 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200',
        'in_progress': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        'published': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'cancelled': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
    };
    return classes[props.status] || classes['planned'];
});

const dotClasses = computed(() => {
    const classes = {
        'planned': 'bg-gray-400',
        'in_progress': 'bg-blue-400',
        'published': 'bg-green-400',
        'cancelled': 'bg-red-400'
    };
    return classes[props.status] || classes['planned'];
});

const statusLabel = computed(() => {
    const labels = {
        'planned': 'Direncanakan',
        'in_progress': 'Dalam Proses',
        'published': 'Dipublikasi',
        'cancelled': 'Dibatalkan'
    };
    return labels[props.status] || 'Unknown';
});
</script> 
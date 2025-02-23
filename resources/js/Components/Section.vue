<script setup>
defineProps({
    title: {
        type: String,
        default: '',
    },
    description: {
        type: String,
        default: '',
    },
    noPadding: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <div class="mb-8">
        <!-- Section Header -->
        <div v-if="title || description" class="mb-6">
            <h2 v-if="title" class="text-2xl font-bold bg-gradient-to-r from-[var(--primary-600)] to-[var(--accent-600)] bg-clip-text text-transparent">
                {{ title }}
            </h2>
            <p v-if="description" class="mt-1 text-sm text-[var(--text-secondary)]">
                {{ description }}
            </p>
        </div>

        <!-- Section Content -->
        <div :class="[
            'bg-[var(--card-gradient)] backdrop-blur-lg rounded-xl border border-[var(--border-primary)] shadow-sm transition-all duration-300 hover:shadow-md',
            { 'p-6': !noPadding }
        ]">
            <slot />
        </div>

        <!-- Section Footer -->
        <div v-if="$slots.footer" class="mt-6">
            <slot name="footer" />
        </div>
    </div>
</template> 
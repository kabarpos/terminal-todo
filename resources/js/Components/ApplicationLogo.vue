<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    class: String
});

// Ambil logo dari settings
const settings = computed(() => usePage().props.settings || {});

const logoSrc = computed(() => {
    if (settings.value?.site_logo) {
        return `/storage/${settings.value.site_logo}`;
    }
    return '/images/logo-default.svg';
});

const logoAlt = computed(() => settings.value?.site_title || 'Application Logo');
</script>

<template>
    <div :class="class">
        <img
            :src="logoSrc"
            :alt="logoAlt"
            class="h-full w-full object-contain"
            @error="$event.target.src = '/images/logo-default.svg'"
        />
    </div>
</template>

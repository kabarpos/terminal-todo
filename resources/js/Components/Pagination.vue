<template>
    <div v-if="links.length > 3" class="flex flex-wrap -mb-1">
        <template v-for="(link, key) in links" :key="key">
            <div
                v-if="link.url === null"
                class="mr-1 mb-1 px-4 py-2 text-sm text-gray-500 dark:text-gray-400"
                :class="{ 'ml-auto': link.label === 'Next' }"
                v-html="link.label"
            />
            <Link
                v-else
                class="mr-1 mb-1 px-4 py-2 text-sm rounded-md focus:text-primary-500 focus:outline-none"
                :class="{
                    'ml-auto': link.label === 'Next',
                    'bg-primary-50 dark:bg-primary-500/10 text-primary-500': link.active,
                    'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800': !link.active
                }"
                :href="link.url"
                v-html="link.label"
            />
        </template>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    links: {
        type: Array,
        required: true
    }
})
</script> 
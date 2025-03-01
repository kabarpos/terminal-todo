<template>
    <div v-if="links.length > 3">
        <div class="flex flex-wrap -mb-1">
            <template v-for="(link, key) in links" :key="key">
                <div v-if="link.url === null" 
                    class="mr-1 mb-1 px-4 py-2 text-sm text-gray-400 border rounded"
                    :class="{ 'ml-auto': link.label === 'Next' }"
                    v-html="link.label" 
                />
                <Link v-else
                    class="mr-1 mb-1 px-4 py-2 text-sm border rounded hover:bg-primary-50 dark:hover:bg-primary-500/10 focus:outline-none transition-colors"
                    :class="{
                        'bg-primary-50 dark:bg-primary-500/10 text-primary-500': link.active,
                        'text-gray-700 dark:text-gray-300': !link.active,
                        'ml-auto': link.label === 'Next'
                    }"
                    :href="link.url"
                    v-html="link.label" 
                />
            </template>
        </div>
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
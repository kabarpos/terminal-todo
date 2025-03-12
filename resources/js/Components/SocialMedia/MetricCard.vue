<template>
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div 
                        v-if="icon" 
                        class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center"
                        :class="iconColorClass"
                    >
                        <component :is="icon" class="w-6 h-6 text-white" />
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ title }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ subtitle }}
                        </p>
                    </div>
                </div>
                <slot name="action" />
            </div>

            <div class="mt-6">
                <div class="flex items-baseline">
                    <p class="text-3xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ formattedValue }}
                    </p>
                    <p 
                        v-if="trend" 
                        class="ml-2 flex items-baseline text-sm font-semibold"
                        :class="trendColorClass"
                    >
                        <component 
                            :is="trendIcon" 
                            class="w-4 h-4 mr-1" 
                            :class="trend > 0 ? 'rotate-0' : 'rotate-180'"
                        />
                        {{ Math.abs(trend) }}%
                    </p>
                </div>
                <div class="mt-1">
                    <slot name="chart" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { 
    ChartBarIcon,
    ArrowTrendingUpIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    subtitle: {
        type: String,
        default: ''
    },
    value: {
        type: [Number, String],
        required: true
    },
    trend: {
        type: Number,
        default: null
    },
    unit: {
        type: String,
        default: 'number'
    },
    icon: {
        type: [String, Object],
        default: () => ChartBarIcon
    },
    color: {
        type: String,
        default: 'blue'
    }
})

// Cached color mapping untuk menghindari rekomputasi
const colorMap = {
    blue: 'bg-blue-600',
    red: 'bg-red-600',
    green: 'bg-green-600',
    yellow: 'bg-yellow-600',
    purple: 'bg-purple-600',
}

const iconColorClass = computed(() => colorMap[props.color] || colorMap.blue)

const trendColorClass = computed(() => {
    if (!props.trend) return ''
    return props.trend > 0 
        ? 'text-green-600 dark:text-green-500'
        : 'text-red-600 dark:text-red-500'
})

const trendIcon = computed(() => ArrowTrendingUpIcon)

// Cached number formatter untuk menghindari pembuatan instance baru setiap kali
const currencyFormatter = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0
})

const numberFormatter = new Intl.NumberFormat('id-ID')

const formattedValue = computed(() => {
    if (props.unit === 'percentage') return `${props.value}%`
    if (props.unit === 'currency') return currencyFormatter.format(props.value)
    return numberFormatter.format(props.value)
})
</script> 
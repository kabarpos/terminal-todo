<template>
    <Head title="Content Calendar" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Content Calendar
                </h2>
                <Link
                    :href="route('calendar.create')"
                    class="inline-flex items-center px-3 py-2 md:px-4 md:py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
                >
                    <span class="flex items-center">
                        <PlusIcon class="h-5 w-5" />
                        <span class="hidden md:inline ml-2">Tambah Event</span>
                    </span>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <!-- Calendar View -->
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-2">
                                    <button
                                        @click="previousMonth"
                                        class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
                                    >
                                        <ChevronLeftIcon class="h-5 w-5" />
                                    </button>
                                    <h3 class="text-lg font-medium">
                                        {{ currentMonthName }} {{ currentYear }}
                                    </h3>
                                    <button
                                        @click="nextMonth"
                                        class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
                                    >
                                        <ChevronRightIcon class="h-5 w-5" />
                                    </button>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button
                                        @click="today"
                                        class="px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
                                    >
                                        Hari Ini
                                    </button>
                                </div>
                            </div>

                            <!-- Calendar Grid -->
                            <div class="grid grid-cols-7 gap-px bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden">
                                <!-- Day Headers -->
                                <div
                                    v-for="day in dayNames"
                                    :key="day"
                                    class="bg-gray-50 dark:bg-gray-800 p-2 text-center text-sm font-medium text-gray-500 dark:text-gray-400"
                                >
                                    {{ day }}
                                </div>

                                <!-- Calendar Days -->
                                <div
                                    v-for="(day, index) in calendarDays"
                                    :key="index"
                                    class="min-h-[120px] bg-white dark:bg-gray-800 p-2 relative group"
                                    :class="{
                                        'opacity-50': !day.isCurrentMonth,
                                        'bg-blue-50 dark:bg-blue-900/20': day.isToday
                                    }"
                                    @click="day.isCurrentMonth && createEventOnDate(day.date)"
                                >
                                    <div class="flex items-center justify-between mb-1">
                                        <span
                                            class="text-sm"
                                            :class="{
                                                'font-bold text-blue-600 dark:text-blue-400': day.isToday
                                            }"
                                        >
                                            {{ day.date.getDate() }}
                                        </span>
                                    </div>

                                    <!-- Events for this day -->
                                    <div class="space-y-1">
                                        <Link
                                            v-for="event in getEventsForDay(day.date)"
                                            :key="event.id"
                                            :href="route('calendar.show', event.id)"
                                            class="block p-1 text-xs rounded-md truncate"
                                            :style="{ backgroundColor: event.category.color + '20' }"
                                            @click.stop
                                        >
                                            <div class="flex items-center">
                                                <i :class="['mr-1 fa-brands', `fa-${event.platform.icon}`]"></i>
                                                {{ event.title }}
                                            </div>
                                        </Link>
                                    </div>
                                    
                                    <!-- Add event button (visible on hover) -->
                                    <div 
                                        v-if="day.isCurrentMonth" 
                                        class="absolute bottom-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity"
                                        @click.stop="createEventOnDate(day.date)"
                                    >
                                        <button class="p-1 bg-blue-100 dark:bg-blue-800 rounded-full hover:bg-blue-200 dark:hover:bg-blue-700">
                                            <PlusIcon class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Event List -->
                        <div class="mt-8">
                            <h3 class="text-lg font-medium mb-4">Upcoming Events</h3>
                            <div class="space-y-4">
                                <Link
                                    v-for="event in upcomingEvents"
                                    :key="event.id"
                                    :href="route('calendar.show', event.id)"
                                    class="block bg-white dark:bg-gray-700 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow"
                                >
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div
                                                class="w-2 h-2 rounded-full"
                                                :style="{ backgroundColor: event.category.color }"
                                            ></div>
                                            <span class="text-sm font-medium hover:text-blue-600 dark:hover:text-blue-400">
                                                {{ event.title }}
                                            </span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <i :class="['text-lg fa-brands', `fa-${event.platform.icon}`]"></i>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ formatDate(event.publish_date) }}
                                            </span>
                                        </div>
                                    </div>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    PlusIcon,
    ChevronLeftIcon,
    ChevronRightIcon
} from '@heroicons/vue/24/outline';
import { format, addMonths, subMonths, startOfMonth, endOfMonth, eachDayOfInterval, isSameMonth, isToday, parseISO, isAfter, isBefore, addDays } from 'date-fns';
import { id } from 'date-fns/locale';

const props = defineProps({
    events: {
        type: Array,
        required: true
    }
});

const currentDate = ref(new Date());

const dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

const currentMonthName = computed(() => {
    return format(currentDate.value, 'MMMM', { locale: id });
});

const currentYear = computed(() => {
    return format(currentDate.value, 'yyyy');
});

const calendarDays = computed(() => {
    const start = startOfMonth(currentDate.value);
    const end = endOfMonth(currentDate.value);
    
    // Get all days in the month
    const days = eachDayOfInterval({ start, end });
    
    // Add padding days at the start
    const firstDayOfWeek = start.getDay();
    const paddingStart = Array(firstDayOfWeek).fill(null).map((_, i) => {
        return {
            date: subMonths(start, 1),
            isCurrentMonth: false,
            isToday: false
        };
    });
    
    // Add padding days at the end
    const lastDayOfWeek = end.getDay();
    const paddingEnd = Array(6 - lastDayOfWeek).fill(null).map((_, i) => {
        return {
            date: addMonths(end, 1),
            isCurrentMonth: false,
            isToday: false
        };
    });
    
    // Combine all days
    return [
        ...paddingStart,
        ...days.map(date => ({
            date,
            isCurrentMonth: isSameMonth(date, currentDate.value),
            isToday: isToday(date)
        })),
        ...paddingEnd
    ];
});

const upcomingEvents = computed(() => {
    return props.events
        .filter(event => isAfter(parseISO(event.publish_date), new Date()))
        .sort((a, b) => new Date(a.publish_date) - new Date(b.publish_date))
        .slice(0, 5);
});

const getEventsForDay = (date) => {
    return props.events.filter(event => {
        const eventDate = parseISO(event.publish_date);
        return (
            eventDate.getDate() === date.getDate() &&
            eventDate.getMonth() === date.getMonth() &&
            eventDate.getFullYear() === date.getFullYear()
        );
    });
};

const formatDate = (date) => {
    if (!date) return '-';
    try {
        return format(parseISO(date), 'dd MMM yyyy', { locale: id });
    } catch (error) {
        return '-';
    }
};

const previousMonth = () => {
    currentDate.value = subMonths(currentDate.value, 1);
};

const nextMonth = () => {
    currentDate.value = addMonths(currentDate.value, 1);
};

const today = () => {
    currentDate.value = new Date();
};

const createEventOnDate = (date) => {
    // Format the date as required by the create form
    const formattedDate = format(date, "yyyy-MM-dd'T'HH:mm", { locale: id });
    
    // Navigate to create page with the date pre-filled
    router.visit(route('calendar.create', { date: formattedDate }));
};
</script> 
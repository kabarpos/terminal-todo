<template>
    <Head title="Calendar" />

    <AuthenticatedLayout :auth="auth" title="Tambah Event">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Tambah Event
                </h2>
                <Link
                    :href="route('calendar.index')"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800"
                >
                    <ArrowLeftIcon class="w-5 h-5 mr-2" />
                    Kembali
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <EventForm
                                :form="form"
                                :platforms="platforms"
                                :categories="categories"
                                :teams="teams"
                                :users="users"
                            />

                            <div class="flex items-center justify-end gap-4">
                                <Link
                                    :href="route('calendar.index')"
                                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                >
                                    Batal
                                </Link>

                                <PrimaryButton
                                    type="submit"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium rounded-lg transition-colors duration-200"
                                >
                                    {{ form.processing ? 'Menyimpan...' : 'Simpan Event' }}
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import { watch, onMounted, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import EventForm from '@/Components/EventForm.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    platforms: {
        type: Array,
        required: true
    },
    categories: {
        type: Array,
        required: true
    },
    teams: {
        type: Array,
        required: true
    },
    users: {
        type: Array,
        required: true
    },
    prefilledDate: {
        type: String,
        default: ''
    },
    auth: {
        type: Object,
        required: true
    }
});

console.log('Props received:', props);
console.log('Prefilled Date from props:', props.prefilledDate);

// Format prefilledDate to include time if it's just a date
const getInitialDate = (date) => {
    console.log('getInitialDate called with:', date);
    if (!date) {
        console.log('No date provided');
        return '';
    }
    // If prefilledDate is just a date (YYYY-MM-DD), add time
    if (date.length === 10) {
        const formattedDate = `${date}T00:00`;
        console.log('Formatted date:', formattedDate);
        return formattedDate;
    }
    console.log('Date already formatted:', date);
    return date;
};

const initialFormDate = getInitialDate(props.prefilledDate);
console.log('Initial form date:', initialFormDate);

// Initialize form with formatted date
const form = useForm({
    title: '',
    description: '',
    publish_date: initialFormDate,
    deadline: '',
    platform_id: '',
    category_id: '',
    team_id: '',
    status: 'planned',
    assignees: []
});

console.log('Form initialized with publish_date:', form.publish_date);

onMounted(() => {
    console.log('Component mounted');
    console.log('Current form publish_date:', form.publish_date);
    console.log('Current props.prefilledDate:', props.prefilledDate);
    
    if (props.prefilledDate && !form.publish_date) {
        const formattedDate = getInitialDate(props.prefilledDate);
        console.log('Setting publish_date on mount:', formattedDate);
        form.publish_date = formattedDate;
    }
});

// Watch for changes in prefilledDate prop
watch(() => props.prefilledDate, (newDate, oldDate) => {
    console.log('Watch triggered');
    console.log('Old date:', oldDate);
    console.log('New date:', newDate);
    
    if (newDate) {
        const formattedDate = getInitialDate(newDate);
        console.log('Setting new publish_date:', formattedDate);
        form.publish_date = formattedDate;
    }
}, { immediate: true });

const submit = () => {
    form.post(route('calendar.store'), {
        onSuccess: () => {
            router.visit(route('calendar.index'));
        }
    });
};
</script> 
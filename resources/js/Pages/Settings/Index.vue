<script setup>
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    settings: {
        type: Object,
        required: true
    },
    auth: {
        type: Object,
        required: true
    }
});

const form = ref({});
const processing = ref(false);

// Computed untuk mendapatkan current settings dari form
const currentSettings = computed(() => {
    const general = props.settings.general || [];
    const settingValues = {};
    general.forEach(setting => {
        settingValues[setting.key] = form.value[setting.key];
    });
    return settingValues;
});

// Inisialisasi form dengan nilai dari settings
Object.entries(props.settings).forEach(([group, groupSettings]) => {
    groupSettings.forEach(setting => {
        form.value[setting.key] = setting.value;
    });
});

const submit = () => {
    processing.value = true;
    const formData = new FormData();
    
    Object.entries(form.value).forEach(([key, value]) => {
        if (value instanceof File) {
            formData.append(key, value);
        } 
        else if (value !== null && value !== undefined) {
            formData.append(key, value.toString());
        }
    });

    router.post(route('admin.settings.update'), formData, {
        preserveScroll: true,
        onSuccess: () => {
            processing.value = false;
            window.location.reload();
        },
        onError: () => {
            processing.value = false;
        }
    });
};

const handleFileChange = (key, event) => {
    const file = event.target.files[0];
    if (file) {
        form.value[key] = file;
    }
};

const getAssetUrl = (path) => {
    if (!path) return null;
    return `/storage/${path}`;
};
</script>

<template>
    <Head title="Pengaturan Website" />

    <AuthenticatedLayout 
        :auth="auth" 
        title="Pengaturan Website"
        :settings="currentSettings"
    >
        <template #header>
            <div class="w-full">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg md:text-xl font-semibold text-[var(--text-primary)] truncate">
                        Pengaturan Website
                    </h2>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <form @submit.prevent="submit" enctype="multipart/form-data">
                        <div v-for="(groupSettings, group) in settings" :key="group" class="mb-8">
                            <h3 class="text-lg font-medium mb-4 capitalize text-gray-900 dark:text-gray-100">
                                {{ group }}
                            </h3>
                            
                            <div v-for="setting in groupSettings" :key="setting.key" class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ setting.label }}
                                </label>

                                <!-- Text Input -->
                                <input 
                                    v-if="setting.type === 'text'"
                                    v-model="form[setting.key]"
                                    type="text"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                <!-- Textarea -->
                                <textarea 
                                    v-else-if="setting.type === 'textarea'"
                                    v-model="form[setting.key]"
                                    rows="4"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                ></textarea>

                                <!-- File Input -->
                                <div v-else-if="setting.type === 'file'">
                                    <div v-if="setting.value" class="mb-2">
                                        <img 
                                            v-if="['jpg', 'jpeg', 'png', 'gif'].includes(setting.value.split('.').pop().toLowerCase())"
                                            :src="getAssetUrl(setting.value)"
                                            :alt="setting.label"
                                            class="h-20 w-auto object-cover rounded-lg"
                                        >
                                        <a 
                                            v-else
                                            :href="getAssetUrl(setting.value)"
                                            target="_blank"
                                            class="text-indigo-600 hover:text-indigo-500"
                                        >
                                            View File
                                        </a>
                                    </div>
                                    <input 
                                        type="file"
                                        :accept="setting.type === 'file' && ['jpg', 'jpeg', 'png', 'gif'].includes(setting.value?.split('.').pop().toLowerCase()) ? 'image/*' : ''"
                                        @change="e => handleFileChange(setting.key, e)"
                                        class="w-full rounded-md border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm"
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <button
                                type="submit"
                                :disabled="processing"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {{ processing ? 'Saving...' : 'Save Settings' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template> 
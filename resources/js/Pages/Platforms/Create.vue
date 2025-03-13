<template>
    <Head title="Platform" />

    <AuthenticatedLayout :auth="auth" title="Tambah Platform">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Tambah Platform
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="name" value="Nama Platform" />
                                <TextInput
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                    autofocus
                                />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="icon" value="Icon" />
                                <TextInput
                                    id="icon"
                                    v-model="form.icon"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                    placeholder="instagram"
                                />
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Masukkan nama icon dari Font Awesome tanpa awalan "fa-". Contoh: instagram, facebook, twitter, dll.
                                </p>
                                <InputError :message="form.errors.icon" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="description" value="Deskripsi" />
                                <TextArea
                                    id="description"
                                    v-model="form.description"
                                    class="mt-1 block w-full"
                                    rows="3"
                                />
                                <InputError :message="form.errors.description" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="settings" value="Pengaturan Platform" />
                                <div class="mb-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Pengaturan platform berisi konfigurasi khusus untuk setiap platform media sosial, seperti:
                                    </p>
                                    <ul class="mt-2 ml-4 list-disc text-sm text-gray-500 dark:text-gray-400">
                                        <li>Jenis postingan yang didukung (feed, story, reels, dll)</li>
                                        <li>Ukuran gambar yang direkomendasikan</li>
                                        <li>Batasan karakter</li>
                                        <li>Dan pengaturan khusus lainnya</li>
                                    </ul>
                                </div>
                                <TextArea
                                    id="settings"
                                    v-model="form.settings"
                                    class="mt-1 block w-full font-mono"
                                    rows="8"
                                    placeholder='{
  "post_types": ["feed", "story", "reels"],
  "image_dimensions": {
    "feed": "1080x1080",
    "story": "1080x1920",
    "reels": "1080x1920"
  },
  "character_limits": {
    "caption": 2200,
    "hashtags": 30
  }
}'
                                />
                                <div class="mt-2 space-y-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Contoh pengaturan untuk beberapa platform:
                                    </p>
                                    <div class="space-y-2 text-sm text-gray-500 dark:text-gray-400">
                                        <details class="cursor-pointer">
                                            <summary class="font-medium">Instagram</summary>
                                            <pre class="mt-2 p-2 bg-gray-100 dark:bg-gray-700 rounded-lg overflow-x-auto">{
  "post_types": ["feed", "story", "reels"],
  "image_dimensions": {
    "feed": "1080x1080",
    "story": "1080x1920",
    "reels": "1080x1920"
  },
  "character_limits": {
    "caption": 2200,
    "hashtags": 30
  }
}</pre>
                                        </details>
                                        <details class="cursor-pointer">
                                            <summary class="font-medium">Facebook</summary>
                                            <pre class="mt-2 p-2 bg-gray-100 dark:bg-gray-700 rounded-lg overflow-x-auto">{
  "post_types": ["feed", "story", "reels"],
  "image_dimensions": {
    "feed": "1200x630",
    "story": "1080x1920",
    "reels": "1080x1920"
  },
  "character_limits": {
    "text": 63206
  }
}</pre>
                                        </details>
                                        <details class="cursor-pointer">
                                            <summary class="font-medium">Twitter/X</summary>
                                            <pre class="mt-2 p-2 bg-gray-100 dark:bg-gray-700 rounded-lg overflow-x-auto">{
  "post_types": ["tweet"],
  "image_dimensions": {
    "tweet": "1600x900"
  },
  "character_limits": {
    "tweet": 280
  }
}</pre>
                                        </details>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Klik salah satu contoh di atas untuk menyalin dan menyesuaikan dengan kebutuhan Anda.
                                    </p>
                                </div>
                                <InputError :message="form.errors.settings" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end gap-4">
                                <Link
                                    :href="route('platforms.index')"
                                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                >
                                    Batal
                                </Link>

                                <PrimaryButton
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium rounded-lg transition-colors duration-200"
                                >
                                    {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
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
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    auth: {
        type: Object,
        required: true
    }
});

const form = useForm({
    name: '',
    icon: '',
    description: '',
    settings: ''
});

const submit = () => {
    // Parse settings JSON if not empty
    if (form.settings) {
        try {
            form.settings = JSON.parse(form.settings);
        } catch (e) {
            form.setError('settings', 'Format JSON tidak valid');
            return;
        }
    }

    form.post(route('platforms.store'));
};
</script> 
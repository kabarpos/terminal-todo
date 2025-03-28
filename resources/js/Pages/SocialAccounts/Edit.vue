<template>
    <Head title="Edit Akun" />

    <AuthenticatedLayout :auth="auth" title="Edit Akun">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Edit Akun
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <div>
                            <InputLabel for="platform_id" value="Platform" />
                            <div class="mt-1 relative">
                                <div v-if="form.platform_id" class="absolute left-3 top-2">
                                    <i :class="[getPlatformIcon(getPlatformNameById(form.platform_id)?.toLowerCase()), 'text-xl']"></i>
                                </div>
                                <select
                                    id="platform_id"
                                    v-model="form.platform_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                    :class="{ 'pl-10': form.platform_id }"
                                    required
                                >
                                    <option value="">Pilih Platform</option>
                                    <option v-for="platform in platforms" :key="platform.id" :value="platform.id">
                                        {{ platform.name }}
                                    </option>
                                </select>
                            </div>
                            <InputError :message="form.errors.platform_id" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="name" value="Nama Akun/Halaman" />
                            <TextInput
                                id="name"
                                type="text"
                                v-model="form.name"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="form.errors.name" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="username" value="Username" />
                            <TextInput
                                id="username"
                                type="text"
                                v-model="form.username"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="form.errors.username" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="url" value="URL Profil" />
                            <TextInput
                                id="url"
                                type="url"
                                v-model="form.url"
                                class="mt-1 block w-full"
                                required
                                placeholder="https://instagram.com/username"
                            />
                            <InputError :message="form.errors.url" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="description" value="Deskripsi (Opsional)" />
                            <textarea
                                id="description"
                                v-model="form.description"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                rows="3"
                            ></textarea>
                            <InputError :message="form.errors.description" class="mt-2" />
                        </div>

                        <div class="flex items-center">
                            <input
                                id="is_active"
                                type="checkbox"
                                v-model="form.is_active"
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                            >
                            <InputLabel for="is_active" value="Akun Aktif" class="ml-2" />
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <Link
                                :href="route('social-accounts.index')"
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
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { ref, computed } from 'vue'

const props = defineProps({
    account: {
        type: Object,
        required: true
    },
    platforms: {
        type: Array,
        required: true
    }
})

const form = useForm({
    platform_id: props.account.platform_id,
    name: props.account.name,
    username: props.account.username,
    url: props.account.url,
    description: props.account.description,
    is_active: props.account.is_active
})

const submit = () => {
    form.put(route('social-accounts.update', props.account.id))
}

// Fungsi untuk mendapatkan nama platform dari ID
const getPlatformNameById = (id) => {
    if (!id) return '';
    const platform = props.platforms.find(p => p.id === id);
    return platform ? platform.name : '';
}

// Fungsi untuk mendapatkan ikon platform
const getPlatformIcon = (platform) => {
    const icons = {
        instagram: 'fa-brands fa-instagram',
        facebook: 'fa-brands fa-facebook-f',
        twitter: 'fa-brands fa-twitter',
        tiktok: 'fa-brands fa-tiktok',
        youtube: 'fa-brands fa-youtube',
        linkedin: 'fa-brands fa-linkedin-in',
        pinterest: 'fa-brands fa-pinterest-p',
        // Tambahkan platform lain sesuai kebutuhan
        default: 'fa-brands fa-globe' // Icon default jika platform tidak ditemukan
    }
    return icons[platform] || icons.default
}

// Fungsi untuk mendapatkan class warna platform
const getPlatformClass = (platform) => {
    const classes = {
        instagram: 'bg-gradient-to-r from-purple-500 to-pink-500 text-white',
        facebook: 'bg-blue-600 text-white',
        twitter: 'bg-blue-400 text-white',
        tiktok: 'bg-black text-white',
        youtube: 'bg-red-600 text-white',
        linkedin: 'bg-blue-700 text-white',
        pinterest: 'bg-red-700 text-white',
        // Tambahkan platform lain sesuai kebutuhan
        default: 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400'
    }
    return classes[platform] || classes.default
}
</script> 
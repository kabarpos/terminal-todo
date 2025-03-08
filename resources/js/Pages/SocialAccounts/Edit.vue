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
                            <select
                                id="platform_id"
                                v-model="form.platform_id"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                required
                            >
                                <option value="">Pilih Platform</option>
                                <option v-for="platform in platforms" :key="platform.id" :value="platform.id">
                                    {{ platform.name }}
                                </option>
                            </select>
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
</script> 
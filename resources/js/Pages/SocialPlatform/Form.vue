<template>
    <Head :title="platform ? 'Edit Platform' : 'Tambah Platform'" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ platform ? 'Edit Platform' : 'Tambah Platform Baru' }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <div>
                            <InputLabel for="name" value="Nama Platform" />
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
                            <InputLabel for="icon" value="URL Icon (Opsional)" />
                            <TextInput
                                id="icon"
                                type="url"
                                v-model="form.icon"
                                class="mt-1 block w-full"
                                placeholder="https://example.com/icon.png"
                            />
                            <InputError :message="form.errors.icon" class="mt-2" />
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
                            <InputLabel for="is_active" value="Platform Aktif" class="ml-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <Link
                                :href="route('social-platforms.index')"
                                class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100"
                            >
                                Batal
                            </Link>
                            <PrimaryButton class="ml-4" :disabled="form.processing">
                                {{ platform ? 'Simpan Perubahan' : 'Tambah Platform' }}
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
    platform: {
        type: Object,
        default: null
    }
})

const form = useForm({
    name: props.platform?.name || '',
    icon: props.platform?.icon || '',
    description: props.platform?.description || '',
    is_active: props.platform?.is_active ?? true
})

const submit = () => {
    if (props.platform) {
        form.put(route('social-platforms.update', props.platform.id))
    } else {
        form.post(route('social-platforms.store'))
    }
}
</script> 
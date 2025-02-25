<template>
    <form @submit.prevent="$emit('submit')" class="space-y-6">
        <div>
            <InputLabel for="title" value="Judul" />
            <TextInput
                id="title"
                type="text"
                class="mt-1 block w-full"
                v-model="form.title"
                required
                autofocus
            />
            <InputError class="mt-2" :message="form.errors.title" />
        </div>

        <div>
            <InputLabel for="description" value="Deskripsi" />
            <TextArea
                id="description"
                class="mt-1 block w-full"
                v-model="form.description"
                rows="4"
            />
            <InputError class="mt-2" :message="form.errors.description" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <InputLabel for="publish_date" value="Tanggal Publikasi" />
                <TextInput
                    id="publish_date"
                    type="datetime-local"
                    class="mt-1 block w-full"
                    v-model="form.publish_date"
                    required
                />
                <InputError class="mt-2" :message="form.errors.publish_date" />
            </div>

            <div>
                <InputLabel for="deadline" value="Deadline" />
                <TextInput
                    id="deadline"
                    type="datetime-local"
                    class="mt-1 block w-full"
                    v-model="form.deadline"
                />
                <InputError class="mt-2" :message="form.errors.deadline" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <InputLabel for="platform_id" value="Platform" />
                <SelectInput
                    id="platform_id"
                    class="mt-1 block w-full"
                    v-model="form.platform_id"
                    required
                >
                    <option value="">Pilih Platform</option>
                    <option v-for="platform in platforms" :key="platform.id" :value="platform.id">
                        {{ platform.name }}
                    </option>
                </SelectInput>
                <InputError class="mt-2" :message="form.errors.platform_id" />
            </div>

            <div>
                <InputLabel for="category_id" value="Kategori" />
                <SelectInput
                    id="category_id"
                    class="mt-1 block w-full"
                    v-model="form.category_id"
                    required
                >
                    <option value="">Pilih Kategori</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                    </option>
                </SelectInput>
                <InputError class="mt-2" :message="form.errors.category_id" />
            </div>
        </div>

        <div>
            <InputLabel for="status" value="Status" />
            <SelectInput
                id="status"
                class="mt-1 block w-full"
                v-model="form.status"
                required
            >
                <option value="">Pilih Status</option>
                <option value="planned">Direncanakan</option>
                <option value="in_progress">Dalam Proses</option>
                <option value="published">Dipublikasi</option>
                <option value="cancelled">Dibatalkan</option>
            </SelectInput>
            <InputError class="mt-2" :message="form.errors.status" />
        </div>

        <div>
            <InputLabel for="assignees" value="Penanggung Jawab" />
            <MultiSelect
                id="assignees"
                class="mt-1 block w-full"
                v-model="form.assignees"
                :options="users.map(u => ({
                    value: u.id,
                    label: u.name
                }))"
                multiple
            />
            <InputError class="mt-2" :message="form.errors.assignees" />
        </div>

        <div class="flex items-center justify-end space-x-3">
            <Link
                :href="route('calendar.index')"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800"
            >
                Batal
            </Link>
            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
            </PrimaryButton>
        </div>
    </form>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import SelectInput from '@/Components/SelectInput.vue';
import MultiSelect from '@/Components/MultiSelect.vue';

const props = defineProps({
    form: {
        type: Object,
        required: true
    },
    platforms: {
        type: Array,
        required: true
    },
    categories: {
        type: Array,
        required: true
    },
    users: {
        type: Array,
        required: true
    }
});

defineEmits(['submit']);
</script> 
<template>
    <Head title="Create Role" />

    <AuthenticatedLayout :auth="auth">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-lg md:text-xl font-semibold text-[var(--text-primary)] truncate">
                    Tambah Role Baru
                </h2>
                <Link
                    :href="route('admin.roles.create')"
                    class="inline-flex items-center"
                >
                    <PrimaryButton>
                        <span class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            <span class="ml-2">Tambah Role</span>
                        </span>
                    </PrimaryButton>
                </Link>
            </div>
        </template>

        <Card class="max-w-6xl mx-auto">
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <InputLabel for="name" value="Nama Role" />
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

                <!-- Permission Table -->
                <div class="mt-6">
                    <h3 class="text-lg font-medium text-[var(--text-primary)] mb-4">
                        Permissions
                    </h3>
                    
                    <!-- Search & Filter -->
                    <div class="mb-4">
                        <TextInput
                            v-model="searchQuery"
                            type="text"
                            placeholder="Cari permission..."
                            class="w-full md:w-64"
                        />
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-200/50 dark:border-gray-700/25">
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[var(--text-secondary)] uppercase tracking-wider">
                                        Module
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[var(--text-secondary)] uppercase tracking-wider">
                                        View
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[var(--text-secondary)] uppercase tracking-wider">
                                        Create
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[var(--text-secondary)] uppercase tracking-wider">
                                        Edit
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[var(--text-secondary)] uppercase tracking-wider">
                                        Delete
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Users Module -->
                                <tr v-show="showModule('users')" class="border-b border-gray-200/50 dark:border-gray-700/25 hover:bg-[var(--bg-secondary)]/50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-[var(--text-primary)]">
                                        Users
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <Checkbox
                                            v-model:checked="form.permissions"
                                            :value="'view users'"
                                            :name="'view users'"
                                        />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <Checkbox
                                            v-model:checked="form.permissions"
                                            :value="'create users'"
                                            :name="'create users'"
                                        />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <Checkbox
                                            v-model:checked="form.permissions"
                                            :value="'edit users'"
                                            :name="'edit users'"
                                        />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <Checkbox
                                            v-model:checked="form.permissions"
                                            :value="'delete users'"
                                            :name="'delete users'"
                                        />
                                    </td>
                                </tr>
                                <!-- Roles Module -->
                                <tr v-show="showModule('roles')" class="border-b border-gray-200/50 dark:border-gray-700/25 hover:bg-[var(--bg-secondary)]/50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-[var(--text-primary)]">
                                        Roles
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <Checkbox
                                            v-model:checked="form.permissions"
                                            :value="'view roles'"
                                            :name="'view roles'"
                                        />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <Checkbox
                                            v-model:checked="form.permissions"
                                            :value="'create roles'"
                                            :name="'create roles'"
                                        />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <Checkbox
                                            v-model:checked="form.permissions"
                                            :value="'edit roles'"
                                            :name="'edit roles'"
                                        />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <Checkbox
                                            v-model:checked="form.permissions"
                                            :value="'delete roles'"
                                            :name="'delete roles'"
                                        />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 mt-6">
                    <Link
                        :href="route('admin.roles.index')"
                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                    >
                        Batal
                    </Link>
                    <button
                        type="submit"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium rounded-lg transition-colors duration-200 disabled:opacity-25"
                    >
                        Simpan
                    </button>
                </div>
            </form>
        </Card>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from '@/Components/Card.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    auth: {
        type: Object,
        required: true
    }
});

const form = useForm({
    name: '',
    permissions: []
});

const searchQuery = ref('');

// Method untuk filter module berdasarkan search query
const showModule = (moduleName) => {
    if (!searchQuery.value) return true;
    return moduleName.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
           form.permissions.some(permission => 
               permission.toLowerCase().includes(searchQuery.value.toLowerCase()) &&
               permission.includes(moduleName)
           );
};

const submit = () => {
    form.post(route('admin.roles.store'));
};
</script> 
<template>
    <Head title="Edit Role" />

    <AuthenticatedLayout :auth="auth">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-[var(--text-primary)]">
                    Edit Role
                </h2>
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
                        class="px-4 py-2 text-sm font-medium text-[var(--text-primary)] bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                    >
                        Batal
                    </Link>
                    <PrimaryButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white transition-all duration-200"
                    >
                        Simpan Perubahan
                    </PrimaryButton>
                </div>
            </form>
        </Card>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from '@/Components/Card.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps({
    auth: {
        type: Object,
        required: true
    },
    role: {
        type: Object,
        required: true
    }
});

const form = useForm({
    name: props.role.name,
    permissions: props.role.permissions || []
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
    form.put(route('admin.roles.update', props.role.id));
};
</script> 
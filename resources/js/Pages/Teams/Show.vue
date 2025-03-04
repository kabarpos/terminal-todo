<template>
    <Head :title="`${team.name} - Detail Tim`" />

    <AuthenticatedLayout :title="`${team.name} - Detail Tim`">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Detail Tim
                </h2>
                <Link
                    :href="route('teams.edit', team.id)"
                    class="inline-flex items-center px-3 py-2 md:px-4 md:py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
                >
                    <PencilSquareIcon class="w-5 h-5 mr-2" />
                    Edit Tim
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center space-x-4">
                            <div :style="{ backgroundColor: team.color }" 
                                class="flex items-center justify-center w-16 h-16 rounded-full">
                                <span class="text-2xl font-bold text-white">
                                    {{ team.name.charAt(0).toUpperCase() }}
                                </span>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ team.name }}
                                </h1>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Dibuat oleh {{ team.creator.name }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                Deskripsi
                            </h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-300">
                                {{ team.description || 'Tidak ada deskripsi' }}
                            </p>
                        </div>

                        <div class="mt-8">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    Anggota Tim ({{ team.users.length }})
                                </h3>
                                <button
                                    @click="showAddMemberModal = true"
                                    class="inline-flex items-center px-3 py-2 md:px-4 md:py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
                                >
                                    <PlusIcon class="w-5 h-5 mr-2" />
                                    Tambah Anggota
                                </button>
                            </div>
                            <div class="mt-4 space-y-4">
                                <div v-for="user in team.users" :key="user.id"
                                    class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-gray-300 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                                <span class="text-lg font-medium text-white">
                                                    {{ user.name.charAt(0).toUpperCase() }}
                                                </span>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ user.name }}
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ user.email }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <select
                                            v-if="canManageMembers"
                                            v-model="user.pivot.role"
                                            @change="updateMemberRole(user)"
                                            class="block w-32 px-3 py-2 text-sm bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                                        >
                                            <option value="member">Anggota</option>
                                            <option value="leader">Ketua Tim</option>
                                        </select>
                                        <button
                                            v-if="canManageMembers && user.id !== $page.props.auth.user.id"
                                            @click="removeMember(user)"
                                            class="p-2 text-red-500 transition-colors duration-200 rounded-lg hover:bg-red-100 dark:hover:bg-red-900"
                                            title="Hapus anggota"
                                        >
                                            <TrashIcon class="w-5 h-5" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Tambah Anggota -->
        <Modal :show="showAddMemberModal" @close="closeAddMemberModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                    Tambah Anggota Tim
                </h2>
                <div class="mt-4">
                    <div class="mb-4">
                        <InputLabel for="user" value="Pilih Pengguna" />
                        <select
                            id="user"
                            v-model="form.user_id"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                        >
                            <option value="">Pilih pengguna...</option>
                            <option v-for="user in availableUsers" :key="user.id" :value="user.id">
                                {{ user.name }} ({{ user.email }})
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.user_id" />
                    </div>

                    <div class="mb-4">
                        <InputLabel for="role" value="Peran" />
                        <select
                            id="role"
                            v-model="form.role"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                        >
                            <option value="member">Anggota</option>
                            <option value="leader">Ketua Tim</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.role" />
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button
                        @click="closeAddMemberModal"
                        class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-600 focus:bg-gray-400 dark:focus:bg-gray-600 active:bg-gray-500 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:focus:ring-gray-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                    >
                        Batal
                    </button>
                    <button
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="addMember"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                    >
                        <PlusIcon class="w-5 h-5 mr-2" />
                        {{ form.processing ? 'Menambahkan...' : 'Tambah' }}
                    </button>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, watch } from 'vue';
import { PlusIcon, TrashIcon, PencilSquareIcon } from '@heroicons/vue/24/outline';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import axios from 'axios';

const props = defineProps({
    team: {
        type: Object,
        required: true
    }
});

const showAddMemberModal = ref(false);
const availableUsers = ref([]);

const form = useForm({
    user_id: '',
    role: 'member'
});

// Check if current user can manage members (is a leader)
const canManageMembers = computed(() => {
    const currentUser = props.team.users.find(user => user.id === usePage().props.auth.user.id);
    return currentUser && currentUser.pivot.role === 'leader';
});

const fetchAvailableUsers = async () => {
    try {
        const response = await axios.get(route('teams.available-users', props.team.id));
        availableUsers.value = response.data;
    } catch (error) {
        console.error('Error fetching available users:', error);
    }
};

const closeAddMemberModal = () => {
    showAddMemberModal.value = false;
    form.reset();
};

const addMember = () => {
    form.post(route('teams.members.add', props.team.id), {
        onSuccess: () => closeAddMemberModal()
    });
};

const removeMember = (user) => {
    if (confirm(`Apakah Anda yakin ingin menghapus ${user.name} dari tim?`)) {
        axios.delete(route('teams.members.remove', props.team.id), {
            data: { user_id: user.id }
        }).then(() => {
            window.location.reload();
        });
    }
};

const updateMemberRole = (user) => {
    axios.put(route('teams.members.update-role', props.team.id), {
        user_id: user.id,
        role: user.pivot.role
    }).then(() => {
        window.location.reload();
    });
};

// Fetch available users when modal is opened
watch(showAddMemberModal, (value) => {
    if (value) {
        fetchAvailableUsers();
    }
});
</script> 
<template>
    <form @submit.prevent="handleSubmit" class="space-y-6">
        <div>
            <InputLabel for="event-title" value="Judul" />
            <TextInput
                id="event-title"
                v-model="form.title"
                type="text"
                class="mt-1 block w-full"
                required
                autofocus
            />
            <InputError class="mt-2" :message="form.errors.title" />
        </div>

        <div>
            <InputLabel for="event-description" value="Deskripsi" />
            <TextArea
                id="event-description"
                v-model="form.description"
                class="mt-1 block w-full"
                rows="4"
            />
            <InputError class="mt-2" :message="form.errors.description" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <InputLabel for="event-publish_date" value="Tanggal Publikasi" />
                <TextInput
                    id="event-publish_date"
                    type="datetime-local"
                    class="mt-1 block w-full"
                    :value="form.publish_date"
                    @input="(e) => {
                        console.log('Input event triggered');
                        console.log('New value:', e.target.value);
                        form.publish_date = e.target.value;
                    }"
                    required
                />
                <InputError class="mt-2" :message="form.errors.publish_date" />
            </div>

            <div>
                <InputLabel for="event-deadline" value="Deadline" />
                <TextInput
                    id="event-deadline"
                    type="datetime-local"
                    class="mt-1 block w-full"
                    v-model="form.deadline"
                />
                <InputError class="mt-2" :message="form.errors.deadline" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <InputLabel for="event-platform_id" value="Platform" />
                <SelectInput
                    id="event-platform_id"
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
                <InputLabel for="event-category_id" value="Kategori" />
                <SelectInput
                    id="event-category_id"
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

            <div>
                <InputLabel for="event-team_id" value="Tim" />
                <SelectInput
                    id="event-team_id"
                    class="mt-1 block w-full"
                    v-model="form.team_id"
                >
                    <option value="">Pilih Tim</option>
                    <option v-for="team in teams" :key="team.id" :value="team.id">
                        {{ team.name }}
                    </option>
                </SelectInput>
                <InputError class="mt-2" :message="form.errors.team_id" />
            </div>
        </div>

        <div>
            <InputLabel for="event-status" value="Status" />
            <SelectInput
                id="event-status"
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
            <InputLabel for="event-assignees" value="Penanggung Jawab" />
            <MultiSelect
                id="event-assignees"
                class="mt-1 block w-full"
                v-model="form.assignees"
                :options="users.map(u => ({
                    value: u.id.toString(),
                    label: u.name
                }))"
                multiple
            />
            <InputError class="mt-2" :message="form.errors.assignees" />
        </div>
    </form>
</template>

<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
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
    teams: {
        type: Array,
        required: true
    },
    users: {
        type: Array,
        required: true
    }
});

const emit = defineEmits(['submit']);

const handleSubmit = (e) => {
    console.log('=== EVENT FORM SUBMIT ===');
    console.log('Form Element:', e.target);
    console.log('Form Data:', {
        title: props.form.title,
        description: props.form.description,
        publish_date: props.form.publish_date,
        deadline: props.form.deadline,
        status: props.form.status,
        platform_id: props.form.platform_id,
        category_id: props.form.category_id,
        team_id: props.form.team_id,
        assignees: props.form.assignees
    });
    emit('submit');
};
</script> 
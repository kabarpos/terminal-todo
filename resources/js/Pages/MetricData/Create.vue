<template>
    <Head title="Input Data Metrik" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Input Data Metrik
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Platform & Account Selection -->
                        <div class="grid grid-cols-1 gap-6 mt-4 md:grid-cols-2">
                            <div>
                                <InputLabel for="platform_id" value="Platform" />
                                <select
                                    id="platform_id"
                                    v-model="form.platform_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                    @change="loadAccounts"
                                >
                                    <option value="">Pilih Platform</option>
                                    <option v-for="platform in platforms" :key="platform.id" :value="platform.id">
                                        {{ platform.name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.platform_id" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="account_id" value="Akun" />
                                <select
                                    id="account_id"
                                    v-model="form.account_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                    @change="loadMetrics"
                                >
                                    <option value="">Pilih Akun</option>
                                    <option v-for="account in filteredAccounts" :key="account.id" :value="account.id">
                                        {{ account.name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.account_id" class="mt-2" />
                            </div>
                        </div>

                        <!-- Metric Selection & Value -->
                        <div class="grid grid-cols-1 gap-6 mt-4 md:grid-cols-2">
                            <div>
                                <InputLabel for="metric_id" value="Jenis Metrik" />
                                <select
                                    id="metric_id"
                                    v-model="form.metric_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                >
                                    <option value="">Pilih Metrik</option>
                                    <option v-for="metric in availableMetrics" :key="metric.id" :value="metric.id">
                                        {{ metric.name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.metric_id" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="value" value="Nilai" />
                                <TextInput
                                    id="value"
                                    type="number"
                                    v-model="form.value"
                                    class="mt-1 block w-full"
                                    step="0.01"
                                />
                                <InputError :message="form.errors.value" class="mt-2" />
                            </div>
                        </div>

                        <!-- Date Selection -->
                        <div class="grid grid-cols-1 gap-6 mt-4 md:grid-cols-2">
                            <div>
                                <InputLabel for="recorded_at" value="Tanggal" />
                                <TextInput
                                    id="recorded_at"
                                    type="date"
                                    v-model="form.recorded_at"
                                    class="mt-1 block w-full"
                                />
                                <InputError :message="form.errors.recorded_at" class="mt-2" />
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end mt-6">
                            <PrimaryButton :disabled="form.processing">
                                Simpan Data
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const props = defineProps({
    platforms: {
        type: Array,
        required: true
    },
    accounts: {
        type: Array,
        required: true
    },
    metrics: {
        type: Array,
        required: true
    }
})

const form = useForm({
    platform_id: '',
    account_id: '',
    metric_id: '',
    value: '',
    recorded_at: new Date().toISOString().split('T')[0],
})

const filteredAccounts = computed(() => {
    if (!form.platform_id) return []
    return props.accounts.filter(account => account.platform_id === form.platform_id)
})

const availableMetrics = computed(() => {
    if (!form.platform_id) return []
    return props.metrics.filter(metric => metric.platform_id === form.platform_id)
})

const submit = () => {
    form.post(route('metric-data.store'), {
        onSuccess: () => {
            form.reset()
        }
    })
}
</script> 
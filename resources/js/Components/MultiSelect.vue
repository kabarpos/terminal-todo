<template>
    <div class="relative">
        <!-- Selected Items Display -->
        <div
            class="min-h-[38px] w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus-within:border-indigo-500 dark:focus-within:border-indigo-600 focus-within:ring-1 focus-within:ring-indigo-500 dark:focus-within:ring-indigo-600"
            @click="toggleDropdown"
        >
            <div class="flex flex-wrap gap-1 p-1">
                <!-- Selected Tags -->
                <div
                    v-for="item in selectedItems"
                    :key="item.value"
                    class="flex items-center gap-1 px-2 py-1 text-sm bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded"
                >
                    <span>{{ item.label }}</span>
                    <button
                        type="button"
                        @click.stop="removeItem(item.value)"
                        class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Input for filtering -->
                <input
                    type="text"
                    class="flex-grow min-w-[60px] bg-transparent border-0 focus:ring-0 focus:outline-none p-1 text-sm"
                    :placeholder="selectedItems.length ? '' : 'Pilih item...'"
                    v-model="searchQuery"
                    @keydown.enter.prevent
                    @keydown.space.prevent
                    @keydown.backspace="handleBackspace"
                />
            </div>
        </div>

        <!-- Dropdown -->
        <div
            v-show="isOpen"
            class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md shadow-lg max-h-60 overflow-auto"
        >
            <div
                v-for="option in filteredOptions"
                :key="option.value"
                class="px-3 py-2 text-sm cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700"
                :class="{
                    'bg-gray-50 dark:bg-gray-700': isSelected(option.value)
                }"
                @click="toggleItem(option)"
            >
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        :checked="isSelected(option.value)"
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600"
                        @click.stop
                    >
                    <span class="ml-2">{{ option.label }}</span>
                </div>
            </div>

            <div
                v-if="filteredOptions.length === 0"
                class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400"
            >
                Tidak ada pilihan yang tersedia
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => []
    },
    options: {
        type: Array,
        required: true,
        validator: (value) => {
            return value.every(option => 
                typeof option === 'object' && 
                'value' in option && 
                'label' in option
            );
        }
    },
    multiple: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const searchQuery = ref('');

// Computed properties
const selectedItems = computed(() => {
    return props.options.filter(option => props.modelValue.includes(option.value));
});

const filteredOptions = computed(() => {
    return props.options.filter(option => 
        option.label.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

// Methods
const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
};

const closeDropdown = (e) => {
    if (!e.target.closest('.relative')) {
        isOpen.value = false;
        searchQuery.value = '';
    }
};

const isSelected = (value) => {
    return props.modelValue.includes(value);
};

const toggleItem = (option) => {
    const newValue = [...props.modelValue];
    const index = newValue.indexOf(option.value);

    if (index === -1) {
        if (props.multiple) {
            newValue.push(option.value);
        } else {
            newValue.splice(0, newValue.length, option.value);
            isOpen.value = false;
        }
    } else {
        newValue.splice(index, 1);
    }

    emit('update:modelValue', newValue);
};

const removeItem = (value) => {
    const newValue = props.modelValue.filter(v => v !== value);
    emit('update:modelValue', newValue);
};

const handleBackspace = (e) => {
    if (searchQuery.value === '' && props.modelValue.length > 0) {
        const newValue = [...props.modelValue];
        newValue.pop();
        emit('update:modelValue', newValue);
    }
};

// Lifecycle hooks
onMounted(() => {
    document.addEventListener('click', closeDropdown);
});

onUnmounted(() => {
    document.removeEventListener('click', closeDropdown);
});

// Watch for changes
watch(isOpen, (newValue) => {
    if (!newValue) {
        searchQuery.value = '';
    }
});
</script>

<style scoped>
.relative {
    position: relative;
}
</style> 
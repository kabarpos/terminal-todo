<template>
    <img
        :src="optimizedSrc"
        :alt="alt"
        :class="className"
        loading="lazy"
        decoding="async"
        :width="width"
        :height="height"
        @error="handleError"
        v-bind="$attrs"
    />
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    src: {
        type: String,
        required: true
    },
    alt: {
        type: String,
        default: ''
    },
    className: {
        type: String,
        default: ''
    },
    width: {
        type: [Number, String],
        default: null
    },
    height: {
        type: [Number, String],
        default: null
    },
    quality: {
        type: Number,
        default: 80
    },
    format: {
        type: String,
        default: 'webp'
    }
});

const imageError = ref(false);

// Fallback image jika gambar utama gagal dimuat
const fallbackImage = '/images/placeholder.jpg';

// Computed property untuk menghasilkan URL gambar yang dioptimasi
const optimizedSrc = computed(() => {
    if (imageError.value) return fallbackImage;
    
    const url = new URL(props.src, window.location.origin);
    
    // Tambahkan parameter optimasi
    if (props.width) url.searchParams.append('w', props.width);
    if (props.height) url.searchParams.append('h', props.height);
    url.searchParams.append('q', props.quality);
    url.searchParams.append('fm', props.format);
    
    // Tambahkan cache buster untuk development
    if (process.env.NODE_ENV !== 'production') {
        url.searchParams.append('t', Date.now());
    }
    
    return url.toString();
});

const handleError = () => {
    imageError.value = true;
};
</script> 
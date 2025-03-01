<template>
  <AuthenticatedLayout title="Tambah Feed Baru">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Tambah Feed Baru
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <form @submit.prevent="submit" class="space-y-6">
              <div>
                <InputLabel for="url" value="URL" />
                <TextInput
                  id="url"
                  type="url"
                  class="mt-1 block w-full"
                  v-model="form.url"
                  required
                  autofocus
                  placeholder="https://example.com/article"
                />
                <InputError class="mt-2" :message="form.errors.url" />
              </div>

              <div>
                <InputLabel for="type" value="Tipe" />
                <SelectInput
                  id="type"
                  class="mt-1 block w-full"
                  v-model="form.type"
                  required
                >
                  <option value="news">Berita</option>
                  <option value="video">Video</option>
                  <option value="social_media">Media Sosial</option>
                  <option value="image">Gambar</option>
                </SelectInput>
                <InputError class="mt-2" :message="form.errors.type" />
              </div>

              <div v-if="preview" class="mt-6">
                <h3 class="text-lg font-medium text-gray-900">Preview</h3>
                <div class="mt-4 bg-gray-50 rounded-lg p-4">
                  <div v-if="preview.image_url" class="aspect-video bg-gray-100 mb-4">
                    <img :src="preview.image_url" :alt="preview.title" class="w-full h-full object-cover rounded-lg">
                  </div>
                  <div v-if="preview.video_url" class="aspect-video bg-gray-100 mb-4">
                    <video :src="preview.video_url" class="w-full h-full object-cover rounded-lg" controls></video>
                  </div>
                  <h4 class="text-xl font-semibold">{{ preview.title }}</h4>
                  <p class="mt-2 text-gray-600">{{ preview.description }}</p>
                  <div class="mt-4 text-sm text-gray-500">
                    {{ preview.site_name }}
                  </div>
                </div>
              </div>

              <div class="flex items-center justify-end mt-4">
                <Link
                  :href="route('news-feeds.index')"
                  class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3"
                >
                  Batal
                </Link>
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                  Simpan
                </PrimaryButton>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import SelectInput from '@/Components/SelectInput.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { ref, watch } from 'vue'
import axios from 'axios'

const preview = ref(null)
const form = useForm({
  url: '',
  type: 'news'
})

const fetchPreview = async (url) => {
  try {
    const response = await axios.post(route('news-feeds.preview'), { url })
    preview.value = response.data
  } catch (error) {
    console.error('Error fetching preview:', error)
  }
}

watch(() => form.url, async (newUrl) => {
  if (newUrl) {
    await fetchPreview(newUrl)
  } else {
    preview.value = null
  }
})

const submit = () => {
  form.post(route('news-feeds.store'), {
    onSuccess: () => {
      form.reset()
      preview.value = null
    }
  })
}
</script> 
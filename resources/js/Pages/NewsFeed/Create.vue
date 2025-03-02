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
                <InputLabel for="type" value="Tipe" />
                <SelectInput
                  id="type"
                  class="mt-1 block w-full"
                  v-model="form.type"
                  required
                >
                  <option value="news">Berita</option>
                  <option value="video">Video</option>
                  <option value="image">Gambar</option>
                </SelectInput>
                <InputError class="mt-2" :message="form.errors.type" />
              </div>

              <!-- Form untuk tipe image -->
              <template v-if="form.type === 'image'">
                <div class="space-y-4">
                  <div>
                    <InputLabel for="image" value="Upload Gambar" />
                    <input
                      type="file"
                      id="image"
                      accept="image/*"
                      @change="handleImageUpload"
                      class="mt-1 block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-md file:border-0
                        file:text-sm file:font-semibold
                        file:bg-indigo-50 file:text-indigo-700
                        hover:file:bg-indigo-100"
                    />
                    <InputError class="mt-2" :message="form.errors.image" />
                  </div>

                  <div v-if="imagePreview" class="mt-4">
                    <div class="aspect-video bg-gray-100 rounded-lg overflow-hidden">
                      <img :src="imagePreview" class="w-full h-full object-contain" />
                    </div>
                  </div>

                  <div>
                    <InputLabel for="title" value="Judul" />
                    <TextInput
                      id="title"
                      type="text"
                      class="mt-1 block w-full"
                      v-model="form.title"
                      placeholder="Judul gambar"
                    />
                    <InputError class="mt-2" :message="form.errors.title" />
                  </div>

                  <div>
                    <InputLabel for="description" value="Deskripsi" />
                    <textarea
                      id="description"
                      v-model="form.description"
                      rows="3"
                      class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                      placeholder="Deskripsi gambar"
                    ></textarea>
                    <InputError class="mt-2" :message="form.errors.description" />
                  </div>
                </div>
              </template>

              <!-- Form untuk tipe lain (news/video) -->
              <div v-else>
                <InputLabel for="url" value="URL" />
                <TextInput
                  id="url"
                  type="url"
                  class="mt-1 block w-full"
                  v-model="form.url"
                  required
                  :placeholder="form.type === 'video' ? 'https://www.youtube.com/watch?v=...' : 'https://example.com/article'"
                />
                <InputError class="mt-2" :message="form.errors.url" />
              </div>

              <div v-if="preview" class="mt-6">
                <h3 class="text-lg font-medium text-gray-900">Preview</h3>
                <div class="mt-4 bg-gray-50 rounded-lg p-4">
                  <div v-if="preview.image_url" class="aspect-video bg-gray-100 mb-4">
                    <img :src="preview.image_url" :alt="preview.title" class="w-full h-full object-cover rounded-lg">
                  </div>
                  <div v-if="preview.video_url" class="aspect-video bg-gray-100 mb-4">
                    <iframe
                      :src="preview.video_url"
                      class="w-full h-full rounded-lg"
                      frameborder="0"
                      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                      allowfullscreen
                    ></iframe>
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
const imagePreview = ref(null)
const form = useForm({
  url: '',
  type: 'news',
  image: null,
  title: '',
  description: ''
})

const fetchPreview = async (url) => {
  try {
    const response = await axios.post(route('news-feeds.preview'), { url })
    preview.value = response.data
  } catch (error) {
    console.error('Error fetching preview:', error)
  }
}

const handleImageUpload = (e) => {
  const file = e.target.files[0]
  if (!file) return

  form.image = file
  
  // Create preview URL
  imagePreview.value = URL.createObjectURL(file)
}

const submit = () => {
  if (form.type === 'image') {
    form.post(route('news-feeds.store'), {
      forceFormData: true,
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => {
        form.reset()
        imagePreview.value = null
      },
      onError: (errors) => {
        console.error('Upload errors:', errors)
      }
    })
  } else {
    form.post(route('news-feeds.store'), {
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => {
        form.reset()
        preview.value = null
      }
    })
  }
}

watch(() => form.type, (newType) => {
  form.reset('url', 'image', 'title', 'description')
  preview.value = null
  imagePreview.value = null
})

watch(() => form.url, async (newUrl) => {
  if (newUrl && form.type !== 'image') {
    await fetchPreview(newUrl)
  } else {
    preview.value = null
  }
})
</script> 
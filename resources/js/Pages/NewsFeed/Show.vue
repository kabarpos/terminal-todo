<template>
  <AuthenticatedLayout :title="feed.title">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Detail Feed
        </h2>
        <div class="flex items-center space-x-4" v-if="$page.props.auth.user.id === feed.user_id">
          <Link
            :href="route('news-feeds.edit', feed.id)"
            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
          >
            Edit
          </Link>
          <button
            @click="destroy"
            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700"
          >
            Hapus
          </button>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div class="max-w-3xl mx-auto">
              <div class="mb-6">
                <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-2 text-sm text-gray-500">
                    <span class="px-2 py-1 bg-gray-100 rounded-full capitalize">{{ feed.type }}</span>
                    <span>•</span>
                    <span>{{ feed.site_name }}</span>
                    <span>•</span>
                    <span>{{ formatDate(feed.created_at) }}</span>
                  </div>
                  <div class="flex items-center space-x-3">
                    <button 
                      @click="copyToClipboard"
                      class="text-gray-500 hover:text-gray-700"
                      title="Salin URL"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                      </svg>
                    </button>
                    <a 
                      :href="getTwitterShareUrl()"
                      target="_blank"
                      class="text-gray-500 hover:text-gray-700"
                      title="Bagikan ke Twitter"
                    >
                      <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                      </svg>
                    </a>
                  </div>
                </div>
                <h1 class="text-3xl font-bold mt-4">{{ feed.title }}</h1>
                <div class="flex items-center mt-4 space-x-2">
                  <img 
                    :src="feed.user.profile_photo_url || '/default-avatar.png'" 
                    :alt="feed.user.name" 
                    class="w-8 h-8 rounded-full object-cover bg-gray-200"
                    @error="$event.target.src = '/default-avatar.png'"
                  >
                  <span class="text-gray-600">{{ feed.user.name }}</span>
                </div>
              </div>

              <div v-if="feed.image_url" class="aspect-video bg-gray-100 mb-6 rounded-lg overflow-hidden shadow-lg">
                <img :src="feed.image_url" :alt="feed.title" class="w-full h-full object-cover">
              </div>

              <div v-if="feed.type === 'video' && feed.video_url" class="aspect-video bg-gray-100 mb-6 rounded-lg overflow-hidden shadow-lg">
                <iframe
                  :src="feed.video_url"
                  class="w-full h-full"
                  frameborder="0"
                  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                  allowfullscreen
                ></iframe>
              </div>

              <div class="prose max-w-none mb-8">
                <p class="text-gray-700 text-lg leading-relaxed">{{ feed.description }}</p>
              </div>

              <!-- Video Information -->
              <div v-if="feed.type === 'video' && feed.meta_data" class="mt-8 border-t pt-8">
                <div class="flex items-center space-x-4 mb-6">
                  <a :href="feed.meta_data.author_url" target="_blank" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900">
                    <span class="font-medium">{{ feed.meta_data.author_name }}</span>
                  </a>
                  <div class="flex items-center space-x-2 text-gray-500">
                    <span v-if="feed.meta_data.view_count">{{ formatNumber(feed.meta_data.view_count) }} x ditonton</span>
                    <span v-if="feed.meta_data.publish_date">• {{ formatDate(feed.meta_data.publish_date) }}</span>
                  </div>
                </div>
              </div>

              <!-- Article Content -->
              <div v-if="feed.meta_data?.content" class="mt-8 border-t pt-8">
                <h2 class="text-xl font-semibold mb-4">Konten Artikel</h2>
                <div class="prose max-w-none">
                  <div class="text-gray-700 leading-relaxed space-y-4" v-html="formatContent(feed.meta_data.content)"></div>
                </div>
              </div>

              <div class="mt-8 bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Artikel</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <p class="text-sm text-gray-500">Sumber</p>
                    <p class="text-gray-900">{{ feed.site_name }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500">Tanggal Publikasi</p>
                    <p class="text-gray-900">{{ formatDate(feed.created_at) }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500">Tipe Konten</p>
                    <p class="text-gray-900 capitalize">{{ feed.type }}</p>
                  </div>
                  <div v-if="feed.url">
                    <p class="text-sm text-gray-500">Domain</p>
                    <p class="text-gray-900">{{ getDomain(feed.url) }}</p>
                  </div>
                </div>
              </div>

              <div class="border-t pt-8 mt-8">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                  <a
                    :href="feed.url"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  >
                    <span>Buka di {{ feed.site_name }}</span>
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                  </a>
                </div>
              </div>

              <div v-if="showCopiedMessage" class="fixed bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg">
                URL berhasil disalin!
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref, onMounted, watch } from 'vue'

const props = defineProps({
  feed: Object
})

const showCopiedMessage = ref(false)

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const getDomain = (url) => {
  try {
    return new URL(url).hostname.replace('www.', '')
  } catch {
    return ''
  }
}

const getTwitterShareUrl = () => {
  if (typeof window !== 'undefined') {
    return `https://twitter.com/intent/tweet?url=${encodeURIComponent(window.location.href)}&text=${encodeURIComponent(props.feed.title)}`
  }
  return '#'
}

const copyToClipboard = async () => {
  if (typeof window === 'undefined') return

  try {
    await navigator.clipboard.writeText(window.location.href)
    showCopiedMessage.value = true
    setTimeout(() => {
      showCopiedMessage.value = false
    }, 2000)
  } catch (err) {
    console.error('Failed to copy:', err)
  }
}

const destroy = () => {
  if (confirm('Apakah Anda yakin ingin menghapus feed ini?')) {
    router.delete(route('news-feeds.destroy', props.feed.id))
  }
}

const formatContent = (content) => {
  if (!content) return '';
  // Split content by newlines and wrap each paragraph in <p> tags
  return content.split('\n\n')
    .filter(p => p.trim())
    .map(p => `<p>${p}</p>`)
    .join('');
}

const formatNumber = (number) => {
  return new Intl.NumberFormat('id-ID').format(number);
}
</script> 
<template>
  <AuthenticatedLayout title="News Feed">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          News Feed
        </h2>
        <Link
          :href="route('news-feeds.create')"
          class="inline-flex items-center px-3 py-2 md:px-4 md:py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
        >
          <span class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            <span class="hidden md:inline ml-2">Tambah Feed</span>
          </span>
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filter Section -->
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
          <div class="flex items-center space-x-4">
            <button 
              v-for="type in ['all', 'news', 'video', 'image']" 
              :key="type"
              @click="selectedType = type"
              class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200"
              :class="{
                'bg-gradient-to-r from-blue-600 to-purple-600 text-white': selectedType === type,
                'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600': selectedType !== type
              }"
            >
              {{ type === 'all' ? 'Semua' : type === 'news' ? 'Berita' : type === 'video' ? 'Video' : 'Gambar' }}
            </button>
          </div>
          <div class="text-sm text-gray-500 dark:text-gray-400">
            Total: {{ filteredFeeds.length }} feed
          </div>
        </div>

        <div class="space-y-8">
          <!-- Monthly Groups -->
          <div v-for="(group, month) in groupedFeeds" :key="month" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="border-b border-gray-200 dark:border-gray-700 p-4">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ month }}</h3>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ group.length }} feed</span>
              </div>
            </div>
            
            <div class="p-6">
              <div v-if="group.length === 0" class="text-center py-8">
                <p class="text-gray-500 dark:text-gray-400">Belum ada feed yang ditambahkan.</p>
              </div>
              <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="feed in group" :key="feed.id" class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
                  <Link :href="route('news-feeds.show', feed.id)" class="block hover:opacity-75">
                    <div v-if="feed.image_url" class="aspect-video bg-gray-100 dark:bg-gray-700">
                      <img :src="feed.type === 'news' || feed.type === 'video' ? feed.image_url : feed.image_url_full" :alt="feed.title" class="w-full h-full object-cover">
                    </div>
                    <div v-else-if="feed.video_url" class="aspect-video bg-gray-100 dark:bg-gray-700">
                      <video :src="feed.video_url" class="w-full h-full object-cover" controls></video>
                    </div>
                    <div class="p-4">
                      <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400 mb-2">
                        <span class="capitalize">{{ feed.type }}</span>
                        <span>•</span>
                        <span>{{ feed.site_name }}</span>
                      </div>
                      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ feed.title }}</h3>
                      <p class="text-gray-600 dark:text-gray-300 text-sm line-clamp-2">{{ feed.description }}</p>
                      <div class="mt-4 flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                          <img 
                            :src="feed.user.profile_photo_url || '/default-avatar.png'" 
                            :alt="feed.user.name" 
                            class="w-6 h-6 rounded-full object-cover bg-gray-200 dark:bg-gray-700"
                            @error="$event.target.src = '/default-avatar.png'"
                          >
                          <span class="text-sm text-gray-600 dark:text-gray-300">{{ feed.user.name }}</span>
                        </div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ formatDate(feed.created_at, true) }}</span>
                      </div>
                    </div>
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <Pagination :links="feeds.links" class="mt-6" />
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link } from '@inertiajs/vue3'
import Pagination from '@/Components/Pagination.vue'
import { ref, computed } from 'vue'

const props = defineProps({
  feeds: Object
})

const selectedType = ref('all')

// Filter feeds berdasarkan tipe yang dipilih
const filteredFeeds = computed(() => {
  if (selectedType.value === 'all') {
    return props.feeds.data
  }
  return props.feeds.data.filter(feed => feed.type === selectedType.value)
})

// Kelompokkan feeds berdasarkan bulan
const groupedFeeds = computed(() => {
  const groups = {}
  
  filteredFeeds.value.forEach(feed => {
    const date = new Date(feed.created_at)
    const monthYear = new Intl.DateTimeFormat('id-ID', { 
      year: 'numeric',
      month: 'long'
    }).format(date)
    
    if (!groups[monthYear]) {
      groups[monthYear] = []
    }
    groups[monthYear].push(feed)
  })
  
  // Urutkan bulan dari yang terbaru
  return Object.fromEntries(
    Object.entries(groups).sort((a, b) => {
      const dateA = new Date(a[1][0].created_at)
      const dateB = new Date(b[1][0].created_at)
      return dateB - dateA
    })
  )
})

const formatDate = (date, showDay = false) => {
  const options = {
    year: 'numeric',
    month: 'long',
    day: showDay ? 'numeric' : undefined
  }
  return new Date(date).toLocaleDateString('id-ID', options)
}
</script> 
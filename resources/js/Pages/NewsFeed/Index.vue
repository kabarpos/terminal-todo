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
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <div v-if="feeds.data.length === 0" class="text-center py-8">
              <p class="text-gray-500 dark:text-gray-400">Belum ada feed yang ditambahkan.</p>
            </div>
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div v-for="feed in feeds.data" :key="feed.id" class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
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
                      <span class="text-sm text-gray-500 dark:text-gray-400">{{ formatDate(feed.created_at) }}</span>
                    </div>
                  </div>
                </Link>
              </div>
            </div>
            <Pagination :links="feeds.links" class="mt-6" />
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link } from '@inertiajs/vue3'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  feeds: Object
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}
</script> 
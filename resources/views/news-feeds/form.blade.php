@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">
            {{ isset($newsFeed) ? 'Edit Feed' : 'Tambah Feed Baru' }}
        </h1>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ isset($newsFeed) ? route('news-feeds.update', $newsFeed) : route('news-feeds.store') }}"
              method="POST"
              enctype="multipart/form-data"
              class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @csrf
            @if(isset($newsFeed))
                @method('PUT')
            @endif

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="type">
                    Tipe Konten
                </label>
                <select name="type" 
                        id="type" 
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        onchange="toggleFields()">
                    <option value="news" {{ (isset($newsFeed) && $newsFeed->type == 'news') ? 'selected' : '' }}>
                        Berita
                    </option>
                    <option value="video" {{ (isset($newsFeed) && $newsFeed->type == 'video') ? 'selected' : '' }}>
                        Video
                    </option>
                    <option value="social_media" {{ (isset($newsFeed) && $newsFeed->type == 'social_media') ? 'selected' : '' }}>
                        Media Sosial
                    </option>
                    <option value="image" {{ (isset($newsFeed) && $newsFeed->type == 'image') ? 'selected' : '' }}>
                        Gambar
                    </option>
                </select>
            </div>

            <div id="url-field" class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="url">
                    URL
                </label>
                <input type="url" 
                       name="url" 
                       id="url" 
                       value="{{ old('url', $newsFeed->url ?? '') }}"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       placeholder="https://example.com">
            </div>

            <div id="image-fields" class="mb-6" style="display: none;">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="title">
                        Judul
                    </label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title', $newsFeed->title ?? '') }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           placeholder="Judul gambar">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                        Deskripsi
                    </label>
                    <textarea name="description" 
                              id="description" 
                              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                              rows="3"
                              placeholder="Deskripsi gambar">{{ old('description', $newsFeed->description ?? '') }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="image">
                        Upload Gambar
                    </label>
                    <input type="file" 
                           name="image" 
                           id="image" 
                           accept="image/*"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @if(isset($newsFeed) && $newsFeed->image_url)
                        <div class="mt-2">
                            <img src="{{ Storage::url($newsFeed->image_url) }}" 
                                 alt="Current image" 
                                 class="max-w-xs">
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    {{ isset($newsFeed) ? 'Update' : 'Simpan' }}
                </button>
                <a href="{{ route('news-feeds.index') }}" 
                   class="text-blue-500 hover:text-blue-800">
                    Kembali
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleFields() {
    const type = document.getElementById('type').value;
    const urlField = document.getElementById('url-field');
    const imageFields = document.getElementById('image-fields');
    
    if (type === 'image') {
        urlField.style.display = 'none';
        imageFields.style.display = 'block';
    } else {
        urlField.style.display = 'block';
        imageFields.style.display = 'none';
    }
}

// Call on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleFields();
});
</script>
@endpush
@endsection 
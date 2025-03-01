@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">News Feed</h1>
        <a href="{{ route('news-feeds.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Tambah Feed Baru
        </a>
    </div>

    @if (session('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($newsFeeds as $feed)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                @switch($feed->type)
                    @case('image')
                        <div class="relative pb-2/3">
                            <img src="{{ Storage::url($feed->image_url) }}" 
                                 alt="{{ $feed->title }}" 
                                 class="absolute h-full w-full object-cover">
                        </div>
                        <div class="p-4">
                            <h2 class="text-xl font-semibold mb-2">{{ $feed->title }}</h2>
                            @if($feed->description)
                                <p class="text-gray-600">{{ $feed->description }}</p>
                            @endif
                        </div>
                        @break

                    @case('video')
                        <div class="relative pb-2/3">
                            @if($feed->video_url)
                                <iframe src="{{ $feed->video_url }}" 
                                        class="absolute h-full w-full object-cover" 
                                        frameborder="0" 
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                        allowfullscreen>
                                </iframe>
                            @elseif($feed->image_url)
                                <img src="{{ $feed->image_url }}" 
                                     alt="{{ $feed->title }}" 
                                     class="absolute h-full w-full object-cover">
                            @endif
                        </div>
                        <div class="p-4">
                            <h2 class="text-xl font-semibold mb-2">
                                <a href="{{ $feed->url }}" target="_blank" class="hover:text-blue-500">
                                    {{ $feed->title }}
                                </a>
                            </h2>
                            <p class="text-gray-600">{{ $feed->description }}</p>
                            <div class="mt-2 text-sm text-gray-500">
                                {{ $feed->site_name }}
                                @if(isset($feed->meta_data['duration']))
                                    • {{ gmdate("i:s", $feed->meta_data['duration']) }}
                                @endif
                            </div>
                        </div>
                        @break

                    @case('social_media')
                        <div class="p-4">
                            <div class="flex items-center mb-4">
                                @if($feed->image_url)
                                    <img src="{{ $feed->image_url }}" 
                                         alt="Profile" 
                                         class="w-12 h-12 rounded-full mr-4">
                                @endif
                                <div>
                                    <h2 class="font-semibold">{{ $feed->site_name }}</h2>
                                    @if(isset($feed->meta_data['author']))
                                        <p class="text-sm text-gray-600">{{ $feed->meta_data['author'] }}</p>
                                    @endif
                                </div>
                            </div>
                            <p class="text-gray-800 mb-4">{{ $feed->description }}</p>
                            <a href="{{ $feed->url }}" 
                               target="_blank" 
                               class="text-blue-500 hover:underline">
                                Lihat di {{ $feed->site_name }}
                            </a>
                        </div>
                        @break

                    @default
                        <div class="relative pb-2/3">
                            @if($feed->image_url)
                                <img src="{{ $feed->image_url }}" 
                                     alt="{{ $feed->title }}" 
                                     class="absolute h-full w-full object-cover">
                            @endif
                        </div>
                        <div class="p-4">
                            <h2 class="text-xl font-semibold mb-2">
                                <a href="{{ $feed->url }}" target="_blank" class="hover:text-blue-500">
                                    {{ $feed->title }}
                                </a>
                            </h2>
                            <p class="text-gray-600">{{ $feed->description }}</p>
                            <div class="mt-2 text-sm text-gray-500">
                                {{ $feed->site_name }}
                                @if(isset($feed->meta_data['published_date']))
                                    • {{ \Carbon\Carbon::parse($feed->meta_data['published_date'])->format('d M Y') }}
                                @endif
                            </div>
                        </div>
                @endswitch

                <div class="px-4 pb-4 flex justify-between items-center">
                    <div class="text-sm text-gray-500">
                        {{ $feed->created_at->diffForHumans() }}
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('news-feeds.edit', $feed) }}" 
                           class="text-blue-500 hover:text-blue-700">
                            Edit
                        </a>
                        <form action="{{ route('news-feeds.destroy', $feed) }}" 
                              method="POST" 
                              class="inline"
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus feed ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $newsFeeds->links() }}
    </div>
</div>
@endsection 
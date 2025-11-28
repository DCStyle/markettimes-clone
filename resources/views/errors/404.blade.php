@extends('layouts.main')

@section('content')
<div class="bg-beige min-h-screen">
    <div class="max-w-6xl mx-auto px-4 py-8 lg:py-12">
        {{-- Hero Section --}}
        <div class="text-center mb-10 lg:mb-14">
            {{-- Large 404 Number --}}
            <div class="relative mb-6">
                <span class="text-[12rem] lg:text-[16rem] font-black text-gray-100 leading-none select-none">
                    404
                </span>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <h1 class="text-2xl lg:text-4xl font-bold text-gray-900 mb-3">
                            Không tìm thấy đường dẫn này
                        </h1>
                        <p class="text-gray-600 text-base lg:text-lg max-w-md mx-auto">
                            Trang bạn tìm kiếm không tồn tại, hoặc đã được chuyển đến một địa chỉ khác
                        </p>
                    </div>
                </div>
            </div>

            {{-- Search Bar --}}
            <div class="max-w-xl mx-auto mt-8">
                <form action="{{ route('search') }}" method="GET" class="flex gap-2">
                    <input
                        type="text"
                        name="q"
                        placeholder="Tìm kiếm bài viết..."
                        class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition"
                    >
                    <button
                        type="submit"
                        class="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition-colors"
                    >
                        Tìm kiếm
                    </button>
                </form>
            </div>
        </div>

        {{-- Quick Navigation - Categories --}}
        @if(isset($categories) && $categories->count() > 0)
        <div class="mb-10 lg:mb-14">
            <div class="flex flex-wrap justify-center gap-2 lg:gap-3">
                <a href="{{ url('/') }}"
                   class="px-4 py-2 bg-white border border-gray-200 rounded-full text-gray-700 font-medium hover:bg-teal-50 hover:border-teal-200 hover:text-teal-700 transition-colors">
                    Trang chủ
                </a>
                @foreach($categories->take(6) as $category)
                <a href="{{ route('category.show', $category->slug) }}"
                   class="px-4 py-2 bg-white border border-gray-200 rounded-full text-gray-700 font-medium hover:bg-teal-50 hover:border-teal-200 hover:text-teal-700 transition-colors">
                    {{ $category->name }}
                </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Articles Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
            {{-- Most Read Section --}}
            @if(isset($popularArticles) && $popularArticles->count() > 0)
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 pb-3 border-b-2 border-teal-600">
                        Đọc nhiều
                    </h2>

                    <div class="space-y-4">
                        @foreach($popularArticles as $index => $article)
                        <div class="flex gap-3 items-start">
                            <div class="flex-shrink-0 text-gray-200 text-3xl font-black leading-none">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('article.show', $article->slug . '-' . $article->id) }}"
                                   class="block">
                                    <h3 class="text-sm font-semibold text-gray-900 hover:text-teal-600 transition-colors line-clamp-2">
                                        {{ $article->title }}
                                    </h3>
                                </a>
                                <div class="flex items-center gap-2 mt-1 text-xs text-gray-500">
                                    <span class="text-teal-600 font-medium">
                                        {{ $article->category->name }}
                                    </span>
                                    <span>{{ $article->published_at ? $article->published_at->diffForHumans() : $article->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                        @if(!$loop->last)
                        <hr class="border-gray-100">
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Latest Articles Section --}}
            @if(isset($latestArticles) && $latestArticles->count() > 0)
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 pb-3 border-b-2 border-teal-600">
                        Bài viết mới
                    </h2>

                    <div class="space-y-0">
                        @foreach($latestArticles as $article)
                        <x-article-card :article="$article" layout="horizontal" />
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Back to Home Button --}}
        <div class="text-center mt-10 lg:mt-14">
            <a href="{{ url('/') }}"
               class="inline-flex items-center gap-2 px-8 py-4 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Về trang chủ
            </a>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Ensure 404 text doesn't cause horizontal scroll */
    .text-\[12rem\], .text-\[16rem\] {
        line-height: 0.8;
    }
</style>
@endpush

@extends('layouts.main')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Kết quả tìm kiếm</h1>

        @if($query)
        <p class="text-gray-600">
            Tìm kiếm cho: <span class="font-semibold">"{{ $query }}"</span>
        </p>
        @endif
    </div>

    <!-- Search Form -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form action="{{ route('search') }}" method="GET">
            <div class="flex gap-2">
                <input type="text"
                       name="q"
                       value="{{ $query }}"
                       placeholder="Nhập từ khóa tìm kiếm..."
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-600 focus:border-transparent">
                <button type="submit"
                        class="max-sm:text-xs px-4 sm:px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors">
                    Tìm kiếm
                </button>
            </div>
        </form>
    </div>

    <!-- Search Results -->
    @if($query && $articles instanceof \Illuminate\Pagination\LengthAwarePaginator && $articles->total() > 0)
        <div class="mb-6">
            <p class="text-sm text-gray-600">
                Tìm thấy <span class="font-semibold">{{ $articles->total() }}</span> kết quả
            </p>
        </div>

        <!-- Results Container -->
        <div id="search-results-container" class="space-y-4 mb-8">
            @include('search.partials.results', ['articles' => $articles])
        </div>

        <!-- Load More Button -->
        @if($articles->hasMorePages())
            <div class="text-center">
                <button id="load-more-btn"
                        data-page="{{ $articles->currentPage() + 1 }}"
                        data-query="{{ $query }}"
                        class="px-8 py-3 bg-teal-600 text-white font-semibold rounded-lg hover:bg-teal-700 transition-colors">
                    Xem thêm
                </button>
                <div id="loading-spinner" class="hidden mt-4">
                    <svg class="animate-spin h-8 w-8 mx-auto text-teal-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
        @endif
    @elseif($query && $articles instanceof \Illuminate\Pagination\LengthAwarePaginator && $articles->total() === 0)
        <!-- No Results Found -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-12 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Không tìm thấy kết quả</h3>
            <p class="text-gray-600">
                Không có bài viết nào phù hợp với từ khóa "<strong>{{ $query }}</strong>".<br>
                Hãy thử tìm kiếm với từ khóa khác.
            </p>
        </div>
    @elseif(!$query)
        <!-- Empty State - No Query -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-12 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Nhập từ khóa để tìm kiếm</h3>
            <p class="text-gray-600">
                Tìm kiếm các bài viết về tài chính, chứng khoán, bất động sản và nhiều hơn nữa.
            </p>
        </div>
    @endif
</div>
@endsection

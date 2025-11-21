@props(['title', 'url' => null])

<div class="flex items-center justify-between mb-6 pb-3 border-b-2 border-teal-600">
    <h3 class="text-2xl font-bold text-gray-900">
        {{ $title }}
    </h3>

    @if($url)
        <a href="{{ $url }}"
           class="text-sm text-teal-600 hover:text-teal-700 font-semibold transition-colors flex items-center gap-1">
            Xem thêm
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    @endif
</div>

@extends('layouts.main')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
    <article class="bg-white rounded-lg shadow p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">{{ $page->title }}</h1>

        <div class="prose prose-lg max-w-none">
            {!! $page->content !!}
        </div>

        @if($page->updated_at->gt($page->created_at))
        <div class="mt-8 pt-6 border-t text-sm text-gray-500">
            Cập nhật lần cuối: {{ $page->updated_at->format('d/m/Y H:i') }}
        </div>
        @endif
    </article>
</div>
@endsection

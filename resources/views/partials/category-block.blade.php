@props(['category'])

@if($category->articles->count() > 0)
<section class="bg-white rounded-lg p-6 mb-8">
    <x-section-heading
        :title="$category->name"
        :url="route('category.show', $category)" />

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($category->articles as $article)
            <x-article-card :article="$article" layout="grid" />
        @endforeach
    </div>
</section>
@endif

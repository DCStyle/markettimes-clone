@if(isset($businessArticles) && $businessArticles->count() > 0)
<div class="bg-white rounded-lg p-6">
    <x-section-heading
        title="Nhịp cầu doanh nghiệp"
        :url="route('category.show', 'kinh-doanh')" />

    <div class="space-y-4">
        @foreach($businessArticles as $article)
            <x-article-card :article="$article" layout="small" />
        @endforeach
    </div>
</div>
@endif

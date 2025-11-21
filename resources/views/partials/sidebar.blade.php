<div class="lg:sticky top-0">
    <!-- Most Read Section -->
    <div class="mb-4 lg:mb-8">
        <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b-2 border-teal-600">
            Đọc nhiều
        </h3>

        @if(isset($mostRead) && $mostRead->count() > 0)
            <div class="space-y-4">
                @foreach($mostRead as $index => $article)
                    <div class="flex gap-3 items-start">
                        <!-- Number Badge -->
                        <div class="flex-shrink-0 w-7 h-7 bg-red-600 text-white rounded-full flex items-center justify-center text-sm font-bold">
                            {{ $index + 1 }}
                        </div>

                        <!-- Article Info -->
                        <div class="flex-1 min-w-0">
                            @if($article->featured_image)
                                <img src="{{ Storage::url($article->featured_image) }}"
                                     alt="{{ $article->title }}"
                                     class="w-full aspect-[3/2] object-cover rounded mb-2">
                            @endif

                            <a href="{{ route('article.show', [$article->category, $article->slug . '-' . $article->id]) }}"
                               class="block">
                                <h4 class="text-sm font-semibold text-gray-900 hover:text-teal-600 transition-colors line-clamp-2">
                                    {{ $article->title }}
                                </h4>
                            </a>

                            <div class="flex items-center gap-2 mt-1 text-xs text-gray-500">
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-700">
                                {{ $article->category->name }}
                            </span>
                                <span>{{ $article->published_at ? $article->published_at->diffForHumans() : $article->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                    @if(!$loop->last)
                        <hr class="border-gray-200">
                    @endif
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-600">Chưa có bài viết nào</p>
        @endif
    </div>

    <!-- Valuation Forum Section -->
    @if(isset($valuationArticles) && $valuationArticles->count() > 0)
        <!-- Advertisement Placeholder (optional) -->
        <div class="mb-4 lg:mb-8">
            <div class="bg-gray-200 rounded-lg p-8 text-center text-gray-500 aspect-5/2 lg:aspect-square flex justify-center items-center">
                <div>
                    <p class="text-sm">Quảng cáo</p>
                </div>
            </div>
        </div>

        <div class="mb-4 lg:mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b-2 border-teal-600">
                <a href="{{ route('category.show', 'tham-dinh-gia') }}" class="hover:text-teal-600 transition-colors">
                    Diễn đàn Thẩm định giá
                </a>
            </h3>

            <div class="space-y-4">
                @foreach($valuationArticles->take(3) as $article)
                    <x-article-card :article="$article" layout="small" />

                    @if(!$loop->last)
                        <hr class="border-gray-200">
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <!-- Business Bridge Section -->
    @if(isset($businessArticles) && $businessArticles->count() > 0)
        <!-- Advertisement Placeholder (optional) -->
        <div class="mb-4 lg:mb-8">
            <div class="bg-gray-200 rounded-lg p-8 text-center text-gray-500 aspect-5/2 lg:aspect-square flex justify-center items-center">
                <div>
                    <p class="text-sm">Quảng cáo</p>
                </div>
            </div>
        </div>

        <div class="mb-4 lg:mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b-2 border-teal-600">
                <a href="{{ route('category.show', 'kinh-doanh') }}" class="hover:text-teal-600 transition-colors">
                    Nhịp cầu doanh nghiệp
                </a>
            </h3>

            <div class="space-y-4">
                @foreach($businessArticles->take(3) as $article)
                    <x-article-card :article="$article" layout="small" />

                    @if(!$loop->last)
                        <hr class="border-gray-200">
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <!-- Special Publications Section -->
    @if(isset($specialPublications) && $specialPublications->count() > 0)
        <!-- Advertisement Placeholder (optional) -->
        <div class="mb-4 lg:mb-8">
            <div class="bg-gray-200 rounded-lg p-8 text-center text-gray-500 aspect-5/2 lg:aspect-square flex justify-center items-center">
                <div>
                    <p class="text-sm">Quảng cáo</p>
                </div>
            </div>
        </div>

        <div class="mb-4 lg:mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b-2 border-teal-600">
                Đặc sản
            </h3>

            <div class="space-y-4">
                @foreach($specialPublications->take(2) as $article)
                    <x-article-card :article="$article" layout="small" />

                    @if(!$loop->last)
                        <hr class="border-gray-200">
                    @endif
                @endforeach
            </div>
        </div>
    @endif

</div>

@props(['article', 'comments', 'sort' => 'time'])

<div class="comments-section">
    <!-- Comment Count & Sort -->
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold">
            Bình luận (<span class="comment-count">{{ $comments->count() }}</span>)
        </h3>

        <!-- Sort Dropdown -->
        <div class="relative">
            <select id="comment-sort"
                    class="px-4 py-2 border border-gray-300 rounded-lg bg-white text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                <option value="time" {{ $sort === 'time' ? 'selected' : '' }}>Thời gian</option>
                <option value="likes" {{ $sort === 'likes' ? 'selected' : '' }}>Số người thích</option>
            </select>
        </div>
    </div>

    <!-- Comments List -->
    <div class="comments-list space-y-6">
        @forelse($comments as $comment)
            <div class="comment-item" data-comment-id="{{ $comment->id }}">
                @include('articles.partials.comment-single', ['comment' => $comment, 'article' => $article])
            </div>
        @empty
            <p class="text-gray-500 text-center py-8">Chưa có bình luận nào. Hãy là người đầu tiên bình luận!</p>
        @endforelse
    </div>
</div>

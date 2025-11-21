@props(['comment', 'article', 'isReply' => false])

@php
    $userId = auth()->id();
    $ipAddress = request()->ip();
    $isLiked = $comment->isLikedBy($userId, $ipAddress);
    $canEdit = $comment->canEdit($userId, $ipAddress);
    $canDelete = $comment->canDelete($userId, $ipAddress);
@endphp

<div class="comment {{ $isReply ? 'ml-12 mt-4' : '' }}" data-comment-id="{{ $comment->id }}">
    <div class="flex gap-4">
        <!-- Avatar Placeholder -->
        <div class="flex-shrink-0">
            <div class="w-10 h-10 rounded-full bg-teal-600 text-white flex items-center justify-center font-semibold text-lg">
                {{ strtoupper(substr($comment->author_name, 0, 1)) }}
            </div>
        </div>

        <!-- Comment Content -->
        <div class="flex-1">
            <!-- Author & Timestamp -->
            <div class="flex items-center gap-2 mb-2">
                <span class="font-semibold text-gray-900">{{ $comment->author_name }}</span>
                <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                @if($comment->created_at != $comment->updated_at)
                    <span class="text-xs text-gray-400 italic">(đã chỉnh sửa)</span>
                @endif
            </div>

            <!-- Comment Text (View Mode) -->
            <div class="comment-content mb-3">
                <p class="text-gray-700">{{ $comment->content }}</p>
            </div>

            <!-- Edit Form (Hidden by default) -->
            <div class="comment-edit-form hidden mb-3">
                <form class="edit-comment-form" data-comment-id="{{ $comment->id }}">
                    @csrf
                    @method('PATCH')
                    <textarea
                        name="content"
                        rows="3"
                        maxlength="1000"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 resize-none mb-2">{{ $comment->content }}</textarea>
                    <div class="flex gap-2">
                        <button type="submit"
                                class="px-4 py-1 bg-teal-600 text-white text-sm rounded hover:bg-teal-700">
                            Lưu
                        </button>
                        <button type="button"
                                class="cancel-edit px-4 py-1 bg-gray-300 text-gray-700 text-sm rounded hover:bg-gray-400">
                            Hủy
                        </button>
                    </div>
                    <div class="error-message text-red-600 text-sm mt-2"></div>
                </form>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-4 text-sm">
                <!-- Like Button -->
                <button class="like-button flex items-center gap-1 {{ $isLiked ? 'text-red-600' : 'text-gray-600' }} hover:text-red-600 transition-colors"
                        data-comment-id="{{ $comment->id }}">
                    <svg class="w-4 h-4 {{ $isLiked ? 'fill-current' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    <span class="likes-count">{{ $comment->likes_count }}</span>
                </button>

                <!-- Reply Button (not for nested replies) -->
                @if(!$isReply)
                    <button class="reply-button text-gray-600 hover:text-teal-600 transition-colors"
                            data-comment-id="{{ $comment->id }}">
                        Trả lời
                    </button>
                @endif

                <!-- Edit Button (if owner and within time window) -->
                @if($canEdit)
                    <button class="edit-button text-gray-600 hover:text-teal-600 transition-colors"
                            data-comment-id="{{ $comment->id }}">
                        Chỉnh sửa
                    </button>
                @endif

                <!-- Delete Button (if owner and within time window) -->
                @if($canDelete)
                    <button class="delete-button text-gray-600 hover:text-red-600 transition-colors"
                            data-comment-id="{{ $comment->id }}">
                        Xóa
                    </button>
                @endif
            </div>

            <!-- Reply Form Container (Hidden by default) -->
            @if(!$isReply)
                <div class="reply-form-container hidden mt-4">
                    @include('articles.partials.comment-form', ['article' => $article, 'parentId' => $comment->id])
                </div>
            @endif

            <!-- Nested Replies -->
            @if($comment->replies->count() > 0)
                <div class="replies mt-4 space-y-4">
                    @foreach($comment->replies as $reply)
                        @include('articles.partials.comment-single', ['comment' => $reply, 'article' => $article, 'isReply' => true])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

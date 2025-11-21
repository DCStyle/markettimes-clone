@props(['article', 'parentId' => null])

<form class="comment-form mb-6"
      data-article-id="{{ $article->id }}"
      data-parent-id="{{ $parentId }}">
    @csrf

    <input type="hidden" name="parent_id" value="{{ $parentId }}">

    @guest
        <!-- Guest Name Field -->
        <div class="mb-4">
            <label for="guest_name_{{ $parentId ?? 'main' }}" class="block text-sm font-semibold text-gray-700 mb-2">
                Tên của bạn <span class="text-red-600">*</span>
            </label>
            <input type="text"
                   id="guest_name_{{ $parentId ?? 'main' }}"
                   name="guest_name"
                   required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                   placeholder="Nhập tên của bạn">
            <span class="text-red-600 text-sm error-message" data-field="guest_name"></span>
        </div>

        <!-- Guest Email Field (Optional) -->
        <div class="mb-4">
            <label for="guest_email_{{ $parentId ?? 'main' }}" class="block text-sm font-semibold text-gray-700 mb-2">
                Email (không bắt buộc)
            </label>
            <input type="email"
                   id="guest_email_{{ $parentId ?? 'main' }}"
                   name="guest_email"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                   placeholder="email@example.com">
            <span class="text-red-600 text-sm error-message" data-field="guest_email"></span>
        </div>
    @endguest

    <!-- Comment Content -->
    <div class="mb-4">
        <label for="content_{{ $parentId ?? 'main' }}" class="block text-sm font-semibold text-gray-700 mb-2">
            Bình luận <span class="text-red-600">*</span>
        </label>
        <textarea
            id="content_{{ $parentId ?? 'main' }}"
            name="content"
            rows="4"
            required
            maxlength="1000"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 resize-none"
            placeholder="Nhập bình luận của bạn..."></textarea>
        <div class="flex justify-between items-center mt-2">
            <span class="text-red-600 text-sm error-message" data-field="content"></span>
            <span class="text-xs text-gray-500 character-counter">
                <span class="current-count">0</span>/1000
            </span>
        </div>
    </div>

    <!-- Moderation Notice -->
    <p class="text-sm text-gray-600 mb-4 p-3 bg-yellow-50 rounded border border-yellow-200">
        <strong>Lưu ý:</strong> Bình luận của bạn sẽ được kiểm duyệt trước khi hiển thị. Ban biên tập giữ quyền biên tập nội dung bình luận để phù hợp với qui định nội dung.
    </p>

    <!-- Buttons -->
    <div class="flex gap-3">
        <button type="submit"
                class="submit-button px-6 py-2 bg-teal-600 text-white font-semibold rounded-lg hover:bg-teal-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
            Gửi bình luận
        </button>

        @if($parentId)
            <button type="button"
                    class="cancel-reply px-6 py-2 bg-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-400 transition-colors">
                Hủy
            </button>
        @endif
    </div>

    <!-- Success/Error Messages -->
    <div class="mt-4">
        <div class="success-message hidden p-3 bg-green-50 border border-green-200 text-green-700 rounded"></div>
        <div class="error-message-global hidden p-3 bg-red-50 border border-red-200 text-red-700 rounded"></div>
    </div>
</form>

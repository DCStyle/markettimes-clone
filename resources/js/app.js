import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// AJAX Load More for Category Pages
document.addEventListener('DOMContentLoaded', function() {
    const loadMoreBtn = document.getElementById('load-more-btn');
    const loadingSpinner = document.getElementById('loading-spinner');
    const articlesContainer = document.getElementById('articles-container');

    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            const page = parseInt(this.dataset.page);
            const categorySlug = this.dataset.category;

            // Show loading spinner, hide button
            loadMoreBtn.classList.add('hidden');
            if (loadingSpinner) {
                loadingSpinner.classList.remove('hidden');
            }

            // Make AJAX request
            fetch(`/${categorySlug}?page=${page}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.html) {
                    // Create temporary container to parse HTML
                    const temp = document.createElement('div');
                    temp.innerHTML = data.html;

                    // Append each article to the container
                    const articles = temp.querySelectorAll('article');
                    articles.forEach(article => {
                        articlesContainer.appendChild(article);
                    });

                    // Update button state
                    if (data.next_page) {
                        loadMoreBtn.dataset.page = data.next_page;
                        loadMoreBtn.classList.remove('hidden');
                    }
                } else {
                    // No more articles, hide button
                    loadMoreBtn.remove();
                }

                // Hide loading spinner
                if (loadingSpinner) {
                    loadingSpinner.classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Error loading more articles:', error);
                loadMoreBtn.classList.remove('hidden');
                if (loadingSpinner) {
                    loadingSpinner.classList.add('hidden');
                }
                alert('Không thể tải thêm bài viết. Vui lòng thử lại.');
            });
        });
    }

    // ============================================
    // COMMENT SYSTEM FUNCTIONALITY
    // ============================================

    // Helper function to get CSRF token
    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content;
    }

    // Character counter for comment textareas
    document.querySelectorAll('.comment-form textarea[name="content"]').forEach(textarea => {
        const counter = textarea.closest('.comment-form').querySelector('.character-counter .current-count');
        if (counter) {
            textarea.addEventListener('input', function() {
                counter.textContent = this.value.length;
            });
        }
    });

    // Submit comment (main or reply)
    document.querySelectorAll('.comment-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const submitButton = this.querySelector('button[type="submit"]');
            const articleId = this.dataset.articleId;
            const parentId = this.dataset.parentId || null;
            const formData = new FormData(this);

            // Clear previous error messages
            this.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            this.querySelector('.success-message')?.classList.add('hidden');
            this.querySelector('.error-message-global')?.classList.add('hidden');

            // Disable submit button
            submitButton.disabled = true;
            submitButton.textContent = 'Đang gửi...';

            try {
                const response = await fetch(`/articles/${articleId}/comments`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Show success message
                    const successDiv = this.querySelector('.success-message');
                    if (successDiv) {
                        successDiv.textContent = data.message;
                        successDiv.classList.remove('hidden');
                    }

                    // Reset form
                    this.reset();
                    this.querySelector('.character-counter .current-count').textContent = '0';

                    // Hide reply form if it was a reply
                    if (parentId) {
                        this.closest('.reply-form-container').classList.add('hidden');
                    }
                } else {
                    // Handle validation errors
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            const errorSpan = this.querySelector(`.error-message[data-field="${field}"]`);
                            if (errorSpan) {
                                errorSpan.textContent = data.errors[field][0];
                            }
                        });
                    } else {
                        const errorDiv = this.querySelector('.error-message-global');
                        if (errorDiv) {
                            errorDiv.textContent = data.message || 'Có lỗi xảy ra. Vui lòng thử lại.';
                            errorDiv.classList.remove('hidden');
                        }
                    }
                }
            } catch (error) {
                console.error('Error submitting comment:', error);
                const errorDiv = this.querySelector('.error-message-global');
                if (errorDiv) {
                    errorDiv.textContent = 'Có lỗi xảy ra. Vui lòng thử lại.';
                    errorDiv.classList.remove('hidden');
                }
            } finally {
                // Re-enable submit button
                submitButton.disabled = false;
                submitButton.textContent = 'Gửi bình luận';
            }
        });
    });

    // Reply button click
    document.querySelectorAll('.reply-button').forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.dataset.commentId;
            const comment = this.closest('.comment');
            const replyFormContainer = comment.querySelector('.reply-form-container');

            if (replyFormContainer) {
                // Toggle reply form
                const isHidden = replyFormContainer.classList.contains('hidden');

                // Hide all other reply forms first
                document.querySelectorAll('.reply-form-container').forEach(container => {
                    container.classList.add('hidden');
                });

                if (isHidden) {
                    replyFormContainer.classList.remove('hidden');
                    replyFormContainer.querySelector('textarea').focus();
                }
            }
        });
    });

    // Cancel reply button
    document.querySelectorAll('.cancel-reply').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.reply-form-container').classList.add('hidden');
        });
    });

    // Like button click
    document.querySelectorAll('.like-button').forEach(button => {
        button.addEventListener('click', async function() {
            const commentId = this.dataset.commentId;
            const likesCountSpan = this.querySelector('.likes-count');
            const heartIcon = this.querySelector('svg');

            try {
                const response = await fetch(`/comments/${commentId}/like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Update likes count
                    likesCountSpan.textContent = data.likes_count;

                    // Toggle like styling
                    if (data.action === 'liked') {
                        this.classList.add('text-red-600');
                        this.classList.remove('text-gray-600');
                        heartIcon.classList.add('fill-current');
                    } else {
                        this.classList.remove('text-red-600');
                        this.classList.add('text-gray-600');
                        heartIcon.classList.remove('fill-current');
                    }
                }
            } catch (error) {
                console.error('Error liking comment:', error);
            }
        });
    });

    // Edit button click
    document.querySelectorAll('.edit-button').forEach(button => {
        button.addEventListener('click', function() {
            const comment = this.closest('.comment');
            const contentDiv = comment.querySelector('.comment-content');
            const editForm = comment.querySelector('.comment-edit-form');

            // Toggle edit mode
            contentDiv.classList.add('hidden');
            editForm.classList.remove('hidden');
            editForm.querySelector('textarea').focus();
        });
    });

    // Cancel edit button
    document.querySelectorAll('.cancel-edit').forEach(button => {
        button.addEventListener('click', function() {
            const comment = this.closest('.comment');
            const contentDiv = comment.querySelector('.comment-content');
            const editForm = comment.querySelector('.comment-edit-form');

            // Exit edit mode
            contentDiv.classList.remove('hidden');
            editForm.classList.add('hidden');
        });
    });

    // Submit edit form
    document.querySelectorAll('.edit-comment-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const commentId = this.dataset.commentId;
            const formData = new FormData(this);
            const errorDiv = this.querySelector('.error-message');
            const comment = this.closest('.comment');
            const contentDiv = comment.querySelector('.comment-content p');

            errorDiv.textContent = '';

            try {
                const response = await fetch(`/comments/${commentId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Update comment content
                    contentDiv.textContent = this.querySelector('textarea').value;

                    // Exit edit mode
                    comment.querySelector('.comment-content').classList.remove('hidden');
                    comment.querySelector('.comment-edit-form').classList.add('hidden');

                    // Show success message (optional)
                    alert(data.message);
                } else {
                    errorDiv.textContent = data.message || 'Có lỗi xảy ra. Vui lòng thử lại.';
                }
            } catch (error) {
                console.error('Error updating comment:', error);
                errorDiv.textContent = 'Có lỗi xảy ra. Vui lòng thử lại.';
            }
        });
    });

    // Delete button click
    document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', async function() {
            if (!confirm('Bạn có chắc chắn muốn xóa bình luận này?')) {
                return;
            }

            const commentId = this.dataset.commentId;
            const commentElement = this.closest('.comment-item') || this.closest('.comment');

            try {
                const response = await fetch(`/comments/${commentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Remove comment from DOM
                    commentElement.remove();

                    // Update comment count
                    const countSpan = document.querySelector('.comment-count');
                    if (countSpan) {
                        const currentCount = parseInt(countSpan.textContent);
                        countSpan.textContent = currentCount - 1;
                    }
                } else {
                    alert(data.message || 'Không thể xóa bình luận.');
                }
            } catch (error) {
                console.error('Error deleting comment:', error);
                alert('Có lỗi xảy ra. Vui lòng thử lại.');
            }
        });
    });

    // Comment sort dropdown
    const sortDropdown = document.getElementById('comment-sort');
    if (sortDropdown) {
        sortDropdown.addEventListener('change', function() {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('sort', this.value);
            window.location.href = currentUrl.toString();
        });
    }

    // ============================================
    // SEARCH: AJAX INFINITE SCROLL / LOAD MORE
    // ============================================

    const searchLoadMoreBtn = document.getElementById('load-more-btn');
    const searchLoadingSpinner = document.getElementById('loading-spinner');
    const searchResultsContainer = document.getElementById('search-results-container');

    if (searchLoadMoreBtn) {
        searchLoadMoreBtn.addEventListener('click', function() {
            const page = parseInt(this.dataset.page);
            const query = this.dataset.query;

            // Show loading spinner, hide button
            searchLoadMoreBtn.classList.add('hidden');
            if (searchLoadingSpinner) {
                searchLoadingSpinner.classList.remove('hidden');
            }

            // Make AJAX request
            fetch(`/search?q=${encodeURIComponent(query)}&page=${page}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.html) {
                    // Create temporary container to parse HTML
                    const temp = document.createElement('div');
                    temp.innerHTML = data.html;

                    // Append each article to the container
                    const articles = temp.children;
                    Array.from(articles).forEach(article => {
                        searchResultsContainer.appendChild(article);
                    });

                    // Update button state
                    if (data.next_page) {
                        searchLoadMoreBtn.dataset.page = data.next_page;
                        searchLoadMoreBtn.classList.remove('hidden');
                    }
                } else {
                    // No more results, hide button
                    searchLoadMoreBtn.remove();
                }

                // Hide loading spinner
                if (searchLoadingSpinner) {
                    searchLoadingSpinner.classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Error loading more search results:', error);
                searchLoadMoreBtn.classList.remove('hidden');
                if (searchLoadingSpinner) {
                    searchLoadingSpinner.classList.add('hidden');
                }
                alert('Không thể tải thêm kết quả. Vui lòng thử lại.');
            });
        });
    }

    // ============================================
    // STICKY NAVIGATION & HEADER ON SCROLL
    // ============================================

    // Desktop: Sticky Navigation (.c-menu-outer)
    // Mobile: Sticky Header (#main-header)

    const desktopNav = document.querySelector('.c-menu-outer');
    const mobileHeader = document.getElementById('main-header');

    let lastScrollTop = 0;
    let ticking = false;

    // Get element offset from top
    function getElementOffsetTop(element) {
        if (!element) return 0;
        const rect = element.getBoundingClientRect();
        return rect.top + window.pageYOffset;
    }

    // Initial offset positions
    const desktopNavOffset = getElementOffsetTop(desktopNav);
    const mobileHeaderOffset = getElementOffsetTop(mobileHeader);

    function handleScroll() {
        const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
        const windowWidth = window.innerWidth;

        // Desktop sticky navigation (>= 992px)
        if (windowWidth >= 992 && desktopNav) {
            if (currentScroll > desktopNavOffset) {
                // User has scrolled past the navigation
                desktopNav.classList.add('is-sticky');

                if (currentScroll > lastScrollTop) {
                    // Scrolling down - show navigation
                    desktopNav.classList.remove('is-hidden');
                } else {
                    // Scrolling up - hide navigation
                    desktopNav.classList.add('is-hidden');
                }
            } else {
                // User is above the navigation - remove sticky
                desktopNav.classList.remove('is-sticky', 'is-hidden');
            }
        } else if (windowWidth >= 992 && desktopNav) {
            // Clean up if window was resized
            desktopNav.classList.remove('is-sticky', 'is-hidden');
        }

        // Mobile sticky header (< 992px)
        if (windowWidth < 992 && mobileHeader) {
            if (currentScroll > mobileHeaderOffset) {
                // User has scrolled past the header
                mobileHeader.classList.add('is-sticky');

                if (currentScroll > lastScrollTop) {
                    // Scrolling down - show header
                    mobileHeader.classList.remove('is-hidden');
                } else {
                    // Scrolling up - hide header
                    mobileHeader.classList.add('is-hidden');
                }
            } else {
                // User is above the header - remove sticky
                mobileHeader.classList.remove('is-sticky', 'is-hidden');
            }
        } else if (windowWidth < 992 && mobileHeader) {
            // Clean up if window was resized
            mobileHeader.classList.remove('is-sticky', 'is-hidden');
        }

        lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
        ticking = false;
    }

    // Throttle scroll events with requestAnimationFrame
    function requestTick() {
        if (!ticking) {
            window.requestAnimationFrame(handleScroll);
            ticking = true;
        }
    }

    // Listen to scroll events
    window.addEventListener('scroll', requestTick, { passive: true });

    // Handle window resize to recalculate or clean up
    window.addEventListener('resize', function() {
        const windowWidth = window.innerWidth;

        // Clean up classes when resizing between breakpoints
        if (windowWidth >= 992 && mobileHeader) {
            mobileHeader.classList.remove('is-sticky', 'is-hidden');
        }
        if (windowWidth < 992 && desktopNav) {
            desktopNav.classList.remove('is-sticky', 'is-hidden');
        }
    });

    // ============================================
    // HOME PAGE: LATEST ARTICLES LOAD MORE
    // ============================================

    const homeLoadMoreBtn = document.getElementById('load-more-btn-home');
    const homeLoadingSpinner = document.getElementById('loading-spinner-home');
    const homeArticlesContainer = document.getElementById('latest-articles-container');

    if (homeLoadMoreBtn) {
        homeLoadMoreBtn.addEventListener('click', function() {
            const page = parseInt(this.dataset.page);

            // Show loading spinner, hide button
            homeLoadMoreBtn.classList.add('hidden');
            if (homeLoadingSpinner) {
                homeLoadingSpinner.classList.remove('hidden');
            }

            // Make AJAX request to home page with page parameter
            fetch(`/?page=${page}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.html) {
                    // Create temporary container to parse HTML
                    const temp = document.createElement('div');
                    temp.innerHTML = data.html;

                    // Append each article to the container
                    const articles = temp.children;
                    Array.from(articles).forEach(article => {
                        homeArticlesContainer.appendChild(article);
                    });

                    // Update button state
                    if (data.next_page) {
                        homeLoadMoreBtn.dataset.page = data.next_page;
                        homeLoadMoreBtn.classList.remove('hidden');
                    } else {
                        // No more articles, remove button
                        homeLoadMoreBtn.remove();
                    }
                } else {
                    // No more articles, remove button
                    homeLoadMoreBtn.remove();
                }

                // Hide loading spinner
                if (homeLoadingSpinner) {
                    homeLoadingSpinner.classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Error loading more articles:', error);
                homeLoadMoreBtn.classList.remove('hidden');
                if (homeLoadingSpinner) {
                    homeLoadingSpinner.classList.add('hidden');
                }
                alert('Không thể tải thêm bài viết. Vui lòng thử lại.');
            });
        });
    }
});

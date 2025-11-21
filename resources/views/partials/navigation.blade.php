@php
    // Helper function to check if a navigation item is active
    $isItemActive = function($item) use ($currentCategory) {
        // Check if item matches current category
        if ($item->type === 'category' && isset($currentCategory) && $currentCategory) {
            if ($item->category_id == $currentCategory->id) {
                return true;
            }
        }

        // Fallback to URL matching for other types
        return request()->is(ltrim($item->url, '/'));
    };
@endphp

<nav class="c-menu-outer bg-black bg-opacity-5">
    <div class="max-w-7xl mx-auto">
        <!-- Desktop Navigation -->
        <div class="hidden lg:block">
            <div class="c-menu">
                @foreach($navigationItems as $item)
                    @if($item->type === 'divider')
                        <!-- Divider -->
                        <span class="c-menu__divider {{ $item->css_classes }}"></span>
                    @else
                        @if($item->hasChildren())
                            <!-- Parent item with dropdown -->
                            <div class="c-menu__item c-menu__item--dropdown">
                                <a href="{{ $item->url }}"
                                   class="{{ $isItemActive($item) ? 'font-semibold' : '' }}"
                                   {{ $item->open_in_new_tab ? 'target="_blank" rel="noopener noreferrer"' : '' }}>
                                    {{ $item->label }}
                                    <svg class="c-menu__dropdown-icon" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </a>

                                <!-- Dropdown menu -->
                                <div class="c-menu__dropdown">
                                    @foreach($item->children as $child)
                                        @if($child->type !== 'divider')
                                            <a href="{{ $child->url }}"
                                               class="c-menu__dropdown-item {{ $isItemActive($child) ? 'active' : '' }}"
                                               {{ $child->open_in_new_tab ? 'target="_blank" rel="noopener noreferrer"' : '' }}>
                                                {{ $child->label }}
                                            </a>
                                        @else
                                            <div class="c-menu__dropdown-divider"></div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <!-- Single link item -->
                            <a href="{{ $item->url }}"
                               class="{{ $isItemActive($item) ? 'font-semibold' : '' }}"
                               {{ $item->open_in_new_tab ? 'target="_blank" rel="noopener noreferrer"' : '' }}>
                                {{ $item->label }}
                            </a>
                        @endif
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</nav>

<!-- Mobile Menu Overlay -->
<div id="mobile-menu-overlay" class="fixed inset-0 bg-white z-50 hidden lg:hidden overflow-y-auto">
    <div class="px-4 py-4">
        <!-- Mobile Menu Header -->
        <div class="flex items-center justify-between mb-6">
            <button id="mobile-menu-close" class="text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <a href="{{ route('home') }}">
                @if(setting('site_logo'))
                    <img src="{{ Storage::url(setting('site_logo')) }}" alt="{{ setting('site_name', config('app.name')) }}" class="c-logo">
                @else
                    <img src="{{ asset('images/logo.svg') }}" alt="{{ setting('site_name', config('app.name')) }}" class="c-logo">
                @endif
            </a>
            <button class="text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>
        </div>

        <!-- Mobile Menu Items -->
        <div class="space-y-0">
            @foreach($navigationItems as $item)
                @if($item->type !== 'divider')
                    @if($item->hasChildren())
                        <!-- Mobile Parent with Children -->
                        <div class="mobile-menu-parent">
                            <a href="{{ $item->url }}"
                               class="block px-4 py-4 text-gray-700 text-lg border-b border-gray-200 {{ $isItemActive($item) ? 'font-semibold text-primary' : '' }}"
                               {{ $item->open_in_new_tab ? 'target="_blank" rel="noopener noreferrer"' : '' }}>
                                {{ $item->label }}
                            </a>
                            <!-- Mobile Submenu -->
                            <div class="mobile-submenu pl-4">
                                @foreach($item->children as $child)
                                    @if($child->type !== 'divider')
                                        <a href="{{ $child->url }}"
                                           class="block px-4 py-3 text-gray-600 border-b border-gray-100 {{ $isItemActive($child) ? 'font-semibold text-primary' : '' }}"
                                           {{ $child->open_in_new_tab ? 'target="_blank" rel="noopener noreferrer"' : '' }}>
                                            {{ $child->label }}
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @else
                        <!-- Mobile Single Link -->
                        <a href="{{ $item->url }}"
                           class="block px-4 py-4 text-gray-700 text-lg border-b border-gray-200 {{ $isItemActive($item) ? 'font-semibold text-primary' : '' }}"
                           {{ $item->open_in_new_tab ? 'target="_blank" rel="noopener noreferrer"' : '' }}>
                            {{ $item->label }}
                        </a>
                    @endif
                @endif
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
        const mobileMenuClose = document.getElementById('mobile-menu-close');
        const hamburgerIcon = document.getElementById('hamburger-icon');
        const closeIcon = document.getElementById('close-icon');
        const searchToggle = document.getElementById('search-toggle');
        const searchOverlay = document.getElementById('search-overlay');
        const searchClose = document.getElementById('search-close');

        // Mobile menu toggle
        if (mobileMenuToggle && mobileMenuOverlay) {
            mobileMenuToggle.addEventListener('click', function() {
                mobileMenuOverlay.classList.toggle('hidden');
                hamburgerIcon.classList.toggle('hidden');
                closeIcon.classList.toggle('hidden');
                document.body.style.overflow = mobileMenuOverlay.classList.contains('hidden') ? '' : 'hidden';
            });
        }

        // Mobile menu close
        if (mobileMenuClose && mobileMenuOverlay) {
            mobileMenuClose.addEventListener('click', function() {
                mobileMenuOverlay.classList.add('hidden');
                hamburgerIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
                document.body.style.overflow = '';
            });
        }

        // Search toggle (mobile)
        if (searchToggle && searchOverlay) {
            searchToggle.addEventListener('click', function() {
                // On mobile, show search overlay
                if (window.innerWidth < 1024) {
                    searchOverlay.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                } else {
                    // On desktop, navigate to search page
                    window.location.href = "{{ route('search') }}";
                }
            });
        }

        // Search close
        if (searchClose && searchOverlay) {
            searchClose.addEventListener('click', function() {
                searchOverlay.classList.add('hidden');
                document.body.style.overflow = '';
            });
        }

        // Close overlays on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (mobileMenuOverlay && !mobileMenuOverlay.classList.contains('hidden')) {
                    mobileMenuOverlay.classList.add('hidden');
                    hamburgerIcon.classList.remove('hidden');
                    closeIcon.classList.add('hidden');
                    document.body.style.overflow = '';
                }
                if (searchOverlay && !searchOverlay.classList.contains('hidden')) {
                    searchOverlay.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            }
        });
    });
</script>
@endpush

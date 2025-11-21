# MarketTimes.vn Clone - Complete Implementation Plan

## Project Overview

**Objective**: Clone markettimes.vn - Vietnamese financial news portal with exact design and structure

**Tech Stack**:
- Laravel 12 (PHP 8.2+)
- Filament 3 (Admin Panel)
- Tailwind CSS
- Alpine.js
- Laravel Scout (Search)
- Intervention Image (Image Processing)

**Target Features**:
- Full CMS with admin panel
- User authentication & commenting system
- Full-text search with Vietnamese support
- SEO optimization with schema markup
- Responsive design matching original

**Demo**:
- Demo images: In folder `demo-images`
- Demo website: 
- [1] For homepage: https://markettimes.vn 
- [2] For category page: https://markettimes.vn/tieu-diem
- [3] For article page: https://markettimes.vn/thu-tuong-yeu-cau-cat-bo-nhung-thu-tuc-khong-can-thiet-ma-nguoi-dan-van-phai-lam-97072.html

---

## Website Analysis Summary

### Site Structure
- **Domain**: markettimes.vn
- **CMS**: Powered by OneCMS
- **Layout**: Traditional news portal (header → nav → hero → grid → footer)
- **URL Pattern**: `https://markettimes.vn/[slug]-[id].html`

### Main Navigation Categories
1. Trang chủ (Home)
2. Tiêu điểm (Highlights)
3. Thẩm định giá (Valuation)
4. Tài chính (Finance)
5. Kinh doanh (Business)
6. Thế giới (World)
7. Công nghệ (Technology)
8. Bất động sản (Real Estate)
9. Ngành hàng (Industry Sectors)

### Design Specifications

#### Layout Components
- **Header**: Logo + horizontal navigation menu
- **Breadcrumbs**: Home > Category format
- **Hero Section**: Large featured article (prominent image + headline)
- **Content Grid**: Article cards with 3:2 aspect ratio thumbnails
- **Sidebar**: "Đọc nhiều" (Most Read), related articles, ads
- **Footer**: Organization info, offices, contact details, copyright

#### Visual Design
- **Color Scheme**: Neutral palette (white backgrounds, gray accents, blue links, dark text)
- **Typography**: Sans-serif fonts, clear hierarchy
- **Images**: 540x360px thumbnails (3:2 aspect ratio)
- **Spacing**: Generous padding, card-based layout
- **Responsive**: Breakpoint at min-width: 992px

#### CSS Classes (Custom)
- `.b-grid__time` - Grid time display
- `.c-menu` - Menu components
- `.c-logo` - Logo styling
- `.c-menu-outer` - Outer menu wrapper
- Menu links: `padding: 0 7px` at 992px+

#### Components Identified
- Article cards (thumbnail + title + category + timestamp)
- Navigation menu (8 categories)
- Search functionality
- "Xem thêm" (View More/Load More) button
- Time-relative dating ("6 giờ trước" = 6 hours ago)
- Social sharing (Facebook, Twitter)
- Comment form with moderation
- Related articles module
- Tags display
- Breadcrumb navigation

### Content Structure

#### Homepage
- Hero featured article (large image + full headline)
- Secondary featured articles grid
- Category-specific sections
- Special sections:
  - "Diễn đàn Thẩm định giá" (Valuation Forum)
  - "Nhịp cầu doanh nghiệp" (Business Pulse)
  - "Đặc san" (Special Publications)

#### Category Pages
- Featured article at top
- Filtered article listing (by category)
- Infinite scroll / "Xem thêm" button
- "Đọc nhiều" (Most Read) sidebar
- Time-based sorting

#### Article Detail Pages
- **Header**: Title (H1), publication timestamp, category tag
- **Metadata**: Author name (Nguyễn Trang example), date/time, category
- **Social Sharing**: Facebook and Twitter buttons
- **Content**: Paragraphs, images with captions, quotes
- **Sidebar**: Related articles (5-6 with thumbnails), tags
- **Tags**: Article-specific tags (VN-Index, VIC, VJC, etc.)
- **Comments**: Submission form with moderation notice
- **Related Content**: "May interest you" section

#### Footer Content
- Publisher: "Cơ quan của Hội Thẩm định giá Việt Nam"
- Editorial leadership (Editor-in-Chief, Deputy Editors)
- Hanoi office address
- Ho Chi Minh City office address
- Contact: Phone + Email
- License: "535/GP-BTTTT dated 21/08/2021"
- Copyright: "© 2021 Toàn bộ bản quyền thuộc Nhịp sống thị trường"
- Technology: "POWERED BY ONECMS"

### Technical Features Observed

#### JavaScript Libraries
- jQuery ($ selector usage)
- Google Analytics (GA-0EM5ZN3YLF)
- Custom WebControl module (load-more functionality)

#### SEO Elements
- Schema.org markup: NewsArticle, BreadcrumbList
- Meta tags and descriptions
- Sitemap integration
- SEO-friendly URLs (slugs)

#### Performance
- Lazy-loading CSS
- Image optimization
- Dynamic content loading (AJAX for more articles)

---

## Database Design

### Tables & Relationships

#### 1. Categories Table
```
- id (primary key)
- name (string) - Category name in Vietnamese
- slug (string, unique) - URL-friendly name
- description (text, nullable)
- parent_id (foreign key, nullable) - For nested categories
- order (integer, default 0) - Display order
- is_active (boolean, default true)
- created_at, updated_at
```

**Relationships**:
- belongsTo: parent category (self-referencing)
- hasMany: child categories, articles

#### 2. Articles Table
```
- id (primary key)
- title (string) - Article headline
- slug (string, unique) - URL slug
- summary (text) - Short description/excerpt
- content (longtext) - Full article body (HTML)
- featured_image (string) - Image path
- author_id (foreign key) - Link to users
- category_id (foreign key) - Link to categories
- published_at (timestamp, nullable) - Schedule publishing
- view_count (integer, default 0) - Page views
- is_featured (boolean, default false) - Homepage hero
- is_published (boolean, default false)
- meta_title (string, nullable) - SEO
- meta_description (text, nullable) - SEO
- created_at, updated_at
```

**Relationships**:
- belongsTo: author (User), category
- belongsToMany: tags
- hasMany: comments
- morphMany: media (images)

#### 3. Tags Table
```
- id (primary key)
- name (string, unique) - Tag name
- slug (string, unique) - URL-friendly
- created_at, updated_at
```

**Pivot Table: article_tag**
```
- article_id (foreign key)
- tag_id (foreign key)
```

**Relationships**:
- belongsToMany: articles

#### 4. Users Table (Extended)
```
- id (primary key)
- name (string)
- email (string, unique)
- email_verified_at (timestamp, nullable)
- password (string)
- bio (text, nullable) - Author biography
- avatar (string, nullable) - Profile picture
- role (enum: admin, editor, author, user) - Permissions
- is_active (boolean, default true)
- remember_token
- created_at, updated_at
```

**Relationships**:
- hasMany: articles (as author), comments

#### 5. Comments Table
```
- id (primary key)
- article_id (foreign key) - Link to article
- user_id (foreign key) - Link to user
- content (text) - Comment text
- is_approved (boolean, default false) - Moderation
- parent_id (foreign key, nullable) - For nested replies
- created_at, updated_at
```

**Relationships**:
- belongsTo: article, user, parent comment
- hasMany: replies (child comments)

#### 6. Pages Table (Static Pages)
```
- id (primary key)
- title (string) - Page title
- slug (string, unique) - URL slug
- content (longtext) - Page content (HTML)
- is_published (boolean, default false)
- meta_title (string, nullable)
- meta_description (text, nullable)
- created_at, updated_at
```

---

## Implementation Phases

### Phase 1: Database & Models Setup

#### Step 1.1: Install Required Packages
```bash
# Filament Admin Panel
composer require filament/filament:"^3.0"

# Image Processing
composer require intervention/image

# Sluggable URLs
composer require spatie/laravel-sluggable

# SEO Helper
composer require spatie/laravel-sitemap

# Search
composer require laravel/scout
composer require teamtnt/laravel-scout-tntsearch-driver
```

#### Step 1.2: Create Migrations
```bash
php artisan make:migration create_categories_table
php artisan make:migration create_articles_table
php artisan make:migration create_tags_table
php artisan make:migration create_article_tag_table
php artisan make:migration create_comments_table
php artisan make:migration create_pages_table
php artisan make:migration add_author_fields_to_users_table
```

#### Step 1.3: Create Models
```bash
php artisan make:model Category
php artisan make:model Article
php artisan make:model Tag
php artisan make:model Comment
php artisan make:model Page
```

#### Step 1.4: Define Relationships
- Category: parent/children, articles
- Article: author, category, tags, comments
- Tag: articles (many-to-many)
- Comment: article, user, parent/replies
- User: articles (as author), comments

#### Step 1.5: Add Traits & Scopes
- Sluggable trait for SEO-friendly URLs
- SoftDeletes for articles and comments
- Scopes: published(), featured(), recent()
- Accessors for relative timestamps ("6 giờ trước")

### Phase 2: Filament Admin Panel

#### Step 2.1: Install Filament
```bash
php artisan filament:install --panels
php artisan make:filament-user
```

#### Step 2.2: Create Filament Resources
```bash
php artisan make:filament-resource Article
php artisan make:filament-resource Category
php artisan make:filament-resource Tag
php artisan make:filament-resource Comment
php artisan make:filament-resource Page
php artisan make:filament-resource User
```

#### Step 2.3: Configure Article Resource
- Rich text editor (TinyMCE or Tiptap) for content
- Image upload with preview (featured_image)
- Select dropdown for category
- Multi-select for tags
- Author auto-filled from logged-in user
- DateTime picker for publish scheduling
- Toggle for is_featured, is_published
- View count display (read-only)
- SEO fields (meta_title, meta_description)

#### Step 2.4: Configure Category Resource
- Tree/nested structure support
- Drag-drop ordering
- Toggle for is_active
- Slug auto-generation from name

#### Step 2.5: Configure Comment Resource
- Filter: approved, pending, spam
- Approve/reject actions
- Bulk actions
- Display article title and user name
- Quick reply functionality

#### Step 2.6: Create Dashboard Widgets
- Total articles count
- Comments pending approval count
- Most viewed articles (top 10)
- Recent articles
- Daily/weekly/monthly view stats chart

### Phase 3: Frontend Layout

#### Step 3.1: Create Blade Layout
**File**: `resources/views/layouts/app.blade.php`

Components:
- Header with logo
- Navigation menu (8 categories)
- Breadcrumbs
- Main content slot
- Footer with organization info

#### Step 3.2: Create Partials
```bash
resources/views/partials/
  - header.blade.php
  - navigation.blade.php
  - breadcrumbs.blade.php
  - footer.blade.php
  - article-card.blade.php
  - sidebar.blade.php
```

#### Step 3.3: Set Up Routes
```php
// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/{category:slug}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/{category:slug}/{article:slug}-{article:id}.html', [ArticleController::class, 'show'])->name('article.show');

// Comment routes (authenticated)
Route::post('/articles/{article}/comments', [CommentController::class, 'store'])->name('comments.store')->middleware('auth');
```

#### Step 3.4: Create Controllers
```bash
php artisan make:controller HomeController
php artisan make:controller CategoryController
php artisan make:controller ArticleController
php artisan make:controller CommentController
php artisan make:controller SearchController
```

### Phase 4: Homepage Implementation

#### Step 4.1: HomeController Logic
```php
public function index()
{
    return view('home', [
        'featuredArticle' => Article::published()->featured()->latest()->first(),
        'latestArticles' => Article::published()->latest()->take(12)->get(),
        'categories' => Category::with(['articles' => function($q) {
            $q->published()->latest()->take(4);
        }])->whereNull('parent_id')->active()->get(),
        'mostRead' => Article::published()->orderBy('view_count', 'desc')->take(5)->get(),
    ]);
}
```

#### Step 4.2: Homepage View
**File**: `resources/views/home.blade.php`

Structure:
- Hero section (featured article, large image)
- Latest articles grid (3-4 columns)
- Category sections
- Sidebar with "Đọc nhiều" (Most Read)
- Load more button (AJAX)

#### Step 4.3: Article Card Component
**File**: `resources/views/components/article-card.blade.php`

Display:
- Thumbnail (540x360px, 3:2 ratio)
- Category tag
- Title (linked)
- Summary excerpt
- Relative timestamp ("6 giờ trước")

### Phase 5: Category & Article Pages

#### Step 5.1: Category Page
**Controller**: CategoryController@show
- Paginate articles (12 per page)
- Filter by category
- Order by published_at DESC
- Breadcrumbs: Home > Category Name

**View**: `resources/views/categories/show.blade.php`
- Featured article at top
- Article grid
- Load more / pagination
- "Đọc nhiều" sidebar

#### Step 5.2: Article Detail Page
**Controller**: ArticleController@show
- Increment view_count
- Load article with author, category, tags
- Load approved comments (with pagination)
- Related articles (same category, exclude current)

**View**: `resources/views/articles/show.blade.php`
- Breadcrumbs: Home > Category > Article Title
- Article title (H1)
- Author, date, category, share buttons
- Featured image
- Article content (HTML)
- Tags list
- Related articles
- Comments section (if authenticated)
- Comment form

### Phase 6: Authentication & Comments

#### Step 6.1: Laravel Breeze (Already Installed)
```bash
# Already installed, just verify
php artisan route:list --name=login
```

#### Step 6.2: Comment Form
**File**: `resources/views/articles/partials/comment-form.blade.php`
- Textarea for comment content
- Submit button
- Notice: "Bình luận sẽ được kiểm duyệt trước khi hiển thị"

#### Step 6.3: Comment Display
**File**: `resources/views/articles/partials/comments.blade.php`
- List approved comments
- Display user name, timestamp
- Support nested replies (optional)

#### Step 6.4: CommentController
```php
public function store(Request $request, Article $article)
{
    $request->validate([
        'content' => 'required|string|max:1000',
    ]);

    $article->comments()->create([
        'user_id' => auth()->id(),
        'content' => $request->content,
        'is_approved' => false, // Require moderation
    ]);

    return back()->with('success', 'Bình luận của bạn đang chờ kiểm duyệt.');
}
```

### Phase 7: Search Functionality

#### Step 7.1: Configure Laravel Scout
```bash
php artisan vendor:publish --provider="Laravel\Scout\ScoutServiceProvider"
```

**Config**: `config/scout.php`
```php
'driver' => env('SCOUT_DRIVER', 'tntsearch'),
```

#### Step 7.2: Make Articles Searchable
**Model**: `app/Models/Article.php`
```php
use Laravel\Scout\Searchable;

class Article extends Model
{
    use Searchable;

    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'summary' => $this->summary,
            'content' => strip_tags($this->content),
        ];
    }
}
```

#### Step 7.3: Search Controller
```php
public function index(Request $request)
{
    $query = $request->input('q');

    $articles = Article::search($query)
        ->where('is_published', true)
        ->paginate(15);

    return view('search', compact('articles', 'query'));
}
```

#### Step 7.4: Search View
**File**: `resources/views/search.blade.php`
- Search input (pre-filled with query)
- Results count
- Article cards grid
- Pagination

### Phase 8: SEO Optimization

#### Step 8.1: Meta Tags Helper
**File**: `app/Helpers/SeoHelper.php`
```php
class SeoHelper
{
    public static function generateMeta($model)
    {
        return [
            'title' => $model->meta_title ?? $model->title,
            'description' => $model->meta_description ?? Str::limit(strip_tags($model->content ?? $model->summary), 160),
            'image' => $model->featured_image ?? asset('images/default-og.jpg'),
            'url' => url()->current(),
        ];
    }
}
```

#### Step 8.2: Article Schema Markup
**File**: `resources/views/articles/partials/schema.blade.php`
```php
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "NewsArticle",
  "headline": "{{ $article->title }}",
  "image": "{{ asset('storage/' . $article->featured_image) }}",
  "datePublished": "{{ $article->published_at->toIso8601String() }}",
  "author": {
    "@type": "Person",
    "name": "{{ $article->author->name }}"
  },
  "publisher": {
    "@type": "Organization",
    "name": "Nhịp sống thị trường",
    "logo": {
      "@type": "ImageObject",
      "url": "{{ asset('images/logo.png') }}"
    }
  }
}
</script>
```

#### Step 8.3: Breadcrumb Schema
```php
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "Trang chủ",
      "item": "{{ route('home') }}"
    },
    {
      "@type": "ListItem",
      "position": 2,
      "name": "{{ $article->category->name }}",
      "item": "{{ route('category.show', $article->category) }}"
    }
  ]
}
</script>
```

#### Step 8.4: Sitemap Generation
```bash
php artisan make:command GenerateSitemap
```

**Command**: Generate XML sitemap with all articles and categories

### Phase 9: CSS Styling (Exact Match)

#### Step 9.1: Tailwind Configuration
**File**: `tailwind.config.js`
```js
module.exports = {
  theme: {
    extend: {
      aspectRatio: {
        '3/2': '3 / 2', // For 540x360 images
      },
      screens: {
        'lg': '992px', // Match breakpoint
      },
    },
  },
}
```

#### Step 9.2: Custom CSS Classes
**File**: `resources/css/app.css`
```css
/* Custom classes matching markettimes.vn */
.b-grid__time {
  /* Grid time display styling */
}

.c-menu {
  /* Menu component styling */
}

.c-logo {
  /* Logo styling */
}

.c-menu-outer {
  /* Outer menu wrapper */
  background-color: #000;
}

.c-menu a {
  padding: 0 7px;
}

@media (min-width: 992px) {
  /* Desktop styles */
}

/* Article card styling */
.article-card {
  /* Card layout */
}

.article-thumbnail {
  aspect-ratio: 3/2;
  width: 100%;
  object-fit: cover;
}
```

#### Step 9.3: Typography & Colors
- Sans-serif font family
- White backgrounds for content
- Gray accents for meta info
- Blue links
- Dark text (#333 or similar)

#### Step 9.4: Responsive Grid
- Mobile: 1 column
- Tablet: 2 columns
- Desktop (992px+): 3-4 columns

### Phase 10: Content Seeding

#### Step 10.1: Create Seeders
```bash
php artisan make:seeder CategorySeeder
php artisan make:seeder ArticleSeeder
php artisan make:seeder TagSeeder
php artisan make:seeder UserSeeder
```

#### Step 10.2: CategorySeeder
Create 8 main categories:
1. Tiêu điểm (Highlights)
2. Thẩm định giá (Valuation)
3. Tài chính (Finance)
4. Kinh doanh (Business)
5. Thế giới (World)
6. Công nghệ (Technology)
7. Bất động sản (Real Estate)
8. Ngành hàng (Industry Sectors)

#### Step 10.3: ArticleSeeder
- 50+ articles with Vietnamese titles/content
- Mix of financial, business, tech news
- Assign to various categories
- Set random view counts
- Mark 1-2 as featured
- Use Faker for content generation

#### Step 10.4: TagSeeder
Create common tags:
- VN-Index, VIC, VJC, VPB, ACB
- Chứng khoán, Bất động sản
- Ngân hàng, Công nghệ
- Etc.

#### Step 10.5: Run Seeders
```bash
php artisan db:seed
```

### Phase 11: Additional Features

#### Step 11.1: View Counter
**Middleware**: Track article views with throttling (1 view per user per hour)

```php
// In ArticleController@show
if (!session()->has('viewed_article_' . $article->id)) {
    $article->increment('view_count');
    session()->put('viewed_article_' . $article->id, true);
}
```

#### Step 11.2: Relative Timestamps
**Helper**: Convert timestamps to Vietnamese relative format

```php
// Carbon already supports Vietnamese, just configure
Carbon::setLocale('vi');

// In Blade: {{ $article->published_at->diffForHumans() }}
// Output: "6 giờ trước"
```

#### Step 11.3: Image Optimization
- Resize on upload to 540x360 (thumbnails)
- Generate multiple sizes (thumbnail, medium, large)
- Use Intervention Image
- Lazy loading in frontend

#### Step 11.4: Load More (AJAX)
**File**: `resources/js/app.js`
```js
// Infinite scroll or "Xem thêm" button
// AJAX load next page of articles
// Append to grid
```

### Phase 12: Configuration & Localization

#### Step 12.1: Vietnamese Locale
**File**: `config/app.php`
```php
'locale' => 'vi',
'faker_locale' => 'vi_VN',
```

#### Step 12.2: Translation Files
**File**: `lang/vi/auth.php`, `lang/vi/validation.php`, etc.
- Translate all messages to Vietnamese
- Comment form labels
- Error messages
- Success notifications

#### Step 12.3: Date Formatting
Configure Carbon for Vietnamese:
```php
// In AppServiceProvider
Carbon::setLocale('vi');
```

### Phase 13: Testing & Optimization

#### Step 13.1: Test Checklist
- [ ] Article CRUD in admin panel
- [ ] Category CRUD in admin panel
- [ ] Comment moderation workflow
- [ ] User registration and login
- [ ] Comment submission (authenticated users)
- [ ] Search functionality with Vietnamese characters
- [ ] Article view counter incrementing
- [ ] Related articles display correctly
- [ ] Tags display and filtering
- [ ] Pagination on category pages
- [ ] Load more functionality
- [ ] Responsive design on mobile/tablet/desktop
- [ ] Image uploads and display
- [ ] SEO meta tags rendering
- [ ] Schema markup validation
- [ ] Sitemap generation

#### Step 13.2: Performance Optimization
- Query optimization (eager loading)
- Database indexing (slug, published_at, view_count)
- Cache frequently accessed data (categories, most read)
- Image optimization (WebP format)
- Minify CSS/JS assets
- Enable Laravel Telescope for debugging

#### Step 13.3: Security
- CSRF protection (already built-in)
- XSS protection (escape output)
- SQL injection protection (Eloquent ORM)
- Comment content sanitization
- Rate limiting on comment submission
- File upload validation (images only)

---

## File Structure

```
markettimes-clone/
├── app/
│   ├── Filament/
│   │   └── Resources/
│   │       ├── ArticleResource.php
│   │       ├── CategoryResource.php
│   │       ├── CommentResource.php
│   │       ├── TagResource.php
│   │       └── UserResource.php
│   ├── Http/
│   │   └── Controllers/
│   │       ├── HomeController.php
│   │       ├── CategoryController.php
│   │       ├── ArticleController.php
│   │       ├── CommentController.php
│   │       └── SearchController.php
│   ├── Models/
│   │   ├── Article.php
│   │   ├── Category.php
│   │   ├── Comment.php
│   │   ├── Tag.php
│   │   ├── Page.php
│   │   └── User.php
│   └── Helpers/
│       └── SeoHelper.php
├── database/
│   ├── migrations/
│   │   ├── 2024_create_categories_table.php
│   │   ├── 2024_create_articles_table.php
│   │   ├── 2024_create_tags_table.php
│   │   ├── 2024_create_article_tag_table.php
│   │   ├── 2024_create_comments_table.php
│   │   └── 2024_create_pages_table.php
│   └── seeders/
│       ├── CategorySeeder.php
│       ├── ArticleSeeder.php
│       ├── TagSeeder.php
│       └── UserSeeder.php
├── resources/
│   ├── css/
│   │   └── app.css (custom styles)
│   ├── js/
│   │   └── app.js (AJAX load more)
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php
│       ├── partials/
│       │   ├── header.blade.php
│       │   ├── navigation.blade.php
│       │   ├── breadcrumbs.blade.php
│       │   ├── footer.blade.php
│       │   └── sidebar.blade.php
│       ├── components/
│       │   └── article-card.blade.php
│       ├── home.blade.php
│       ├── search.blade.php
│       ├── categories/
│       │   └── show.blade.php
│       └── articles/
│           ├── show.blade.php
│           └── partials/
│               ├── comment-form.blade.php
│               ├── comments.blade.php
│               └── schema.blade.php
└── routes/
    └── web.php
```

---

## Quick Start Commands

```bash
# Install dependencies
composer install
npm install

# Configure environment
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Install Filament
composer require filament/filament:"^3.0"
php artisan filament:install --panels

# Create admin user
php artisan make:filament-user

# Install additional packages
composer require intervention/image
composer require spatie/laravel-sluggable
composer require spatie/laravel-sitemap
composer require laravel/scout
composer require teamtnt/laravel-scout-tntsearch-driver

# Build assets
npm run build

# Start development server
php artisan serve
```

---

## Admin Panel Access

- URL: `http://localhost:8000/admin`
- Create admin user: `php artisan make:filament-user`
- Manage articles, categories, tags, comments, users

---

## Expected Deliverables

1. **Functional CMS**
   - Filament admin panel with full CRUD
   - User roles: admin, editor, author, user
   - Rich text editor for article content
   - Image upload and management
   - Comment moderation system

2. **Public Frontend**
   - Homepage with featured articles
   - Category pages with filtering
   - Article detail pages
   - Search functionality
   - User authentication (login/register)
   - Comment submission

3. **Design Match**
   - Pixel-perfect replication of markettimes.vn
   - Responsive design (mobile, tablet, desktop)
   - 3:2 aspect ratio images
   - Custom CSS classes matching original, but as little as possible. Mostly we will use Tailwind CSS 3.
   - Vietnamese typography and spacing

4. **SEO Optimization**
   - Meta tags (title, description, OG tags)
   - Schema.org markup (NewsArticle, BreadcrumbList)
   - Sitemap generation
   - SEO-friendly URLs (slugs)

5. **Database**
   - 50+ seeded articles with Vietnamese content
   - 8 main categories
   - 20+ tags
   - Sample comments
   - Multiple test users

---

## Notes & Considerations

1. **Vietnamese Language Support**
   - Set locale to 'vi' in config/app.php
   - Use Carbon for relative timestamps
   - Ensure UTF-8 encoding in database
   - Test search with Vietnamese diacritics

2. **Image Handling**
   - Store images in `storage/app/public/images`
   - Symlink: `php artisan storage:link`
   - Generate thumbnails on upload
   - Use intervention/image package

3. **Performance**
   - Cache most read articles (1 hour)
   - Cache categories (until updated)
   - Eager load relationships to avoid N+1 queries
   - Index database columns (slug, published_at, view_count)

4. **Security**
   - Validate and sanitize all user input
   - Escape output in Blade templates
   - Rate limit comment submissions
   - Moderate comments before display
   - Validate image uploads (type, size)

5. **Future Enhancements** (Optional)
   - Newsletter subscription
   - RSS feed
   - Social media auto-posting
   - Analytics dashboard in admin
   - Multiple authors per article
   - Article versioning/revisions
   - Advanced search filters
   - Reading time estimator
   - Print-friendly version
   - Save/bookmark articles (for logged-in users)

---

## Resources & Documentation

- **Laravel 12**: https://laravel.com/docs/12.x
- **Filament 3**: https://filamentphp.com/docs/3.x
- **Tailwind CSS**: https://tailwindcss.com/docs
- **Laravel Scout**: https://laravel.com/docs/12.x/scout
- **Intervention Image**: http://image.intervention.io/
- **Spatie Packages**: https://spatie.be/open-source

---

**Plan Created**: November 20, 2025
**Target Website**: markettimes.vn
**Framework**: Laravel 12 + Filament 3
**Estimated Completion**: 2-3 weeks (full-time development)

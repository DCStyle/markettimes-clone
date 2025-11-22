<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\NavigationItem;
use App\Models\Page;
use App\Models\Tag;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportFromWordPress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:wordpress
                            {--wp-path= : Path to WordPress installation}
                            {--dry-run : Preview what would be imported without making changes}
                            {--skip-users : Skip user import}
                            {--skip-comments : Skip comment import}
                            {--limit= : Limit number of posts to import for testing}
                            {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import all data from WordPress database (x_investor) to Laravel application';

    /**
     * WordPress database connection
     *
     * @var \Illuminate\Database\Connection
     */
    protected $wpDb;

    /**
     * WordPress installation path
     *
     * @var string
     */
    protected $wpInstallPath;

    /**
     * Statistics for import summary
     *
     * @var array
     */
    protected $stats = [
        'users' => 0,
        'categories' => 0,
        'tags' => 0,
        'articles' => 0,
        'pages' => 0,
        'comments' => 0,
        'featured_images_processed' => 0,
        'featured_images_not_found' => 0,
        'featured_images_failed' => 0,
        'content_images_processed' => 0,
        'content_images_not_found' => 0,
        'content_images_failed' => 0,
        'errors' => 0,
    ];

    /**
     * Mapping arrays
     *
     * @var array
     */
    protected $userMap = [];
    protected $categoryMap = [];
    protected $tagMap = [];
    protected $articleMap = [];

    /**
     * Cache for processed content images (to avoid re-processing duplicates)
     *
     * @var array
     */
    protected $processedContentImages = [];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('===========================================');
        $this->info('WordPress to Laravel Migration Tool');
        $this->info('===========================================');
        $this->newLine();

        // Setup WordPress database connection
        if (!$this->setupWordPressConnection()) {
            $this->error('Failed to connect to WordPress database. Aborting.');
            return 1;
        }

        // Setup WordPress installation path
        $this->wpInstallPath = $this->option('wp-path') ?: getenv('HOME') . '/Documents/www/x-investor';

        // Verify WordPress installation exists
        if (!is_dir($this->wpInstallPath . '/wp-content/uploads')) {
            $this->error("WordPress installation not found at: {$this->wpInstallPath}");
            $this->error("Please specify correct path using --wp-path option");
            return 1;
        }

        $this->info("✓ Found WordPress installation at: {$this->wpInstallPath}");

        // Confirm before proceeding
        if (!$this->option('dry-run') && !$this->option('force')) {
            if (!$this->confirm('This will DELETE all existing data (articles, categories, tags, etc.) and replace it with WordPress data. Continue?', false)) {
                $this->warn('Import cancelled.');
                return 0;
            }
        }

        $this->newLine();
        $this->info('Starting import...');
        $this->newLine();

        try {
            // Clear existing data
            if (!$this->option('dry-run')) {
                $this->clearExistingData();
            } else {
                $this->info('[DRY RUN] Would clear existing data');
            }

            // Import in order
            if (!$this->option('skip-users')) {
                $this->importUsers();
            }

            $this->importCategories();
            $this->importTags();
            $this->importArticles();
            $this->importPages();

            if (!$this->option('skip-comments')) {
                $this->importComments();
            }

            // Post-import tasks
            if (!$this->option('dry-run')) {
                $this->postImportTasks();
            } else {
                $this->info('[DRY RUN] Would run post-import tasks');
            }

            // Display summary
            $this->displaySummary();

            $this->newLine();
            $this->info('✓ Import completed successfully!');

        } catch (\Exception $e) {
            $this->error('Import failed: ' . $e->getMessage());
            Log::error('WordPress Import Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }

        return 0;
    }

    /**
     * Setup connection to WordPress database
     */
    protected function setupWordPressConnection(): bool
    {
        try {
            config([
                'database.connections.wordpress' => [
                    'driver' => 'mysql',
                    'host' => env('DB_HOST', '127.0.0.1'),
                    'port' => env('DB_PORT', '3306'),
                    'database' => 'x_investor',
                    'username' => env('DB_USERNAME', 'root'),
                    'password' => env('DB_PASSWORD', ''),
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                    'strict' => false,
                ]
            ]);

            $this->wpDb = DB::connection('wordpress');

            // Test connection
            $this->wpDb->getPdo();

            $this->info('✓ Connected to WordPress database');
            return true;

        } catch (\Exception $e) {
            $this->error('Failed to connect to WordPress database: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear all existing data
     */
    protected function clearExistingData()
    {
        $this->info('Clearing existing data...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate pivot and dependent tables first
        DB::table('article_tag')->truncate();
        DB::table('comment_likes')->truncate();
        DB::table('comments')->truncate();
        DB::table('articles')->truncate();
        DB::table('navigation_items')->truncate();
        DB::table('pages')->truncate();
        DB::table('tags')->truncate();
        DB::table('categories')->truncate();

        // Delete all users except admin (id = 1)
        User::where('id', '>', 1)->delete();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Clear caches
        Cache::flush();
        Artisan::call('view:clear');

        $this->info('✓ Existing data cleared');
        $this->newLine();
    }

    /**
     * Import users from WordPress
     */
    protected function importUsers()
    {
        $this->info('Importing users...');

        $wpUsers = $this->wpDb->table('users')->get();

        $bar = $this->output->createProgressBar($wpUsers->count());
        $bar->start();

        foreach ($wpUsers as $wpUser) {
            try {
                if ($this->option('dry-run')) {
                    $this->userMap[$wpUser->ID] = 1; // Map to admin in dry-run
                    $bar->advance();
                    continue;
                }

                // Check if user already exists
                $existingUser = User::where('email', $wpUser->user_email)->first();
                if ($existingUser) {
                    $this->userMap[$wpUser->ID] = $existingUser->id;
                    $bar->advance();
                    continue;
                }

                $user = User::create([
                    'name' => !empty($wpUser->display_name) ? $wpUser->display_name : $wpUser->user_login,
                    'email' => $wpUser->user_email,
                    'password' => Hash::make(Str::random(16)), // Generate random password
                    'role' => 'author',
                    'is_active' => true,
                    'created_at' => $wpUser->user_registered,
                ]);

                $this->userMap[$wpUser->ID] = $user->id;
                $this->stats['users']++;

            } catch (\Exception $e) {
                $this->stats['errors']++;
                Log::error("Failed to import user {$wpUser->user_login}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✓ Imported {$this->stats['users']} users");
        $this->newLine();
    }

    /**
     * Import categories from WordPress
     */
    protected function importCategories()
    {
        $this->info('Importing categories...');

        $wpCategories = $this->wpDb->table('terms')
            ->join('term_taxonomy', 'terms.term_id', '=', 'term_taxonomy.term_id')
            ->where('term_taxonomy.taxonomy', 'category')
            ->select('terms.*', 'term_taxonomy.parent', 'term_taxonomy.description')
            ->orderBy('term_taxonomy.parent') // Import parents first
            ->get();

        $bar = $this->output->createProgressBar($wpCategories->count());
        $bar->start();

        foreach ($wpCategories as $wpCat) {
            try {
                if ($this->option('dry-run')) {
                    $this->categoryMap[$wpCat->term_id] = $wpCat->term_id;
                    $bar->advance();
                    continue;
                }

                $category = Category::create([
                    'name' => $wpCat->name,
                    'slug' => $wpCat->slug,
                    'description' => $wpCat->description,
                    'parent_id' => $wpCat->parent > 0 ? $wpCat->parent : null,
                    'order' => 0,
                    'is_active' => true,
                ]);

                $this->categoryMap[$wpCat->term_id] = $category->id;
                $this->stats['categories']++;

            } catch (\Exception $e) {
                $this->stats['errors']++;
                Log::error("Failed to import category {$wpCat->name}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✓ Imported {$this->stats['categories']} categories");
        $this->newLine();
    }

    /**
     * Import tags from WordPress
     */
    protected function importTags()
    {
        $this->info('Importing tags...');

        $wpTags = $this->wpDb->table('terms')
            ->join('term_taxonomy', 'terms.term_id', '=', 'term_taxonomy.term_id')
            ->where('term_taxonomy.taxonomy', 'post_tag')
            ->select('terms.*')
            ->get();

        $bar = $this->output->createProgressBar($wpTags->count());
        $bar->start();

        foreach ($wpTags as $wpTag) {
            try {
                if ($this->option('dry-run')) {
                    $this->tagMap[$wpTag->term_id] = $wpTag->term_id;
                    $bar->advance();
                    continue;
                }

                $tag = Tag::create([
                    'name' => $wpTag->name,
                    'slug' => $wpTag->slug,
                ]);

                $this->tagMap[$wpTag->term_id] = $tag->id;
                $this->stats['tags']++;

            } catch (\Exception $e) {
                $this->stats['errors']++;
                Log::error("Failed to import tag {$wpTag->name}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✓ Imported {$this->stats['tags']} tags");
        $this->newLine();
    }

    /**
     * Import articles from WordPress posts
     */
    protected function importArticles()
    {
        $this->info('Importing articles...');

        $query = $this->wpDb->table('posts')
            ->where('post_type', 'post')
            ->where('post_status', 'publish');

        if ($this->option('limit')) {
            $query->limit((int) $this->option('limit'));
        }

        $wpPosts = $query->orderBy('post_date', 'desc')->get();

        $bar = $this->output->createProgressBar($wpPosts->count());
        $bar->start();

        foreach ($wpPosts as $wpPost) {
            try {
                // Get primary category
                $primaryCategoryId = $this->getPostPrimaryCategory($wpPost->ID);
                if (!$primaryCategoryId) {
                    // Skip posts without category
                    $bar->advance();
                    continue;
                }

                // Get author
                $authorId = $this->userMap[$wpPost->post_author] ?? 1; // Default to admin

                // Get featured image (now returns JSON with multiple sizes)
                $featuredImageJson = $this->getPostFeaturedImage($wpPost->ID);

                // Get tags
                $tagIds = $this->getPostTags($wpPost->ID);

                // Process content URLs - replace absolute with relative
                $processedContent = $this->replaceContentUrls($wpPost->post_content);

                // Generate summary if empty
                $summary = !empty($wpPost->post_excerpt)
                    ? $wpPost->post_excerpt
                    : Str::limit(strip_tags($wpPost->post_content), 200);

                if ($this->option('dry-run')) {
                    $this->articleMap[$wpPost->ID] = $wpPost->ID;
                    $bar->advance();
                    continue;
                }

                $article = Article::create([
                    'title' => $wpPost->post_title,
                    'slug' => $wpPost->post_name,
                    'summary' => $summary,
                    'content' => $processedContent, // Use processed content
                    'featured_image' => $featuredImageJson, // Store as JSON
                    'author_id' => $authorId,
                    'category_id' => $primaryCategoryId,
                    'published_at' => $wpPost->post_date,
                    'is_published' => true,
                    'is_featured' => false,
                    'view_count' => 0,
                    'created_at' => $wpPost->post_date,
                    'updated_at' => $wpPost->post_modified,
                ]);

                // Attach tags
                if (!empty($tagIds)) {
                    $article->tags()->attach($tagIds);
                }

                $this->articleMap[$wpPost->ID] = $article->id;
                $this->stats['articles']++;

            } catch (\Exception $e) {
                $this->stats['errors']++;
                Log::error("Failed to import post {$wpPost->post_title}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✓ Imported {$this->stats['articles']} articles");
        $this->newLine();
    }

    /**
     * Get primary category for a WordPress post
     */
    protected function getPostPrimaryCategory($postId): ?int
    {
        $termTaxonomyId = $this->wpDb->table('term_relationships')
            ->join('term_taxonomy', 'term_relationships.term_taxonomy_id', '=', 'term_taxonomy.term_taxonomy_id')
            ->where('term_relationships.object_id', $postId)
            ->where('term_taxonomy.taxonomy', 'category')
            ->value('term_taxonomy.term_id');

        return $termTaxonomyId ? ($this->categoryMap[$termTaxonomyId] ?? null) : null;
    }

    /**
     * Get tags for a WordPress post
     */
    protected function getPostTags($postId): array
    {
        $termIds = $this->wpDb->table('term_relationships')
            ->join('term_taxonomy', 'term_relationships.term_taxonomy_id', '=', 'term_taxonomy.term_taxonomy_id')
            ->where('term_relationships.object_id', $postId)
            ->where('term_taxonomy.taxonomy', 'post_tag')
            ->pluck('term_taxonomy.term_id')
            ->toArray();

        $mappedTagIds = [];
        foreach ($termIds as $termId) {
            if (isset($this->tagMap[$termId])) {
                $mappedTagIds[] = $this->tagMap[$termId];
            }
        }

        return $mappedTagIds;
    }

    /**
     * Get featured image URL for a WordPress post and process through ImageService
     */
    protected function getPostFeaturedImage($postId): ?string
    {
        $thumbnailId = $this->wpDb->table('postmeta')
            ->where('post_id', $postId)
            ->where('meta_key', '_thumbnail_id')
            ->value('meta_value');

        if (!$thumbnailId) {
            return null;
        }

        $imageUrl = $this->wpDb->table('posts')
            ->where('ID', $thumbnailId)
            ->value('guid');

        if (!$imageUrl) {
            return null;
        }

        // Process image through ImageService
        return $this->processImageFromWordPress($imageUrl);
    }

    /**
     * Process image from WordPress installation through ImageService
     */
    protected function processImageFromWordPress(string $wpImageUrl, string $type = 'featured'): ?string
    {
        try {
            // Extract relative path from WordPress URL
            // Pattern: wp-content/uploads/2025/01/image.jpg
            if (!preg_match('#wp-content/uploads/(.+)$#', $wpImageUrl, $matches)) {
                Log::warning("Could not extract path from WordPress URL: {$wpImageUrl}");
                return null;
            }

            $relativePath = $matches[1]; // e.g., "2025/01/image.jpg"

            // Build local source path
            $sourcePath = $this->wpInstallPath . '/wp-content/uploads/' . $relativePath;

            // Check if file exists
            if (!file_exists($sourcePath)) {
                Log::warning("WordPress image not found at: {$sourcePath}");
                $this->stats[$type . '_images_not_found']++;
                return null;
            }

            // Create temporary file
            $tempPath = tempnam(sys_get_temp_dir(), 'wp_img_');
            copy($sourcePath, $tempPath);

            // Get file info
            $mimeType = mime_content_type($sourcePath);
            $extension = pathinfo($sourcePath, PATHINFO_EXTENSION);
            $originalName = basename($sourcePath);

            // Create UploadedFile instance
            $uploadedFile = new UploadedFile(
                $tempPath,
                $originalName,
                $mimeType,
                null,
                true // test mode (allows temp files)
            );

            // Process through ImageService
            $imageService = app(ImageService::class);
            $paths = $imageService->processUpload($uploadedFile, 'articles');

            // Clean up temp file
            @unlink($tempPath);

            // Track success
            $this->stats[$type . '_images_processed']++;

            // Return JSON-encoded paths
            return json_encode($paths);

        } catch (\Exception $e) {
            Log::error("Failed to process WordPress image {$wpImageUrl}: " . $e->getMessage());
            $this->stats[$type . '_images_failed']++;
            return null;
        }
    }

    /**
     * Replace absolute WordPress URLs in content with relative storage paths
     * and process images through ImageService
     */
    protected function replaceContentUrls(string $content): string
    {
        // Patterns to match WordPress image URLs
        $patterns = [
            '#https?://x-investor\.de/wp-content/uploads/([^"\'>\s]+)#i',
            '#https?://coin\.themevivu\.site/wp-content/uploads/([^"\'>\s]+)#i',
        ];

        foreach ($patterns as $pattern) {
            $content = preg_replace_callback($pattern, function($matches) {
                $originalUrl = $matches[0]; // Full URL
                $relativePath = $matches[1]; // e.g., "2025/01/image.jpg"

                // Check cache first to avoid re-processing same image
                if (isset($this->processedContentImages[$relativePath])) {
                    return $this->processedContentImages[$relativePath];
                }

                // WordPress images can have size suffixes like -150x150, -300x200, etc.
                // We want to process the original image, not the resized versions
                $cleanRelativePath = preg_replace('/-\d+x\d+(\.[a-z]+)$/i', '$1', $relativePath);

                // Build WordPress URL for processing
                $wpImageUrl = 'wp-content/uploads/' . $cleanRelativePath;

                // Process image through ImageService
                $processedJson = $this->processImageFromWordPress($wpImageUrl, 'content');

                if ($processedJson) {
                    $paths = json_decode($processedJson, true);

                    // For content images, use large size for better quality
                    // Fall back to medium, then original if large doesn't exist
                    $imagePath = $paths['large'] ?? $paths['medium'] ?? $paths['original'];

                    // Convert to Storage URL (relative path)
                    $storageUrl = Storage::url($imagePath);

                    // Cache the result
                    $this->processedContentImages[$relativePath] = $storageUrl;

                    return $storageUrl;
                }

                // If processing failed, keep original URL
                $this->processedContentImages[$relativePath] = $originalUrl;
                return $originalUrl;
            }, $content);
        }

        return $content;
    }

    /**
     * Import pages from WordPress
     */
    protected function importPages()
    {
        $this->info('Importing pages...');

        $wpPages = $this->wpDb->table('posts')
            ->where('post_type', 'page')
            ->where('post_status', 'publish')
            ->get();

        $bar = $this->output->createProgressBar($wpPages->count());
        $bar->start();

        foreach ($wpPages as $wpPage) {
            try {
                if ($this->option('dry-run')) {
                    $bar->advance();
                    continue;
                }

                Page::create([
                    'title' => $wpPage->post_title,
                    'slug' => $wpPage->post_name,
                    'content' => $wpPage->post_content,
                    'is_published' => true,
                    'created_at' => $wpPage->post_date,
                    'updated_at' => $wpPage->post_modified,
                ]);

                $this->stats['pages']++;

            } catch (\Exception $e) {
                $this->stats['errors']++;
                Log::error("Failed to import page {$wpPage->post_title}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✓ Imported {$this->stats['pages']} pages");
        $this->newLine();
    }

    /**
     * Import comments from WordPress
     */
    protected function importComments()
    {
        $this->info('Importing comments...');

        $wpComments = $this->wpDb->table('comments')
            ->where('comment_approved', '1')
            ->orderBy('comment_parent') // Import parent comments first
            ->get();

        $bar = $this->output->createProgressBar($wpComments->count());
        $bar->start();

        foreach ($wpComments as $wpComment) {
            try {
                // Get mapped article ID
                $articleId = $this->articleMap[$wpComment->comment_post_ID] ?? null;
                if (!$articleId) {
                    // Skip comments for non-imported posts
                    $bar->advance();
                    continue;
                }

                // Get mapped user ID
                $userId = null;
                $guestName = null;
                $guestEmail = null;

                if ($wpComment->user_id > 0) {
                    $userId = $this->userMap[$wpComment->user_id] ?? null;
                }

                if (!$userId) {
                    $guestName = $wpComment->comment_author;
                    $guestEmail = $wpComment->comment_author_email;
                }

                if ($this->option('dry-run')) {
                    $bar->advance();
                    continue;
                }

                Comment::create([
                    'article_id' => $articleId,
                    'user_id' => $userId,
                    'guest_name' => $guestName,
                    'guest_email' => $guestEmail,
                    'content' => $wpComment->comment_content,
                    'parent_id' => $wpComment->comment_parent > 0 ? $wpComment->comment_parent : null,
                    'is_approved' => true,
                    'likes_count' => 0,
                    'created_at' => $wpComment->comment_date,
                ]);

                $this->stats['comments']++;

            } catch (\Exception $e) {
                $this->stats['errors']++;
                Log::error("Failed to import comment ID {$wpComment->comment_ID}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✓ Imported {$this->stats['comments']} comments");
        $this->newLine();
    }

    /**
     * Run post-import tasks
     */
    protected function postImportTasks()
    {
        $this->info('Running post-import tasks...');

        // Rebuild search index
        $this->info('Rebuilding search index...');
        Artisan::call('scout:import', ['model' => 'App\\Models\\Article']);

        // Clear all caches
        Cache::flush();
        Artisan::call('view:clear');
        Artisan::call('cache:clear');

        $this->info('✓ Post-import tasks completed');
        $this->newLine();
    }

    /**
     * Display import summary
     */
    protected function displaySummary()
    {
        $this->newLine();
        $this->info('===========================================');
        $this->info('Import Summary');
        $this->info('===========================================');
        $this->table(
            ['Entity', 'Count'],
            [
                ['Users', $this->stats['users']],
                ['Categories', $this->stats['categories']],
                ['Tags', $this->stats['tags']],
                ['Articles', $this->stats['articles']],
                ['Pages', $this->stats['pages']],
                ['Comments', $this->stats['comments']],
                ['Featured Images Processed', $this->stats['featured_images_processed']],
                ['Featured Images Not Found', $this->stats['featured_images_not_found']],
                ['Featured Images Failed', $this->stats['featured_images_failed']],
                ['Content Images Processed', $this->stats['content_images_processed']],
                ['Content Images Not Found', $this->stats['content_images_not_found']],
                ['Content Images Failed', $this->stats['content_images_failed']],
                ['Errors', $this->stats['errors']],
            ]
        );
    }
}

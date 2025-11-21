<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:sitemap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate XML sitemap for search engines';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating sitemap...');

        $sitemap = Sitemap::create();

        // Add homepage
        $sitemap->add(
            Url::create(route('home'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(1.0)
        );

        // Add search page
        $sitemap->add(
            Url::create(route('search'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.5)
        );

        // Add all active categories
        $this->info('Adding categories...');
        $categories = Category::all();
        foreach ($categories as $category) {
            $sitemap->add(
                Url::create(route('category.show', $category->slug))
                    ->setLastModificationDate($category->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                    ->setPriority(0.8)
            );
        }
        $this->info("Added {$categories->count()} categories");

        // Add all published articles
        $this->info('Adding articles...');
        $articles = Article::published()
            ->with('category')
            ->orderBy('published_at', 'desc')
            ->get();

        foreach ($articles as $article) {
            $priority = $article->is_featured ? 0.9 : 0.6;

            $sitemap->add(
                Url::create(route('article.show', [$article->category->slug, $article->slug]))
                    ->setLastModificationDate($article->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority($priority)
            );
        }
        $this->info("Added {$articles->count()} articles");

        // Write sitemap to public directory
        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully at: ' . public_path('sitemap.xml'));
        $this->info('Total URLs: ' . ($categories->count() + $articles->count() + 2));

        return Command::SUCCESS;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Latest general articles with pagination (12 per page initially, then 6 per load)
        $latestArticles = Article::with(['category', 'author'])
            ->published()
            ->latest('published_at')
            ->paginate(12);

        // Handle AJAX request for load more
        if ($request->ajax()) {
            $html = view('home.partials.latest-articles', [
                'articles' => $latestArticles
            ])->render();

            return response()->json([
                'html' => $html,
                'next_page' => $latestArticles->hasMorePages() ? $latestArticles->currentPage() + 1 : null,
            ]);
        }

        // Hero article (single, most recent featured)
        $heroArticle = Article::with(['category', 'author'])
            ->published()
            ->featured()
            ->latest('published_at')
            ->first();

        // Secondary featured articles (10 articles for new layout)
        $featuredArticles = Article::with(['category', 'author'])
            ->published()
            ->featured()
            ->latest('published_at')
            ->skip(1)
            ->take(10)
            ->get();

        // Most read (cached for 15 minutes)
        $mostRead = Cache::remember('homepage.mostRead', 900, function() {
            return Article::with(['category', 'author'])
                ->published()
                ->orderBy('view_count', 'desc')
                ->take(5)
                ->get();
        });

        // Valuation Forum (Diễn đàn Thẩm định giá)
        $valuationArticles = Article::with(['category', 'author'])
            ->published()
            ->whereHas('category', fn($q) => $q->where('slug', 'tham-dinh-gia'))
            ->latest('published_at')
            ->take(4)
            ->get();

        // Business Bridge (Nhịp cầu doanh nghiệp)
        $businessArticles = Article::with(['category', 'author'])
            ->published()
            ->whereHas('category', fn($q) => $q->where('slug', 'kinh-doanh'))
            ->latest('published_at')
            ->take(5)
            ->get();

        // Special Publications (Đặc san)
        $specialPublications = Article::with(['category', 'author'])
            ->published()
            ->where('is_special_publication', true)
            ->latest('published_at')
            ->take(2)
            ->get();

        // Category blocks (cached for 1 hour)
        $categories = Cache::remember('homepage.categories', 3600, function() {
            return Category::with(['articles' => function($q) {
                $q->with(['category', 'author'])
                    ->published()
                    ->latest('published_at')
                    ->take(4);
            }])
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
        });

        return view('home.index', compact(
            'heroArticle',
            'featuredArticles',
            'latestArticles',
            'mostRead',
            'valuationArticles',
            'businessArticles',
            'specialPublications',
            'categories'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function show(Category $category, Request $request)
    {
        $page = $request->get('page', 1);

        // Get hero article for category (cached for 15 minutes)
        $heroArticle = Cache::remember("category.{$category->id}.hero", 900, function () use ($category) {
            return $category->articles()
                ->with(['category', 'author'])
                ->published()
                ->latest('published_at')
                ->first();
        });

        // Get 8 featured articles for the featured section (cached for 15 minutes)
        $featuredArticles = Cache::remember("category.{$category->id}.featured", 900, function () use ($category, $heroArticle) {
            $query = $category->articles()
                ->with(['category', 'author'])
                ->published()
                ->latest('published_at');

            if ($heroArticle) {
                $query->where('id', '!=', $heroArticle->id);
            }

            return $query->take(6)->get();
        });

        // Get remaining articles (excluding hero and featured articles)
        $articlesQuery = $category->articles()
            ->with(['category', 'author'])
            ->published()
            ->latest('published_at');

        // Exclude hero article and featured articles from main listing
        $excludeIds = collect([$heroArticle])->merge($featuredArticles)->pluck('id')->filter()->toArray();
        if (!empty($excludeIds)) {
            $articlesQuery->whereNotIn('id', $excludeIds);
        }

        // For AJAX requests, return JSON
        if ($request->ajax()) {
            $articles = $articlesQuery->paginate(12);
            return response()->json([
                'html' => view('partials.articles-grid', compact('articles'))->render(),
                'next_page' => $articles->hasMorePages() ? $articles->currentPage() + 1 : null
            ]);
        }

        // For regular requests, paginate (cached for 15 minutes per page)
        $articles = Cache::remember("category.{$category->id}.articles.page.{$page}", 900, function () use ($articlesQuery) {
            return $articlesQuery->paginate(12);
        });

        // Get most read for sidebar (cached for 15 minutes)
        $mostRead = Cache::remember('articles.most_read', 900, function () {
            return Article::with(['category', 'author'])
                ->published()
                ->orderBy('view_count', 'desc')
                ->take(5)
                ->get();
        });

        return view('categories.show', compact('category', 'heroArticle', 'featuredArticles', 'articles', 'mostRead'));
    }
}

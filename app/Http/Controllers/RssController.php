<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;

class RssController extends Controller
{
    /**
     * Display the main RSS feed
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $articles = Article::published()
            ->with(['author', 'category'])
            ->latest('published_at')
            ->take(50)
            ->get();

        return response()->view('rss.feed', [
            'articles' => $articles,
            'title' => config('app.name') . ' - Tin tức mới nhất',
            'description' => 'Tin tức tài chính, kinh doanh, chứng khoán, bất động sản và phân tích thị trường.',
            'link' => route('home'),
        ])->header('Content-Type', 'application/xml');
    }

    /**
     * Display category-specific RSS feed
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function category(Category $category)
    {
        $articles = Article::published()
            ->where('category_id', $category->id)
            ->with(['author', 'category'])
            ->latest('published_at')
            ->take(50)
            ->get();

        return response()->view('rss.feed', [
            'articles' => $articles,
            'title' => $category->name . ' - ' . config('app.name'),
            'description' => $category->description ?? "Tin tức về {$category->name}",
            'link' => route('category.show', $category),
        ])->header('Content-Type', 'application/xml');
    }
}

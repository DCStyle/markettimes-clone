<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q', '');

        // If query is empty, return empty results
        if (empty(trim($query))) {
            $articles = collect();

            if ($request->ajax()) {
                return response()->json([
                    'html' => '',
                    'next_page' => null,
                ]);
            }

            return view('search.index', compact('query', 'articles'));
        }

        // Search using Scout with published filter
        $articles = Article::search($query)
            ->query(fn ($builder) => $builder
                ->where('is_published', true)
                ->with(['category', 'author'])
            )
            ->paginate(15);

        // Handle AJAX requests for infinite scroll
        if ($request->ajax()) {
            $html = view('search.partials.results', compact('articles'))->render();

            return response()->json([
                'html' => $html,
                'next_page' => $articles->hasMorePages() ? $articles->currentPage() + 1 : null,
            ]);
        }

        return view('search.index', compact('query', 'articles'));
    }
}

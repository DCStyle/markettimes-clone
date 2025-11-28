<?php

use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Not Found'], 404);
            }

            $popularArticles = Article::published()
                ->with(['category', 'author'])
                ->orderBy('view_count', 'desc')
                ->take(5)
                ->get();

            $latestArticles = Article::published()
                ->with(['category', 'author'])
                ->latest('published_at')
                ->take(4)
                ->get();

            $categories = Category::active()
                ->parent()
                ->ordered()
                ->take(6)
                ->get();

            return response()->view('errors.404', [
                'popularArticles' => $popularArticles,
                'latestArticles' => $latestArticles,
                'categories' => $categories,
                'currentCategory' => null,
                'category' => null,
            ], 404);
        });
    })->create();

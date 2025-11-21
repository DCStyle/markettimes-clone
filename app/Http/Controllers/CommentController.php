<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    /**
     * Store a new comment
     */
    public function store(Request $request, $articleId)
    {
        // Manually find the article
        $article = Article::findOrFail($articleId);

        $rules = [
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ];

        // Add guest fields validation if not authenticated
        if (!auth()->check()) {
            $rules['guest_name'] = 'required|string|max:255';
            $rules['guest_email'] = 'nullable|email|max:255';
        }

        $validated = $request->validate($rules);

        $comment = $article->comments()->create([
            'user_id' => auth()->id(),
            'content' => strip_tags($validated['content']),
            'parent_id' => $validated['parent_id'] ?? null,
            'guest_name' => $validated['guest_name'] ?? null,
            'guest_email' => $validated['guest_email'] ?? null,
            'is_approved' => false, // Require moderation
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Bình luận của bạn đang chờ kiểm duyệt.',
                'comment' => $comment,
            ]);
        }

        return back()->with('success', 'Bình luận của bạn đang chờ kiểm duyệt.');
    }

    /**
     * Update an existing comment (within 15 minutes)
     */
    public function update(Request $request, Comment $comment)
    {
        $userId = auth()->id();
        $ipAddress = $request->ip();

        if (!$comment->canEdit($userId, $ipAddress)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không thể chỉnh sửa bình luận này.',
                ], 403);
            }
            return back()->with('error', 'Bạn không thể chỉnh sửa bình luận này.');
        }

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment->update([
            'content' => strip_tags($validated['content']),
            'is_approved' => false, // Re-moderate edited comments
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Bình luận đã được cập nhật và đang chờ kiểm duyệt.',
                'comment' => $comment,
            ]);
        }

        return back()->with('success', 'Bình luận đã được cập nhật và đang chờ kiểm duyệt.');
    }

    /**
     * Delete a comment (within 15 minutes)
     */
    public function destroy(Request $request, Comment $comment)
    {
        $userId = auth()->id();
        $ipAddress = $request->ip();

        if (!$comment->canDelete($userId, $ipAddress)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không thể xóa bình luận này.',
                ], 403);
            }
            return back()->with('error', 'Bạn không thể xóa bình luận này.');
        }

        $comment->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Bình luận đã được xóa.',
            ]);
        }

        return back()->with('success', 'Bình luận đã được xóa.');
    }

    /**
     * Toggle like on a comment
     */
    public function like(Request $request, Comment $comment)
    {
        $userId = auth()->id();
        $ipAddress = $request->ip();

        DB::beginTransaction();

        try {
            if ($userId) {
                // Authenticated user
                $liked = $comment->likes()->where('user_id', $userId)->exists();

                if ($liked) {
                    $comment->likes()->detach($userId);
                    $comment->decrement('likes_count');
                    $action = 'unliked';
                } else {
                    $comment->likes()->attach($userId);
                    $comment->increment('likes_count');
                    $action = 'liked';
                }
            } else {
                // Guest user - use IP address
                $liked = DB::table('comment_likes')
                    ->where('comment_id', $comment->id)
                    ->where('ip_address', $ipAddress)
                    ->whereNull('user_id')
                    ->exists();

                if ($liked) {
                    DB::table('comment_likes')
                        ->where('comment_id', $comment->id)
                        ->where('ip_address', $ipAddress)
                        ->whereNull('user_id')
                        ->delete();
                    $comment->decrement('likes_count');
                    $action = 'unliked';
                } else {
                    DB::table('comment_likes')->insert([
                        'comment_id' => $comment->id,
                        'ip_address' => $ipAddress,
                        'user_id' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $comment->increment('likes_count');
                    $action = 'liked';
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'action' => $action,
                    'likes_count' => $comment->fresh()->likes_count,
                ]);
            }

            return back();
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra. Vui lòng thử lại.',
                ], 500);
            }

            return back()->with('error', 'Có lỗi xảy ra. Vui lòng thử lại.');
        }
    }
}

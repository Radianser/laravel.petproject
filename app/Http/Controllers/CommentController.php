<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Comment;
use App\Http\Requests\CommentStoreRequest;
use App\Http\Requests\CommentUpdateRequest;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\RedisController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\FileController;


class CommentController extends Controller
{
    private $redis;

    public function __construct()
    {
        if(!$this->redis) {
            $this->redis = new RedisController;
        }
    }

    public function store(CommentStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $comment = $request->user()->comments()->create($validated);
        (new AttachmentController)->index($request, $comment->id, 'comment');
        
        $this->redis->set_comment($comment);
        $comment = $this->redis->get_details([$comment], 'comment');

        return response()->json($comment[0])->header('Content-Type', 'application/json');
    }

    public function update(CommentUpdateRequest $request, Comment $comment): JsonResponse
    {
        $this->authorize('update', $comment);
        $validated = $request->validated();
        $comment->update($validated);
        (new AttachmentController)->index($request, $comment->id, 'comment');

        $post = $this->redis->get_comment($comment->id);
        $post = $this->redis->update_comment($post, $validated['message']);
        $comment = $this->redis->get_details([$post], 'comment');
    
        return response()->json($comment[0])->header('Content-Type', 'application/json;charset=utf-8');
    }

    public function destroy(Comment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);
        $response = $comment->id;

        $this->redis->delete_comment($comment->id);

        $this->delete_dependent_records($comment->id);
        $comment->delete();

        return response()->json($response)->header('Content-Type', 'application/json');
    }

    public function destroy_all($id, $objectType): void
    {
        $comments = Comment::where([
            ['commentable_id', '=', $id],
            ['commentable_type', '=', $objectType]
        ])->get();

        if(!empty($comments)) {
            foreach($comments as $comment) {
                $this->delete_dependent_records($comment->id);
                $this->redis->delete_comment($comment->id);
                $comment->delete();
            }
        }
    }

    private function delete_dependent_records($id): void
    {
        (new LikeController)->destroy_all($id, 'comment');
        (new LinkController)->destroy_all($id, 'comment');
        (new ImageController)->destroy_all($id, 'comment');
        (new VideoController)->destroy_all($id, 'comment');
        (new FileController)->destroy_all($id, 'comment');
    }
}
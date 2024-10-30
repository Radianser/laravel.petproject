<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Like;
use App\Events\LikesCountChange;
use App\Http\Requests\LikeRequest;
use App\Http\Controllers\RedisController;

class LikeController extends Controller
{
    private $redis;

    public function __construct()
    {
        if(!$this->redis) {
            $this->redis = new RedisController;
        }
    }

    public function store_like(LikeRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $like = Like::where([
            ['likeable_id', '=', "$validated[likeable_id]"],
            ['likeable_type', '=', "$validated[likeable_type]"],
            ['user_id', '=', "$validated[user_id]"]
        ])->first();

        if($like) {
            $this->redis->delete_like($like->id);
            $like->delete();
            $response = false;
        } else {
            $like = $request->user()->likes()->create($validated);
            $this->redis->set_like($like);
            $response = true;
        }

        broadcast(new LikesCountChange($validated['likeable_id'], $validated['likeable_type'], $request, $response))->toOthers();

        return response()->json($response)->header('Content-Type', 'application/json');
    }

    public function show_likes(Request $request, $type = null, $id = null)
    {
        if($request->wantsJson()) {
            return $this->redis->turn_the_page($request);
        }

        $message = (object)[];
        $message->id = $id;
        $message->type = $type;
        $message->likes = [];
        $this->redis->get_likes($message);

        return Inertia::render('UsersList', [
            'profiles' => $this->redis->create_export_data($request, $message->likes, false),
            'title' => 'liked',
            'localization' => $this->redis->get_localization(),
            'session' => $this->redis->get_session($request)
        ]);
    }

    public function show_replies(Request $request, $id)
    {
        if($request->wantsJson()) {
            return $this->redis->turn_the_page($request);
        }

        return Inertia::render('BabblesList', [
            'babbles' => $this->redis->create_posts_list($request, 'babble_id', [$id]),
            'title' => 'replied',
            'localization' => $this->redis->get_localization(),
            'session' => $this->redis->get_session($request)
        ]);
    }

    public function destroy_all($id, $objectType): void
    {
        $likes = Like::where([
            ['likeable_id', '=', $id],
            ['likeable_type', '=', $objectType]
        ])->get();

        if(!empty($likes)) {
            foreach($likes as $like) {
                $this->redis->delete_like($like->id);
                $like->delete();
            }
        }
    }
}
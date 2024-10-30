<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Babble;
use App\Http\Requests\BabbleStoreRequest;
use App\Http\Requests\BabbleUpdateRequest;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\RedisController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\FileController;

class BabbleController extends Controller
{
    private $redis;

    public function __construct()
    {
        if(!$this->redis) {
            $this->redis = new RedisController;
        }
    }

    public function feed(Request $request)
    {
        if ($request->isMethod('post')) {
            $babbles = $this->redis->turn_the_page($request);
            $babbles->data = $this->redis->get_details($babbles->data);

            return $babbles;
        }

        $users = $request->user()->following;
        $users[] = $request->user()->id;

        return Inertia::render('BabblesList', [
            'babbles' => $this->redis->create_posts_list($request, 'user_id', $users),
            'title' => 'feed',
            'localization' => $this->redis->get_localization(),
            'session' => $this->redis->get_session($request)
        ]);
    }

    public function show(Request $request, $id): Response
    {
        $post = $this->redis->get_post_message($id, true);
        
        return Inertia::render('BabblesList', [
            'babbles' => $this->redis->create_export_data($request, [$post], true),
            'title' => 'Post',
            'localization' => $this->redis->get_localization(),
            'session' => $this->redis->get_session($request)
        ]);
    }

    public function store(BabbleStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $babble = $request->user()->babbles()->create($validated);
        (new AttachmentController)->index($request, $babble->id, 'babble');
        
        $this->redis->set_post_message($babble);
        $babble = $this->redis->get_details([$babble], 'babble');
        
        return response()->json($babble[0])->header('Content-Type', 'application/json');
    }

    public function update(BabbleUpdateRequest $request, Babble $babble): JsonResponse
    {
        $this->authorize('update', $babble);
        $validated = $request->validated();
        $babble->update($validated);
        (new AttachmentController)->index($request, $babble->id, 'babble');
        
        $post = $this->redis->get_post_message($babble->id);
        $post = $this->redis->update_post_message($post, $validated['message']);
        $babble = $this->redis->get_details([$post], 'babble');
    
        return response()->json($babble[0])->header('Content-Type', 'application/json;charset=utf-8');
    }

    public function destroy(Babble $babble): JsonResponse
    {
        $this->authorize('delete', $babble);
        $response = $babble->id;

        $this->redis->delete_post_message($babble->id);
        
        $this->delete_dependent_records($babble->id);
        $babble->delete();

        return response()->json($response)->header('Content-Type', 'application/json');
    }

    private function delete_dependent_records($id): void
    {
        (new CommentController)->destroy_all($id, 'babble');
        (new LikeController)->destroy_all($id, 'babble');
        (new LinkController)->destroy_all($id, 'babble');
        (new ImageController)->destroy_all($id, 'babble');
        (new VideoController)->destroy_all($id, 'babble');
        (new FileController)->destroy_all($id, 'babble');
    }
}
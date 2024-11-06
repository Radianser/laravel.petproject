<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\RedisController;

class UserController extends Controller
{
    private $redis;

    public function __construct()
    {
        if(!$this->redis) {
            $this->redis = new RedisController;
        }
    }

    public function index(Request $request, $id = null)
    {
        if(!Auth::check()) {
            return $this->get_user_page($request, $id);
        }

        if($id != null && $id != $request->user()->id) {
            return $this->get_user_page($request, $id);
        } else if($id != null && $id == $request->user()->id) {
            return redirect(route('home.index'));
        } else {
            return $this->get_user_page($request, $request->user()->id);
        }
    }

    public function next_page(Request $request): JsonResponse
    {
        $babbles = $this->redis->turn_the_page($request);
        $babbles->data = $this->redis->get_details($babbles->data);

        return response()->json($babbles)->header('Content-Type', 'application/json');
    }

    private function get_user_page(Request $request, $id): Response
    {
        $user = $this->redis->get_user($id);
        $follow = $request->user()->following ?? [];
        
        if($request->user()) {
            $user_gallery = $this->redis->get_photos($user->id, 6);
            $subscription = in_array($user->id, $follow);
        } else {
            $user_gallery = [];
            $subscription = false;
        }

        return Inertia::render('UserProfile/Index', [
            'babbles' => $this->redis->create_posts_list($request, 'user_id', [$id]),
            'user' => $user,
            'images' => $user_gallery,
            'subscription' => $subscription,
            'localization' => $this->redis->get_localization(),
            'session' => $this->redis->get_session($request)
        ]);
    }

    public function subscribe(Request $request): void
    {
        $this->subscribe_user('following', $request->user()->id, $request->id);
        $this->subscribe_user('followers', $request->id, $request->user()->id);
    }

    private function subscribe_user($field, $user1_id, $user2_id): void
    {
        $redis_user = $this->redis->get_user($user1_id);
        $user = User::select('id', $field)->find($user1_id);

        $follow = $user->$field;

        if($follow === null) {
            $follow[] = $user2_id;
            $user->$field = $follow;
            $user->save();

            $redis_user->$field = $follow;
        } else {
            if(in_array($user2_id, $follow)) {
                if(count($follow) == 1) {
                    $follow = null;
                    $user->$field = $follow;
                    $user->save();

                    $redis_user->$field = [];
                } else {
                    $key = array_search($user2_id, $follow);
                    array_splice($follow, $key, 1);
                    $user->$field = $follow;
                    $user->save();

                    $redis_user->$field = $follow;
                }
            } else {
                $follow[] = $user2_id;
                $user->$field = $follow;
                $user->save();

                $redis_user->$field = $follow;
            }
        }

        $this->redis->set_user($redis_user);
    }

    public function unsubscribe_deleted_user(Request $request): JsonResponse
    {
        $type = lcfirst($request->title);
        $user = User::find($request->auth_user_id);
        $redis_user = $this->redis->get_user($request->auth_user_id);
        $subs = $user->$type;

        if(count($subs) == 1) {
            $subs = null;
        } else {
            $key = array_search($request->user_id, $subs);
            array_splice($subs, $key, 1);
        }

        $user->$type = $subs;
        $user->save();

        $redis_user->$type = $subs;
        $this->redis->set_user($redis_user);

        return response()->json($request->user_id)->header('Content-Type', 'application/json');
    }

    public function follow(Request $request, $follow, $id)
    {
        if($request->wantsJson()) {
            return $this->redis->turn_the_page($request);
        }

        $user = $this->redis->get_user($id);

        if($user->$follow != null) {
            $profiles = $this->redis->create_users_list($user->$follow);
            $profiles = $this->redis->create_export_data($request, $profiles, false);
        } else {
            $profiles = (object)[];
            $profiles->data = [];
            $profiles->stop = true;
        }

        return Inertia::render('UsersList', [
            'profiles' => $profiles,
            'title' => $follow,
            'localization' => $this->redis->get_localization(),
            'session' => $this->redis->get_session($request)
        ]);
    }

    public function gallery(Request $request, $id)
    {
        if($request->wantsJson()) {
            return $this->redis->turn_the_page($request);
        }

        $user = $this->redis->get_user($id);
        $user_gallery = $this->redis->get_photos($user->id);

        return Inertia::render('Gallery', [
            'user' => $user,
            'images' => $this->redis->create_export_data($request, $user_gallery, false, 40),
            'localization' => $this->redis->get_localization(),
            'session' => $this->redis->get_session($request)
        ]);
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\User;
use App\Http\Controllers\RedisController;

class SearchController extends Controller
{
    public function index(Request $request): Response
    {
        $redis = new RedisController;

        return Inertia::render('Search', [
            'title' => 'search',
            'localization' => $redis->get_localization(),
            'session' => $redis->get_session($request)
        ]);
    }

    public function search(Request $request)
    {
        $redis = new RedisController;

        if($request->has('next')) {
            return $redis->turn_the_page($request);
        }
        
        $validated = $request->validate([
            'name' => 'required|string'
        ]);

        $user = $validated['name'];
        $profiles = User::where('name', 'LIKE', "%$user%")->select($redis->user_props)->get()->toArray();
        $list = $redis->create_export_data($request, $profiles, false);

        $redis->set_user($profiles);

        return response()->json($list)->header('Content-Type', 'application/json');
    }

    public function messenger(Request $request): Response
    {
        $redis = new RedisController;

        return Inertia::render('Messenger', [
            'title' => 'messenger',
            'localization' => $redis->get_localization(),
            'session' => $redis->get_session($request)
        ]);
    }
}
<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\RedisController;

class UserOnline
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::check()) {
            $id = Auth::id();
            $user = User::find($id);
            $user->update([
                'last_action' => now()
            ]);

            $redis = new RedisController;
            $user = $redis->get_user($id);
            $user->last_action = now();
            $redis->set_user($user);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Controllers\RedisController;
use Illuminate\Support\Facades\App;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): Response
    {
        $redis = new RedisController;

        return Inertia::render('Auth/Register', [
            'localization' => $redis->get_localization(),
            'session' => $redis->get_session($request)
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'language' => 'required|string|max:2'
        ]);

        App::setLocale($request->language);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'theme' => 'required|integer',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'language' => $request->language,
            'theme' => $request->theme,
        ]);

        event(new Registered($user));

        Auth::login($user);

        $redis = new RedisController;
        $redis->set_user(User::select($redis->user_props)->find($user->id));

        return redirect(RouteServiceProvider::HOME);
    }
}

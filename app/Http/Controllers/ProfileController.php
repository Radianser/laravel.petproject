<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\PreferencesUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\RedisController;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        $redis = new RedisController;

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'localization' => $redis->get_localization(),
            'session' => $redis->get_session($request)
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $request->user()->fill($validated);

        $redis = new RedisController;
        $user = $redis->get_user($request->user()->id);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
            $user->email_verified_at = null;
        }

        $request->user()->save();
        
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $redis->set_user($user);

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        Redis::del("user:$user->id");

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function update_session(PreferencesUpdateRequest $request): void
    {
        $validated = $request->validated();

        $request->session()->put('language', $validated['language']);
        $request->session()->put('theme', $validated['theme']);

        if($request->user()) {
            $this->update_user_settings($request, $validated['language'], $validated['theme'], $validated['sorting']);
        }
    }

    private function update_user_settings($request, $language, $theme, $sorting): void
    {
        $request->user()->language = $language;
        $request->user()->theme = $theme;
        $request->user()->sorting = $sorting;
        $request->user()->save();

        $redis = new RedisController;
        $user = $redis->get_user($request->user()->id);
        $user->theme = $theme;
        $user->language = $language;
        $user->sorting = $sorting;
        $redis->set_user($user);
    }
}
<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use App\Http\Controllers\BabbleController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\DemoController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\RedisController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
require __DIR__.'/auth.php';

Route::get('/', function (Request $request) {
    if(Auth::check()) {
        return Redirect::route('home.index');
    }

    $redis = new RedisController;
    
    return Inertia::render('Welcome', [
        'canResetPassword' => Route::has('password.request'),
        'status' => session('status'),
        'localization' => $redis->get_localization(),
        'session' => $redis->get_session($request)
    ]);
})->middleware('lang')->name('welcome');

Route::middleware(['auth', 'online', 'lang'])->controller(ProfileController::class)->group(function () {
    Route::get('/profile', 'edit')->name('profile.edit');
    Route::patch('/profile', 'update')->name('profile.update');
    Route::put('/profile', 'update_session')->name('profile.update_session')->withoutMiddleware(['auth']);
    Route::delete('/profile', 'destroy')->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'online', 'lang'])->controller(BabbleController::class)->group(function () {
    Route::match(['GET', 'POST'], '/feed', 'feed')->name('feed.show');
    Route::get('/babble/{id}', 'show')->name('babble.show')->withoutMiddleware(['auth', 'verified']);
    Route::post('/babble', 'store')->name('babble.store');
    Route::put('/babble/{babble}', 'update')->name('babble.update');
    Route::delete('/babble/{babble}', 'destroy')->name('babble.destroy');
});

Route::middleware(['auth', 'verified', 'online', 'lang'])->controller(CommentController::class)->group(function () {
    Route::post('/comment', 'store')->name('comment.store');
    Route::put('/comment/{comment}', 'update')->name('comment.update');
    Route::delete('/comment/{comment}', 'destroy')->name('comment.destroy');
});

Route::middleware(['auth', 'verified', 'online', 'lang'])->controller(LikeController::class)->group(function () {
    Route::post('/like', 'store_like')->name('like.store');
    Route::match(['GET', 'POST'], '/likes/{object?}/{id?}', 'show_likes')->name('likes.show');
    Route::get('/replies/{id}', 'show_replies')->name('replies.show')->withoutMiddleware(['auth', 'verified']);
});

Route::middleware(['auth', 'verified', 'online', 'lang'])->controller(UserController::class)->group(function () {
    Route::get('/profile/{id}', 'index')->withoutMiddleware(['auth', 'verified']);
    Route::get('/home', 'index')->name('home.index')->withoutMiddleware(['verified']);
    Route::post('/home', 'next_page')->name('next.page')->withoutMiddleware(['auth', 'verified']);
    Route::post('/subscribe', 'subscribe')->name('user.subscribe');
    Route::post('/unsubscribe', 'unsubscribe_deleted_user')->name('user.unsubscribe');
    Route::get('/home/{follow}/{id}', 'follow');
    Route::match(['GET', 'POST'], '/gallery/{id}', 'gallery')->name('gallery');
});

Route::middleware(['auth', 'verified', 'online', 'lang'])->controller(SearchController::class)->group(function () {
    Route::get('/search', 'index')->name('search.index');
    Route::post('/search', 'search')->name('search');
    Route::get('/messenger', 'messenger')->name('messenger');
});

Route::middleware(['auth', 'verified', 'online', 'lang'])->group(function () {
    Route::get('/away/{url}', [LinkController::class, 'away'])->name('away.link');
    Route::post('/upload/profile/image', [ImageController::class, 'upload_profile_image'])->name('image.upload');
    Route::delete('/image/{id}', [ImageController::class, 'destroy'])->name('image.destroy');
    Route::post('/links/data', [AttachmentController::class, 'get_links_data'])->name('links.data');
});

Route::middleware(['auth', 'verified', 'online', 'lang'])->controller(FileController::class)->group(function () {
    Route::post('/validate/files', 'validate_files')->name('files.validate');
    Route::post('/upload/files/{attachmentable_id}/{attachmentable_type}', 'upload')->name('files.upload');
});
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\UploadFileRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Image;
use App\Models\User;
use App\Http\Controllers\RedisController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use Intervention\Image\Laravel\Facades\Image as Picture;

class ImageController extends Controller
{
    private $redis;

    public function __construct()
    {
        if(!$this->redis) {
            $this->redis = new RedisController;
        }
    }

    public function store($request, $post_id, $post_type, $src, $home_page): Image
    {
        $data['user_id'] = $request->user()->id;
        $data['src'] = $src;
        $data['home_page'] = $home_page;

        if($post_id != "null" && $post_type != "null") {
            $data['imageable_id'] = $post_id;
            $data['imageable_type'] = $post_type;
        } else {
            $data['imageable_id'] = null;
            $data['imageable_type'] = null;
        }
        
        $image = $request->user()->images()->create($data);
        $this->redis->set_image($image);

        return $image;
    }

    public function destroy(Request $request, $id)
    {
        $image = Image::find($id);
        $this->authorize('delete', $image);

        $this->redis->delete_image($image->id);

        $this->delete_dependent_records($image->id);
        preg_match('/\/storage\/(.+)/', $image->src, $match);
        if($match[1]) {
            Storage::delete($match[1]);
        }
        $image->delete();

        if($request->wantsJson()) {
            return response()->json('destroyed')->header('Content-Type', 'application/json');
        }
    }

    public function destroy_all($id, $objectType): void
    {
        $images = Image::where([
            ['imageable_id', '=', $id],
            ['imageable_type', '=', $objectType]
        ])->get();

        if(!empty($images)) {
            foreach($images as $image) {
                $this->delete_dependent_records($image->id);
                $this->redis->delete_image($image->id);

                preg_match('/\/storage\/(.+)/', $image->src, $match);
                if($match[1]) {
                    Storage::delete($match[1]);
                }

                $image->delete();
            }
        }
    }

    private function delete_dependent_records($id): void
    {
        (new LikeController)->destroy_all($id, 'image');
        (new CommentController)->destroy_all($id, 'image');
    }

    public function upload_profile_image(UploadFileRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if(filled($validated['avatar'])) {
            $this->save_profile_image($validated['avatar'], 'avatar');
        } else {
            $this->save_profile_image($validated['cover'], 'cover');
        }

        return Redirect::route('home.index');
    }

    private function save_profile_image($file, $param): void
    {
        $user = User::select('id', $param)->where('id', Auth::user()->id)->first();

        if($user->$param) {
            Storage::delete("/$param" . "s/" . $user->$param);
        }

        $filename = Auth::user()->id . Str::random(1) . '.webp';
        $image = Picture::read(file_get_contents($file));
        $image = $image->toWebp(70);
        Storage::put("/{$param}s/$filename", $image);

        $user->$param = $filename;
        $user->save();

        $profile = $this->redis->get_user(Auth::user()->id);
        $profile->$param = $filename;
        $this->redis->set_user($profile);
    }
}
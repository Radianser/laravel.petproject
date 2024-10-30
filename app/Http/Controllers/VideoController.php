<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use App\Http\Controllers\RedisController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;

class VideoController extends Controller
{
    public function store($request, $post_id, $post_type, $src, $type, $spec, $embedded): Video
    {
        $data['user_id'] = $request->user()->id;
        $data['videoable_id'] = $post_id;
        $data['videoable_type'] = $post_type;
        $data['src'] = $src;
        $data['type'] = $type;
        $data['spec'] = $spec;
        $data['embedded'] = $embedded;
        
        $video = $request->user()->videos()->create($data);
        (new RedisController)->set_video($video);

        return $video;
    }

    public function destroy($id): void
    {
        $video = Video::find($id);
        $this->authorize('delete', $video);

        (new RedisController)->delete_video($video->id);

        $this->delete_dependent_records($video->id);
        preg_match('/\/storage\/(.+)/', $video->src, $match);
        if($match) {
            Storage::delete($match[1]);
        }
        $video->delete();
    }

    public function destroy_all($id, $objectType): void
    {
        $videos = Video::where([
            ['videoable_id', '=', $id],
            ['videoable_type', '=', $objectType]
        ])->get();

        if(!empty($videos)) {
            $redis = new RedisController;
            foreach($videos as $video) {
                $this->delete_dependent_records($video->id);
                $redis->delete_video($video->id);

                preg_match('/\/storage\/(.+)/', $video->src, $match);
                if($match[1]) {
                    Storage::delete($match[1]);
                }

                $video->delete();
            }
        }
    }

    private function delete_dependent_records($id): void
    {
        (new LikeController)->destroy_all($id, 'video');
        (new CommentController)->destroy_all($id, 'video');
    }
}

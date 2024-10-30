<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Models\User;
use App\Models\Babble;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Link;
use App\Models\Image;
use App\Models\Video;
use App\Models\File;
// use App\Models\Attachment;
use App\Http\Controllers\BabbleController;
use App\Http\Controllers\RedisController;
use Illuminate\Support\Facades\DB;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:go';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $data = 'Hello, World!';

        // $result = Cache::rememberForever('data', function() use($data) {
        //     return $data;
        // });

        // $users = User::all();

        // foreach($users as $user) {
        //     Cache::put('user:' . $user->id, $user);
        // }

        // $id = 3;
        // $user = Cache::get('user.' . 2);
        // $user = Cache::rememberForever('user.' . $id, function() use($id) {
        //     return User::find($id);
        // });

        // $users = Cache::get('users:all');
        // $users = Cache::rememberForever('users:all', function() {
        //     return User::all();
        // });

        // $result = '';
        // if(Cache::has('data')) {
        //     $result = Cache::get('data');
        // } else {
        //     $result = $data;
        // }

        // dd($user->name);
        // dd($users->pluck('name'));

        // Redis::set('user:' . 2, User::find(2));
        // $user = Redis::get('user:' . 2);
        // $user = json_decode($user);

        // $babbles = Babble::all();
        // $babbles = (new BabbleController)->babble_processing($babbles);

        // foreach($babbles as $babble) {
            // Redis::set('babbles:all', $babbles);
            // Cache::put('babbles:all', $babbles);
        // }

        // foreach($babbles as $babble) {
        //     // Redis::set('babbles:all', $babbles);
        //     // Cache::put("babble:$babble->id", $babble);
        //     Redis::set("babble:$babble->id", $babble);
        // }

        // dd($babbles);

        // $babbles = Cache::get('babbles:all');
        // dd($babbles);

        // $value = Cache::remember('users', 60, function () {
        //     $users = DB::table('users')->get();
        //     foreach($users as $user) {
        //         Cache::put("user:$user->id", $user, 60);
        //     }
        //     return $users;
        // });

        // $id = 2;
        // $value = Cache::remember("user:$id", 60, function () {
        //     return DB::table('users')->find($id);
        // });


        // for($i=0; $i<100; $i++) {
        //     Redis::setex("333:$i", 31536000, 'New Data!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
        // }

        // public function babble_processing($babbles)
        // {
        //     foreach($babbles as $babble) {
        //         $this->get_reply_message($babble);
        //         $this->get_all_replies($babble);
        //         $this->get_likes($babble);

        //         $babble->attachments = "";
        //     }

        //     return $babbles;
        // }

        // private function get_likes($babble)
        // {
        //     foreach($babble->likes as $key => $like) {
        //         $babble->likes[$key] = $like->user;
        //     }
        // }

        // private function get_reply_message($babble)
        // {
        //     $reply = null;
        //     if($babble->babble_id != null) {
        //         $reply = Babble::find($babble->babble_id);

        //         if($reply == null) {
        //             $reply = (object)[];
        //             $reply->user = User::select('id', 'name', 'image')->find($babble->babble_user_id);
        //         }
        //     }

        //     $babble->reply = $reply;
        // }

        // private function get_all_replies($babble) {
        //     $babbles = Babble::where('babble_id', $babble->id)->get();      
        //     $babble->replies = $babbles;  
        // }

        $redis = new RedisController;

        $files = File::all();
        foreach($files as $file) {
            $redis->set_file($file);
            // if($file->attachmentable_type == 'comment') {
            //     $file->attachmentable_type = 2;
            //     $file->save();
            // }
        }

        $links = Link::all();
        foreach($links as $link) {
            $redis->set_link($link);
            // if($link->attachmentable_type == 'comment') {
            //     $link->attachmentable_type = 2;
            //     $link->save();
            // }
        }

        $images = Image::all();
        foreach($images as $image) {
            $redis->set_image($image);
            // if($image->attachmentable_type == 'comment') {
            //     $image->attachmentable_type = 2;
            //     $image->save();
            // }
        }

        $videos = Video::all();
        foreach($videos as $video) {
            $redis->set_video($video);
            // if($video->attachmentable_type == 'comment') {
            //     $video->attachmentable_type = 2;
            //     $video->save();
            // }
        }
        
        $babbles = Babble::all();
        foreach($babbles as $babble) {
            $redis->set_post_message($babble);
        }

        $users = User::all();
        foreach($users as $user) {
            $redis->set_user($user);
        }

        $comments = Comment::all();
        foreach($comments as $comment) {
            $redis->set_comment($comment);
        }

        $likes = Like::all();
        foreach($likes as $like) {
            Redis::set("like:$like->id", $like);
            // if($like->likeable_type == 'comment') {
            //     $like->likeable_type = 2;
            //     $like->save();
            // }
        }
    }
}

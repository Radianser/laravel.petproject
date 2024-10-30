<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\Link;
use App\Http\Controllers\RedisController; 

class LinkController extends Controller
{
    public function away($url): RedirectResponse
    {
        $str = preg_replace('#%;#', '/', $url);
        $str = preg_replace('#``#', '?', $str);

        return redirect()->away($str);
    }
    
    public function store($request, $post_id, $post_type, $domain, $uri): void
    {
        $data['user_id'] = $request->user()->id;
        $data['linkable_id'] = $post_id;
        $data['linkable_type'] = $post_type;
        $data['domain'] = $domain;
        $data['uri'] = $uri;
        $data['title'] = $this->get_title($domain . $uri);
        
        $link = $request->user()->links()->create($data);

        (new RedisController)->set_link($link);
    }

    public function get_title($url): string
    {
        try {
            $content = file_get_contents($url);
            if($content) {
                $arr1 = explode('</title>', $content);
                $arr2 = explode('<title>', $arr1[0]);
            }
    
            if(!empty($arr2[1])) {
                $title = $arr2[1];
            } else {
                $title = $this->parse_link($url);
            }
        } catch(\Exception $exception) {
            $title = $this->parse_link($url);
        }

        return $title;
    }

    private function parse_link($url): string
    {
        $parsed_url = parse_url($url); 
        $path = $parsed_url['path']; 
        $text = basename($path);
        $title = ucfirst($text);

        return $title;
    }

    public function destroy($id): void
    {
        $link = Link::find($id);
        $this->authorize('delete', $link);
        (new RedisController)->delete_link($link->id);
        $link->delete();
    }

    public function destroy_all($id, $objectType): void
    {
        $links = Link::where([
            ['linkable_id', '=', $id],
            ['linkable_type', '=', $objectType]
        ])->get();

        if(!empty($links)) {
            $redis = new RedisController;
            foreach($links as $link) {
                $redis->delete_link($link->id);
                $link->delete();
            }
        }
    }
}
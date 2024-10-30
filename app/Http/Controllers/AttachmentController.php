<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\FileController;

class AttachmentController extends Controller
{
    public function index($request, $post_id, $post_type): void
    {
        $add_links = $request->add_links;
        $delete_attachments = $request->delete_attachments;

        if(!empty($add_links)) {
            $this->add_links($request, $post_id, $post_type, $add_links);
        }
        if(!empty($delete_attachments)) {
            $this->delete_attachments($request, $delete_attachments);
        }
    }

    private function get_headers($param): array
    {
        try {
            $headers = get_headers($param, true);
        } catch(\Exception $exception) {
            $headers = [];
        }
        return $headers;
    }

    private function get_content_type($headers): array
    {
        foreach($headers as $key => $param) {
            if (strcasecmp($key, 'Content-Type') == 0) {
                $headers['Content-Type'] = $param;
            }
        }

        if(is_array($headers['Content-Type'])) {
            if(isset($headers['Content-Type'][1])) {
                $headers['Content-Type'] = $headers['Content-Type'][1];
            } else {
                $headers['Content-Type'] = $headers['Content-Type'][0];
            }
        }

        return $headers;
    }

    public function get_links_data(Request $request): JsonResponse
    {
        $links = $request->links;
        $response = [];

        foreach($links as $link) {
            $link = (object)$link;

            if(empty($link->type)) {
                $headers = $this->get_headers($link->src);
                if(!empty($headers)) {
                    $headers = $this->get_content_type($headers);
                } else {
                    $headers['Content-Type'] = 'text/plain';
                }

                preg_match('/([a-z]+)\/([^;\s]+)/', $headers['Content-Type'], $match);
                $obj = (object)[];
                $obj->id = $link->id;
                $obj->src = $link->src;
                $obj->type = $match[1];
                $obj->spec = $match[2];
                $obj->domain = $link->domain;
                $obj->uri = $link->uri;
                $obj->file = $link->file;
                $obj->title = (new LinkController)->get_title($obj->src);

                $response[] = $obj;
            } else {
                $response[] = $link;
            }
        }

        return response()->json($response)->header('Content-Type', 'application/json');
    }

    private function add_links($request, $post_id, $post_type, $add_links): void
    {
        foreach($add_links as $key => $link) {
            switch($link['type']) {
                case 'text':
                    (new LinkController)->store($request, $post_id, $post_type, $link['domain'], $link['uri']);
                break;

                case 'image':
                    (new ImageController)->store($request, $post_id, $post_type, $link['src'], 0);
                break;

                case 'video':
                    (new VideoController)->store($request, $post_id, $post_type, $link['src'], null, null, true);
                break;

                default:
                    (new FileController)->store($request, $post_id, $post_type, $link['src'], $link['title'], $link['type'], $link['spec']);
                break;
            }
        }
    }

    private function delete_attachments($request, $delete_attachments): void
    {
        foreach($delete_attachments as $attachment) {
            switch($attachment['type']) {
                case 'links':
                    (new LinkController)->destroy($attachment['object_id']);
                break;

                case 'images':
                    (new ImageController)->destroy($request, $attachment['object_id']);
                break;

                case 'videos':
                    (new VideoController)->destroy($attachment['object_id']);
                break;

                default:
                    (new FileController)->destroy($attachment['object_id']);
                break;
            }
        }
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Exceptions\UploadFailedException;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\RedisController;
use App\Models\File;
use Intervention\Image\Laravel\Facades\Image;

class FileController extends Controller
{
    private $redis;

    public function __construct()
    {
        if(!$this->redis) {
            $this->redis = new RedisController;
        }
    }

    public function store($request, $post_id, $post_type, $src, $title, $type, $spec, $size = null, $original_title = null, $ext = null): File
    {
        $data['user_id'] = $request->user()->id;
        $data['fileable_id'] = $post_id;
        $data['fileable_type'] = $post_type;
        $data['src'] = $src;
        $data['title'] = $title;
        $data['content_type'] = "$type/$spec";
        $data['size'] = $size;
        $data['extension'] = $ext;

        if(isset($original_title)) {
            $data['original_title'] = $original_title;
        } else {
            $data['original_title'] = $title;
        }
        
        $file = $request->user()->files()->create($data);
        $this->redis->set_file($file);

        return $file;
    }

    public function upload(Request $request, $attachmentable_id, $attachmentable_type): JsonResponse
    {
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));
        if ($receiver->isUploaded() === false) {
            $localization =  __('localization');
            throw new UploadMissingFileException($localization['upload_error']);
        }

        $save = $receiver->receive();
        
        if ($save->isFinished()) {
            $response = $this->save_file($request, $attachmentable_id, $attachmentable_type, $save->getFile());
            $tempPath = storage_path("app\\chunks\\");
            $tempItems = array_diff(scandir($tempPath), ['.', '..']);

            foreach($tempItems as $item) {
                unlink($tempPath . $item);
            }

            return $response;
        }

        $handler = $save->handler();

        return response()->json([
            "done" => $handler->getPercentageDone(),
            'status' => true
        ]);
    }

    private function save_file($request, $attachmentable_id, $attachmentable_type, $file): JsonResponse
    {
        $fileName = md5($file->getClientOriginalName() . time());
        $extension = $file->getClientOriginalExtension();
        $mime = explode('/', $file->getMimeType());
        $type = $mime[0];
        $spec = $mime[1];

        if($attachmentable_id != "null" && $attachmentable_type != "null") {
            $home_page = false;
        } else {
            $home_page = true;
        }
        
        // Вариант без конвертации изображений:

        // $filePath = "{$type}s";
        // $src = "/storage/$filePath/$fileName";
        // Storage::put("/$filePath/$fileName", file_get_contents($file));

        $filePath = "{$type}s";
        $item = file_get_contents($file);
        
        if($type === 'image') {
            $arr = ['jpg', 'jpeg', 'png'];

            if(in_array($extension, $arr)) {
                $fileName .= '.webp';
                $image = Image::read($item);
                $item = $image->toWebp(70);
            } else {
                $fileName .= $extension;
            }
        }

        $src = "/storage/$filePath/$fileName";
        Storage::put("/$filePath/$fileName", $item);

        switch($type) {
            case 'image':
                $data = (new ImageController)->store($request, $attachmentable_id, $attachmentable_type, $src, $home_page);
            break;

            case 'video':
                $data = (new VideoController)->store($request, $attachmentable_id, $attachmentable_type, $src, $type, $spec, false);
            break;

            default:
                $data = $this->store($request, $attachmentable_id, $attachmentable_type, $src, $fileName, $type, $spec, $file->getSize(), $file->getClientOriginalName(), '.' . $file->getClientOriginalExtension());
            break;
        }

        // $data->user = $this->redis->get_user($data->user_id);

        return response()->json([
            'data' => $data,
            'mime_type' => $file->getMimeType()
        ])->header('Content-Type', 'application/json');
    }

    public function validate_files(Request $request): JsonResponse
    {
        $files = $request->all();
        $response = [
            'approved_keys' => [],
            'declined_keys' => [],
            'errors' => []
        ];
        $rules = 'max:204800';

        if($request->header('gallery')) {
            $rules = 'mimes:jpg,jpeg,png,bmp,webp,gif|max:204800';
        }

        foreach($files as $key => $file) {
            $validator = Validator::make($files, [$key => $rules]);

            if ($validator->fails()) {
                $error = $validator->getMessageBag()->toArray()[$key][0];
                $response['errors'][] = $error;
                $response['declined_keys'][] = $key;
            } else {
                $response['approved_keys'][] = $key;
            }
        }

        return response()->json($response)->header('Content-Type', 'application/json');
    }

    public function destroy($id): void
    {
        $file = File::find($id);
        $this->authorize('delete', $file);

        $this->redis->delete_file($file->id);

        preg_match('/\/storage\/(.+)/', $file->src, $match);
        if($match[1]) {
            Storage::delete($match[1]);
        }
        $file->delete();
    }

    public function destroy_all($id, $objectType): void
    {
        $files = File::where([
            ['fileable_id', '=', $id],
            ['fileable_type', '=', $objectType]
        ])->get();

        if(!empty($files)) {
            foreach($files as $file) {
                $this->redis->delete_file($file->id);

                preg_match('/\/storage\/(.+)/', $file->src, $match);
                if($match[1]) {
                    Storage::delete($match[1]);
                }

                $file->delete();
            }
        }
    }
}
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
// use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\App;

class CommentUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(FormRequest $request): array
    {
        App::setLocale($request->user()->language);
        
        return [
            'user_id' => 'required|integer',
            'commentable_id' => 'required|integer',
            'commentable_type' => 'required|string',
            'message' => [
                Rule::requiredIf(fn () => $request->links == null && $request->images == null && $request->videos == null && $request->docs == null && $request->add_files == null && $request->add_links == null),
                'nullable',
                'string',
                'max:1000'
            ],
            'links' => 'nullable|array|max:10',
            'images' => 'nullable|array|max:10',
            'videos' => 'nullable|array|max:10',
            'docs' => 'nullable|array|max:10',
            'add_files' => [
                'nullable',
                'array',
                'max:10'
            ],
            'add_links' => [
                'nullable',
                'array',
                'max:10'
            ],
            'delete_attachments' => 'nullable|array'
        ];
    }
}

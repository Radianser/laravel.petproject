<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
// use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\App;

class CommentStoreRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(FormRequest $request): array
    {
        App::setLocale($request->user()->language);
        
        return [
            'user_id' => 'required|integer',
            'commentable_id' => 'required|integer',
            'commentable_type' => 'required|string',
            'message' => [
                'nullable',
                Rule::requiredIf(fn () => $request->add_files == null && $request->add_links == null),
                'string',
                'max:1000'
            ],
            'add_files' => [
                'nullable',
                'array',
                'max:10'
            ],
            'add_links' => [
                'nullable',
                'array',
                'max:10'
            ]
        ];
    }
}

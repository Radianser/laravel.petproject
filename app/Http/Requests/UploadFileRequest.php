<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\App;

class UploadFileRequest extends FormRequest
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
            'avatar' => [
                Rule::requiredIf(fn () => $request->cover == null),
                'nullable',
                'file',
                'max:2048',
                File::image()
                    ->max(2 * 1024)
            ],
            'cover' => [
                Rule::requiredIf(fn () => $request->avatar == null),
                'nullable',
                'file',
                'max:2048',
                File::image()
                    ->max(2 * 1024)
            ],
        ];
    }
}

<?php

namespace App\Http\Requests\Media;

use App\Enums\LevelEnum;
use Illuminate\Validation\Rule;
use App\Http\Requests\BaseApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class UploadMediaRequest extends BaseApiRequest
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
    public function rules(): array
    {
        return [
            'images' => ['nullable' , 'array'],
            'images.*'                  => [
                'nullable' ,
                'mimes:jpeg,jpg,png,webp,svg' ,
                'max:4096'
            ],

            'videos' => ['nullable' , 'array'],
            'videos.*'                      => [
                'nullable',
                'mimes:avi,mpeg,quicktime,mp4,mov,wmv',
                'max:51200'
            ],
            'files' => ['nullable' , 'array'],
            'files.*'                   => [
                'nullable',
                'file',
                'max:5120',
                'mimes:pdf'
            ],
            'context_type'              => ['required', 'required_with:context_id', Rule::in(LevelEnum::getValues())],
            'context_id'                => ['required', 'required_with:context_type'],
        ];
    }
}

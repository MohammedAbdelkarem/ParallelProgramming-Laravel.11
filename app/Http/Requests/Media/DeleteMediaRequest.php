<?php

namespace App\Http\Requests\Media;

use App\Enums\LevelEnum;
use Illuminate\Validation\Rule;
use App\Http\Requests\BaseApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class DeleteMediaRequest extends BaseApiRequest
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
            'ids' => ['required' , 'array'],
            'ids.*' => ['required' , 'exists:media,id']
        ];
    }
}

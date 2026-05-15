<?php

namespace App\Http\Requests\Finance;

use App\Enums\ActorTypeEnum;
use App\Http\Requests\BaseApiRequest;
use Illuminate\Validation\Rule;

class TransferRequest extends BaseApiRequest
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
            'to_id' => ['required'],
            // 'to_type' => ['required' , Rule::in(ActorTypeEnum::values())],
            'amount' => ['required', 'numeric', 'min:1'],
            'order_id' => ['nullable', 'exists:orders,id'],
            'notes' => ['nullable', 'string'],
        ];
    }
}

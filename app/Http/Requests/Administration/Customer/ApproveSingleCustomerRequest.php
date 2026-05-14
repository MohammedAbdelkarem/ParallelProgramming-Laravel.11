<?php

namespace App\Http\Requests\Administration\Customer;

use App\Http\Requests\BaseApiRequest;
use App\Rules\CustomerActiveRule;

class ApproveSingleCustomerRequest extends BaseApiRequest
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
            'customer_id' => ['required', 'exists:customers,id', new CustomerActiveRule()],
            // single_customer_personal_infos table
            'national_id_number' => ['required', 'string', 'unique:single_customer_personal_infos,national_id_number'],
            'full_name' => ['required', 'string'],
            'birth_date' => ['required', 'date'],
            'nationality' => ['required', 'string'],
            'residency_address_city' => ['required', 'string'],
            'residency_address_street' => ['required', 'string'],
            'residency_address_district' => ['required', 'string'],
            'id_expiration_date' => ['required', 'date'],
            'single_customer_personal_info_approved' => ['required', 'boolean' , 'accepted'],
        ];
    }
}

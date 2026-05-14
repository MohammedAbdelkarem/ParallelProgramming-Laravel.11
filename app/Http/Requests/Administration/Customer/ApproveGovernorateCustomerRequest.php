<?php

namespace App\Http\Requests\Administration\Customer;

use App\Http\Requests\BaseApiRequest;
use App\Rules\GovernorateCustomerActiveRule;

class ApproveGovernorateCustomerRequest extends BaseApiRequest
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

            'customer_id' => ['required', 'exists:customers,id', new GovernorateCustomerActiveRule()],
            /*
            |--------------------------------------------------------------------------
            | Governorate Official Registration
            |--------------------------------------------------------------------------
            */
            'official_record_number' => ['nullable', 'string'],
            'entity_name'            => ['required', 'string'],
            'issue_date'             => ['required', 'date'],
            'expiry_date'            => ['nullable', 'date'],
            /*
            |--------------------------------------------------------------------------
            | Governorate Ministerial Decision
            |--------------------------------------------------------------------------
            */
            'decision_number'     => ['required', 'string'],
            'documentation_date'  => ['required', 'date'],
            'decision_issue_date' => ['required', 'date'],
            /*
            |--------------------------------------------------------------------------
            | Governorate Authorized Employee
            |--------------------------------------------------------------------------
            */
            'authorized_employee_name'    => ['required', 'string'],
            'id_or_passport_number'       => ['required', 'string'],
            'authorization_date'          => ['required', 'date'],
            'authorization_duration_days' => ['required', 'integer'],
            'authorization_letter'        => ['required', 'string'],
        ];
    }
}

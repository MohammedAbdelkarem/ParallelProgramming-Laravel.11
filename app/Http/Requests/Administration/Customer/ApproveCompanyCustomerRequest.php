<?php

namespace App\Http\Requests\Administration\Customer;

use App\Http\Requests\BaseApiRequest;
use App\Rules\CompanyCustomerActiveRule;

class ApproveCompanyCustomerRequest extends BaseApiRequest
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
            'customer_id' => ['required', 'exists:customers,id', new CompanyCustomerActiveRule()],
            // company_customer_personal_infos table
            'commercial_registration_number' => ['required', 'string'],
            'company_name' => ['required', 'string'],
            'commercial_activity' => ['required', 'string'],
            'commercial_issue_date' => ['required', 'date'],
            'commercial_expiry_date' => ['required', 'date'],
            'vat_id' => ['nullable', 'string'],
            'vat_issued_date' => ['nullable', 'date'],
            'operating_card_number' => ['required', 'string'],
            'address_city' => ['required', 'string'],
            'address_street' => ['required', 'string'],
            'address_district' => ['required', 'string'],
            'iban' => ['required', 'string'],
            'bank_name' => ['required', 'string'],
            'additional_bank_info' => ['nullable', 'string'],
            'company_customer_personal_info_approved' => ['required', 'boolean', 'accepted'],
        ];
    }
}

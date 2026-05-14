<?php

namespace App\Http\Requests\Administration\CustomClearenceCompany;

use App\Http\Requests\BaseApiRequest;
use App\Rules\CustomClearenceCompanyActiveRule;
use App\Rules\DriverCompanyActiveRule;

class ApproveCustomClearenceCompanyRequest extends BaseApiRequest
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
            'custom_clearence_company_id' => ['required', 'exists:custom_clearence_companies,id', new CustomClearenceCompanyActiveRule()],
            
            // commercial registeration
            'commercial_registration_number' => ['required', 'string'],
            'company_name' => ['required', 'string'],
            'issue_date' => ['required', 'date'],
            'expiry_date' => ['required', 'date'],
            'commercial_activity' => ['required', 'string'],

            // tax certificate
            'certificate_number' => ['nullable', 'string'],
            'tax_issue_date' => ['nullable', 'date'],

            // faseh doc 
            'facility_number' => ['required', 'string'],
            'faseh_account_status' => ['required', 'string'],

            // clearence license
            'license_number' => ['required', 'string'],
            'license_issue_date' => ['required', 'date'],
            'license_expiry_date' => ['required', 'date'],
            'activity_type' => ['required', 'string'],
        ];
    }
}

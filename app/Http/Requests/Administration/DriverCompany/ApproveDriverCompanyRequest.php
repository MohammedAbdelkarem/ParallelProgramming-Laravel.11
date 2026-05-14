<?php

namespace App\Http\Requests\Administration\DriverCompany;

use Illuminate\Validation\Rule;
use App\Http\Requests\BaseApiRequest;
use App\Rules\DriverCompanyActiveRule;
use App\Enums\DriverCompanyTransportLicenseEnum;

class ApproveDriverCompanyRequest extends BaseApiRequest
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
            'driver_company_id' => ['required', 'exists:drivers_companies,id', new DriverCompanyActiveRule()],
            
            // transport_licenses table
            'license_type' => ['required', Rule::in(DriverCompanyTransportLicenseEnum::values())],
            'license_number' => ['required', 'string'],
            'issue_date' => ['required', 'date'],
            'expiry_date' => ['required', 'date'],
            'transport_activity_license' => ['required', 'string'],
            'transport_license_approved' => ['required', 'boolean', 'accepted'],

            // invoices_addresses table
            'address_city' => ['required', 'string'],
            'address_district' => ['required', 'string'],
            'address_street' => ['required', 'string'],
            'postal_code' => ['required', 'string'],
            'invoices_address_approved' => ['required', 'boolean', 'accepted'],

            // vat_cards table
            'vat_id' => ['required', 'string'],
            'vat_issued_date' => ['required', 'date'],
            'vat_card_approved' => ['required', 'boolean', 'accepted'],

            // commercial_registerations table
            'commercial_registration_number' => ['required', 'string'],
            'company_name' => ['required', 'string'],
            'commercial_activity' => ['required', 'string'],
            'commercial_issue_date' => ['required', 'date'],
            'commercial_expiry_date' => ['required', 'date'],
            'commercial_registeration_approved' => ['required', 'boolean', 'accepted'],

            // bank_informations table
            'bank_name' => ['required', 'string'],
            'account_number' => ['required', 'string'],
            'verified_iban' => ['required', 'string'],
            'account_holder_name' => ['required', 'string'],
            'bank_issue_date' => ['required', 'date'],
            'bank_information_approved' => ['required', 'boolean', 'accepted'],
        ];
    }
}

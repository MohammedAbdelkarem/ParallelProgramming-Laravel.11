<?php

namespace App\Http\Requests\Users\Auth;

use App\Enums\GenderEnum;
use App\Rules\PhoneNumberRule;
use App\Rules\ShiftsOverlappingRule;
use Illuminate\Validation\Rule;
use App\Http\Requests\BaseApiRequest;

class AuthRequest extends BaseApiRequest
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
        return match ($this->route()->getActionMethod()) {
            "registerSaudiDriver" => $this->registerSaudiDriverRules(),
            "loginDriver" => $this->loginDriverRules(),
            "registerSingleCustomer" => $this->registerSingleCustomerRules(),
            "loginCustomer" => $this->loginCustomerRules(),
            "registerCompanyCustomer" => $this->registerCompanyCustomerRules(),
            "registerGovernorateCustomer" => $this->registerGovernorateCustomerRules(),
            "registerDriverCompany" => $this->registerDriverCompanyRules(),
            "loginDriverCompany" => $this->loginDriverCompanyRules(),
            "registerSaudiDriverEmployee" => $this->registerSaudiDriverEmployeeRules(),//for driver company
            "registerNonSaudiDriverEmployee" => $this->registerNonSaudiDriverEmployeeRules(),//for driver company
            "registerCustomClearenceCompany" => $this->registerCustomClearenceCompanyRules(),
            "loginCustomClearenceCompany" => $this->loginCustomClearenceCompanyRules(),
        };
    }

    public function registerSaudiDriverRules()
    {
        return [
            'phone_number' => ['required', new PhoneNumberRule() , Rule::unique('users' , 'phone_number')->where('role_id' , 3)],
            'email' => ['required', 'email', Rule::unique('users' , 'email')->where('role_id' , 3)],
            'national_card_front' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'national_card_back' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'driving_license' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'owner_card' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'operating_card' => ['required' , 'image' , 'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'operating_card_number' => ['required' , 'string'],
            'insurance_card' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'residency_card' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'profile_image' => ['nullable',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'referral_code' => ['nullable', 'string'],
        ];
    }

    public function registerSaudiDriverEmployeeRules()
    {
        return [
            'driver_company_id' => ['nullable', 'exists:drivers_companies,id'],
            'custom_clearence_company_id' => ['nullable', 'exists:drivers_companies,id'],
            'phone_number' => ['required', new PhoneNumberRule() , Rule::unique('users' , 'phone_number')->where('role_id' , 3)],
            'email' => ['required', 'email', Rule::unique('users' , 'email')->where('role_id' , 3)],
            'national_card_front' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'national_card_back' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'driving_license' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'owner_card' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'operating_cards' => ['required', 'array', 'min:1'],
            'operating_cards.*.file' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'operating_cards.*.operating_card_number' => ['required','string'],
            'insurance_card' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'residency_card' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'profile_image' => ['nullable',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'company_commercial_registration_number' => ['nullable', 'string'],
            'referral_code' => ['nullable', 'string'],
        ];
    }
    public function registerNonSaudiDriverEmployeeRules()
    {
        return [
            'driver_company_id' => ['nullable', 'exists:drivers_companies,id'],
            'custom_clearence_company_id' => ['nullable', 'exists:custom_clearence_companies,id'],
            'phone_number' => ['required', new PhoneNumberRule() , Rule::unique('users' , 'phone_number')->where('role_id' , 3)],
            'email' => ['required', 'email', Rule::unique('users' , 'email')->where('role_id' , 3)],
            'passport_card' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'driving_license' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'operating_cards' => ['required', 'array', 'min:1'],
            'operating_cards.*.file' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'operating_cards.*.operating_card_number' => ['required','string'],
            'insurance_card' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'profile_image' => ['nullable',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
        ];
    }

    public function registerDriverCompanyRules()
    {
        return [
            'phone_number' => ['required', new PhoneNumberRule()],
            'email' => ['required', 'email'],
            'profile_image' => ['nullable',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'transport_license' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'vat_card' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'commercial_registeration' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'commercial_registeration_number' => ['required', 'string'],
            'invoices_address' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'bank_information' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
        ];
    }

    public function registerCustomClearenceCompanyRules()
    {
        return [
            'phone_number' => ['required', new PhoneNumberRule() , Rule::unique('users' , 'phone_number')->where('role_id' , 6)],
            'email' => ['required', 'email', Rule::unique('users' , 'email')->where('role_id' , 5)],
            'commercial_registeration' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'faseh_document' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'tax_certificate' => ['nullable',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'custom_clearenece_license' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'additional_docs' => ['nullable', 'array'],
            'additional_docs.*.file' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'additional_docs.*.doc_title' => ['required', 'string'],
            'additional_docs.*.doc_description' => ['nullable', 'string'],
        ];
    }

    public function loginDriverCompanyRules()
    {
        return [
            'phone_number' => ['required', new PhoneNumberRule() , Rule::exists('users' , 'phone_number')->where('role_id' , 5)],
        ];
    }
    public function loginCustomClearenceCompanyRules()
    {
        return [
            'phone_number' => ['required', new PhoneNumberRule() , Rule::exists('users' , 'phone_number')->where('role_id' , 6)],
        ];
    }

    public function registerSingleCustomerRules()
    {
        return [
            'phone_number' => ['required', new PhoneNumberRule() , Rule::unique('users' , 'phone_number')->where('role_id' , 4)],
            'email' => ['required', 'email', Rule::unique('users' , 'email')->where('role_id' , 4)],
            'national_card_front' => ['required', 'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'national_card_back' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'profile_image' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
        ];
    }
    public function registerCompanyCustomerRules(): array
    {
        return [
            'phone_number' => ['required', new PhoneNumberRule() , Rule::unique('users' , 'phone_number')->where('role_id' , 4)],
            'email' => ['required', 'email', Rule::unique('users' , 'email')->where('role_id' , 4)],
            'commercial_registration_card' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'vat_card' => ['nullable',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            // 'operating_card_number' => ['required', 'string'],
            'address_city' => ['required', 'string'],
            'address_street' => ['required', 'string'],
            'address_district' => ['required', 'string'],
            'iban' => ['required', 'string'],
            'bank_name' => ['required', 'string'],
            'additional_bank_info' => ['nullable', 'string'],
            'additional_docs' => ['nullable', 'array'],
            'additional_docs.*.file' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'additional_docs.*.doc_title' => ['required', 'string'],
            'additional_docs.*.doc_description' => ['nullable', 'string'],
        ];
    }
    public function registerGovernorateCustomerRules(): array
    {
        return [
            'phone_number' => ['required', new PhoneNumberRule() , Rule::unique('users' , 'phone_number')->where('role_id' , 4)],
            'email' => ['required', 'email', Rule::unique('users' , 'email')->where('role_id' , 4)],
            'address' => ['required', 'string'],
            'governorate_official_registeration' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'governorate_ministerial_decision' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'governorate_authorized_employee' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'additional_docs' => ['nullable', 'array'],
            'additional_docs.*.file' => ['required',  'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:20480'],
            'additional_docs.*.document_type' => ['required', 'string'],
            'additional_docs.*.license_number' => ['required', 'string'],
            'additional_docs.*.issue_date' => ['nullable', 'date'],
            'additional_docs.*.expiry_date' => ['nullable', 'date'],
        ];
    }
    public function loginDriverRules()
    {
        return [
            'phone_number' => ['required', new PhoneNumberRule() , Rule::exists('users' , 'phone_number')->where('role_id' , 3)],
        ];
    }
    public function loginCustomerRules()
    {
        return [
            'phone_number' => ['required', new PhoneNumberRule() , Rule::exists('users' , 'phone_number')->where('role_id' , 4)],
        ];
    }

    public function messages()
    {
        return [];
    }
}

<?php

namespace App\Http\Requests\Administration\Driver;

use App\Rules\DriverActiveRule;
use App\Http\Requests\BaseApiRequest;

class ApproveNonSaudiEmployeeJoinRequest extends BaseApiRequest
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
            'driver_id' => ['required', 'exists:drivers,id', new DriverActiveRule()],

            // driver_personal_infos table
            'national_id_number' => ['required', 'string', 'unique:driver_personal_infos,national_id_number'],
            'full_name' => ['required', 'string'],
            'birth_date' => ['required', 'date'],
            'nationality' => ['required', 'string'],
            'id_expiration_date' => ['required', 'date'],
            'driver_personal_info_approved' => ['required', 'boolean', 'accepted'],

            //driver_passports_table
            'passport_number' => ['required', 'string'],
            'passport_holder_name' => ['required', 'string'],
            'passport_issue_date' => ['required', 'date'],
            'passport_expiration_date' => ['required', 'date'],
            'residency_number' => ['required', 'string'],
            'residency_issue_date' => ['required', 'date'],
            'residency_expiration_date' => ['required', 'date'],
            'driver_passport_approved' => ['required', 'boolean', 'accepted'],
            
            // driver_licenses table
            'driving_license_number' => ['required', 'string', 'unique:driver_licenses,driving_license_number'],
            'license_type' => ['required', 'string'],
            'license_issue_date' => ['required', 'date'],
            'license_expiry_date' => ['required', 'date'],
            'license_issuing_authority' => ['required', 'string'],
            'driver_license_approved' => ['required', 'boolean', 'accepted'],

            // driver_operating_cards table (multiple cards)
            'operating_cards' => ['required', 'array', 'min:1'],

            'operating_cards.*.operating_card_number' => [
                'required',
                'string',
                // 'unique:driver_operating_cards,operating_card_number'
            ],

            'operating_cards.*.operating_plate_number' => ['required', 'string'],
            'operating_cards.*.operating_authorized_activity_type' => ['required', 'string'],
            'operating_cards.*.operating_issue_date' => ['required', 'date'],
            'operating_cards.*.operating_expiration_date' => ['required', 'date'],
            'operating_cards.*.driver_operating_card_approved' => ['required', 'boolean', 'accepted'],

            // driver_insurances table
            'insurance_company' => ['required', 'string'],
            'insurance_policy_number' => ['required', 'string', 'unique:driver_insurances,insurance_policy_number'],
            'insurance_type' => ['required', 'string'],
            'insurance_start_date' => ['required', 'date'],
            'insurance_end_date' => ['required', 'date'],
            'driver_insurance_approved' => ['required', 'boolean', 'accepted'],
        ];
    }
}

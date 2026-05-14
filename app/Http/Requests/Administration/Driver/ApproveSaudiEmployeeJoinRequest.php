<?php

namespace App\Http\Requests\Administration\Driver;

use Illuminate\Validation\Rule;
use App\Http\Requests\BaseApiRequest;
use App\Enums\ResidencyProofeTypeEnum;
use App\Rules\DriverActiveRule;

class ApproveSaudiEmployeeJoinRequest extends BaseApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

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

            // driver_licenses table
            'driving_license_number' => ['required', 'string', 'unique:driver_licenses,driving_license_number'],
            'license_type' => ['required', 'string'],
            'license_issue_date' => ['required', 'date'],
            'license_expiry_date' => ['required', 'date'],
            'license_issuing_authority' => ['required', 'string'],
            'driver_license_approved' => ['required', 'boolean', 'accepted'],

            // driver_vehicle_ownerships table
            'owner_name' => ['required', 'string'],
            'owner_national_id' => ['required', 'string'],
            'owner_vehicle_type' => ['required', 'string'],
            'owner_plate_number' => ['required', 'string'],
            'owner_vehicle_serial_number' => ['required', 'string', 'unique:driver_vehicle_ownerships,owner_vehicle_serial_number'],
            'owner_issue_date' => ['required', 'date'],
            'driver_vehicle_ownership_approved' => ['required', 'boolean', 'accepted'],

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

            // driver_residency_proofs table
            'residency_proof_type' => ['required', Rule::in(ResidencyProofeTypeEnum::values())],
            'residency_proof_owner_name' => ['required', 'string'],
            'residency_address_city' => ['required', 'string'],
            'residency_address_street' => ['required', 'string'],
            'residency_address_district' => ['required', 'string'],
            'residency_issue_date' => ['required', 'date'],
            'driver_residency_proof_approved' => ['required', 'boolean', 'accepted'],
        ];
    }
}

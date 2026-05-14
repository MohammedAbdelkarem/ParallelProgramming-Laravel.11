<?php

namespace App\Http\Requests\Administration\ZATCA;

use App\Http\Requests\BaseApiRequest;

class GenerateZatcaCredentialRequest extends BaseApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'otp'                      => ['required', 'string'],
            'email_address'            => ['required', 'email'],
            'common_name'              => ['required', 'string'],
            'organizational_unit_name' => ['required', 'string'],
            'organization_name'        => ['required', 'string'],
            'tax_number'               => ['required', 'string'],
            'registered_address'       => ['required', 'string'],
            'business_category'        => ['required', 'string'],
            'registration_number'      => ['required', 'string'],
            'egs_serial_number'        => ['nullable', 'string'],
            'environment'              => ['nullable', 'in:local,simulation,production'],
        ];
    }
}

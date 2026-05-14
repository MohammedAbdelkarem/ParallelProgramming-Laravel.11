<?php

namespace App\Http\Requests\System;

use App\Http\Requests\BaseApiRequest;
use App\Models\System\SystemSetting;

class SystemSettingRequest extends BaseApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->route()->getActionMethod()) {
            "update" => $this->updateRules(),
        };
    }

    public function updateRules()
    {
        SystemSetting::findOrFail($this->id);

        return match ((int)$this->id) {
            1 => $this->UsdToSpRules(),
            2 => $this->BannerLiveTimeRules(),
            3 => $this->ReelLiveTimeRules(),
        };
    }

    public function UsdToSpRules()
    {
        return [
            "value" => ["required", "numeric", "between:1,999999999"],
        ];
    }

    public function BannerLiveTimeRules()
    {
        return [
            "value" => ["required", "numeric", "between:1,10000"],
        ];
    }

    public function ReelLiveTimeRules()
    {
        return [
            "value" => ["required", "numeric", "between:1,10000"],
        ];
    }

    public function messages()
    {
        return [];
    }
}

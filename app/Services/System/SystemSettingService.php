<?php

namespace App\Services\System;

use App\Models\System\SystemSetting;
use App\Services\MainService;
use Illuminate\Support\Facades\Cache;

/**
 * Class SystemSettingService.
 */
class SystemSettingService extends MainService
{
    public function index()
    {
        return SystemSetting::orderBy('id')->with("updated_by")->get();
    }

    public function update($id, $validatedData)
    {
        $feature = SystemSetting::findOrFail($id);
        $feature->value = $validatedData["value"];
        $feature->update_by = auth()->id();
        $feature->save();
    }
}

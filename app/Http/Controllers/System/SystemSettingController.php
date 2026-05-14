<?php

namespace App\Http\Controllers\System;

use App\Constants\ApiMessages;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\System\SystemSetting;
use App\Services\System\SystemSettingService;
use Illuminate\Http\Request;
use App\Http\Requests\System\SystemSettingRequest;
use App\Http\Resources\System\SystemSettingResource;

class SystemSettingController extends Controller
{
    public function __construct(
        protected SystemSettingService $systemSettingService
    ) {}

    public function index(): JsonResponse
    {
        return success(
            $this->systemSettingService->index(),
            ApiMessages::MSG_SUCCESS,
            SystemSettingResource::class,
        );
    }

    public function update(Request $request,$id)
    {
        return success(
            $this->systemSettingService->update($id, $request->all()),
            ApiMessages::MSG_UPDATED
        );
    }
}

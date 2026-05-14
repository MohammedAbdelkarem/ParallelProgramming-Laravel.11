<?php

namespace App\Http\Controllers\Users\Profile;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Profile\NumberUpdateRequest;
use App\Services\Users\Profile\NumberUpdateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NumberUpdateController extends Controller
{
    public function __construct(
        protected NumberUpdateService $service
    ) {}

    public function updateNumber(NumberUpdateRequest $request): JsonResponse
    {
        return success(
            $this->service->updateNumber($request->validated()),
            ApiMessages::MSG_OTP_CODE_SENDED_SUCCESSFULLY,
        );
    }

    public function verifyNumber(NumberUpdateRequest $request): JsonResponse
    {
        return createdSuccess(
            $this->service->verifyNumber($request->validated()),
            ApiMessages::MSG_UPDATED
        );
    }
}
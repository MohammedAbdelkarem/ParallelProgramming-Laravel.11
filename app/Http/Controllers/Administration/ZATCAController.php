<?php

namespace App\Http\Controllers\Administration;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\ZATCA\GenerateZatcaCredentialRequest;
use App\Services\ZATCA\ZATCACredentialService;

class ZATCAController extends Controller
{
    public function __construct(
        protected ZATCACredentialService $zatcaCredentialService
    ) {}

    public function generate(GenerateZatcaCredentialRequest $request)
    {
        return success(
            $this->zatcaCredentialService->generate($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function getActive()
    {
        return success(
            $this->zatcaCredentialService->getActive(),
            ApiMessages::MSG_SUCCESS,
        );
    }
}

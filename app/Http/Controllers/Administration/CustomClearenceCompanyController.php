<?php

namespace App\Http\Controllers\Administration;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\CustomClearenceCompany\ApproveCustomClearenceCompanyRequest;
use App\Http\Requests\Administration\CustomClearenceCompany\RejectCustomClearenceCompanyRequest;
use App\Http\Resources\CustomClearenceCompany\CustomClearenceCompanyResource;
use App\Models\CustomClearenceCompany;
use App\Services\CustomClearenceCompany\CustomClearenceCompanyService;
use Illuminate\Http\Request;

class CustomClearenceCompanyController extends Controller
{
    public function __construct(
        protected CustomClearenceCompanyService $customClearenceCompanyService
    ) {}

    public function getCustomClearenceCopmaniesByStatus(Request $request)
    {
        return success(
            $this->customClearenceCompanyService->getCustomClearenceCompaniesByStatus($request->all()),
            ApiMessages::MSG_SUCCESS,
            CustomClearenceCompanyResource::class,
            $request->has('per_page')
        );
    }

    public function showCustomClearenceCompany($id)
    {
        return success(
            $this->customClearenceCompanyService->showCustomClearenceCompany($id),
            ApiMessages::MSG_SUCCESS,
            CustomClearenceCompanyResource::class
        );
    }

    public function approveCustomClearenceCompany(ApproveCustomClearenceCompanyRequest $request)
    {
        return success(
            $this->customClearenceCompanyService->approveCustomClearenceCompany($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function rejectCustomClearenceCompany(RejectCustomClearenceCompanyRequest $request, $id)
    {
        return success(
            $this->customClearenceCompanyService->rejectCustomClearenceCompany($request->validated(), $id),
            ApiMessages::MSG_SUCCESS,
        );
    }
}

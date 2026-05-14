<?php

namespace App\Http\Controllers\Administration;

use Illuminate\Http\Request;
use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Resources\DriverCompany\DriverCompanyResource;
use App\Services\DriverCompany\DriverCompanyService;
use App\Http\Requests\Administration\DriverCompany\RejectDriverCompanyRequest;
use App\Http\Requests\Administration\DriverCompany\ApproveDriverCompanyRequest;

class DriverCompanyController extends Controller
{
    public function __construct(
        protected DriverCompanyService $driverCompanyService
    ) {}

    public function getDriverCompaniesByStatus(Request $request)
    {
        return success(
            $this->driverCompanyService->getDriverCompaniesByStatus($request->all()),
            ApiMessages::MSG_SUCCESS,
            DriverCompanyResource::class,
            $request->has('per_page')
        );
    }

    public function showDriverCompany($id)
    {
        return success(
            $this->driverCompanyService->showDriverCompany($id),
            ApiMessages::MSG_SUCCESS,
            DriverCompanyResource::class
        );
    }

    public function approveDriverCompany(ApproveDriverCompanyRequest $request)
    {
        return success(
            $this->driverCompanyService->approveDriverCompany($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function rejectDriverCompany(RejectDriverCompanyRequest $request, $id)
    {
        return success(
            $this->driverCompanyService->rejectDriverCompany($request->validated(), $id),
            ApiMessages::MSG_SUCCESS,
        );
    }
}

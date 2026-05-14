<?php

namespace App\Http\Controllers\Administration;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\Driver\ApproveNonSaudiEmployeeJoinRequest;
use App\Http\Requests\Administration\Driver\ApproveSaudiEmployeeJoinRequest;
use App\Http\Requests\Administration\Driver\ApproveSaudiJoinRequest;
use App\Http\Requests\Administration\Driver\RejectDriverRequest;
use App\Http\Resources\Driver\DriverResource;
use App\Services\Driver\DriverService;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function __construct(
        protected DriverService $driverService
    ) {}

    public function getDriversByStatus(Request $request)
    {
        return success(
            $this->driverService->getDriversByStatus($request->all()),
            ApiMessages::MSG_SUCCESS,
            DriverResource::class,
            $request->has('per_page')
        );
    }

    public function showDriver($id)
    {
        return success(
            $this->driverService->showDriver($id),
            ApiMessages::MSG_SUCCESS,
            DriverResource::class
        );
    }

    public function approveSaudiJoinRequest(ApproveSaudiJoinRequest $request)
    {
        return success(
            $this->driverService->approveDriver($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function approveSaudiEmployeeJoinRequest(ApproveSaudiEmployeeJoinRequest $request)
    {
        return success(
            $this->driverService->approveSaudiEmployeeDriver($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function approveNonSaudiEmployeeJoinRequest(ApproveNonSaudiEmployeeJoinRequest $request)
    {
        return success(
            $this->driverService->approveNonSaudiEmployeeDriver($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function rejectDriver(RejectDriverRequest $request, $id)
    {
        return success(
            $this->driverService->rejectDriver($request->validated(), $id),
            ApiMessages::MSG_SUCCESS,
        );
    }
}

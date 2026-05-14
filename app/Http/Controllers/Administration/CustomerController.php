<?php

namespace App\Http\Controllers\Administration;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\Customer\ApproveCompanyCustomerRequest;
use App\Http\Requests\Administration\Customer\ApproveGovernorateCustomerRequest;
use App\Http\Requests\Administration\Customer\ApproveSingleCustomerRequest;
use App\Http\Requests\Administration\Customer\RejectCustomerRequest;
use App\Http\Resources\Customer\CustomerResource;
use App\Services\Customer\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(
        protected CustomerService $customerService
    ) {}

    public function getCustomersByStatus(Request $request)
    {
        return success(
            $this->customerService->getCustomersByStatus($request->all()),
            ApiMessages::MSG_SUCCESS,
            CustomerResource::class,
            $request->has('per_page')
        );
    }

    public function showCustomer($id)
    {
        return success(
            $this->customerService->showCustomer($id),
            ApiMessages::MSG_SUCCESS,
            CustomerResource::class
        );
    }

    public function approveSingleCustomer(ApproveSingleCustomerRequest $request)
    {
        return success(
            $this->customerService->approveSingleCustomer($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function approveCompanyCustomer(ApproveCompanyCustomerRequest $request)
    {
        return success(
            $this->customerService->approveCompanyCustomer($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function approveGovernorateCustomer(ApproveGovernorateCustomerRequest $request)
    {
        return success(
            $this->customerService->approveGovernorateCustomer($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function rejectCustomer(RejectCustomerRequest $request, $id)
    {
        return success(
            $this->customerService->rejectCustomer($request->validated(), $id),
            ApiMessages::MSG_SUCCESS,
        );
    }
}

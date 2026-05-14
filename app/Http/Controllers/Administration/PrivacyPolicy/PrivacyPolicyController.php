<?php

namespace App\Http\Controllers\Administration\PrivacyPolicy;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\CreatePrivacyPolicyRequest;
use App\Http\Requests\Administration\UpdatePrivacyPolicyRequest;
use App\Services\Administration\PrivacyPolicy\PrivacyPolicyService;
use Illuminate\Http\Request;

class PrivacyPolicyController extends Controller
{
    public function __construct(
        protected PrivacyPolicyService $privacy_policy_service
    )
    {
    }

    public function get(Request $request)
    {
        return success(
            $this->privacy_policy_service->get(),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function create(CreatePrivacyPolicyRequest $request)
    {
        return createdSuccess(
            $this->privacy_policy_service->create($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function update(UpdatePrivacyPolicyRequest $request , $id)
    {
        return success(
            $this->privacy_policy_service->update($id , $request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }
}

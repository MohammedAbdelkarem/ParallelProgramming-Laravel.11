<?php

namespace App\Http\Controllers\Users\Auth;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Auth\AuthRequest;
use App\Services\Users\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function registerSaudiDriver(AuthRequest $request): JsonResponse
    {
        // dd(9);
        return createdSuccess(
            $this->authService->registerSaudiDriver($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }
    public function registerSingleCustomer(AuthRequest $request): JsonResponse
    {
        return createdSuccess(
            $this->authService->registerSingleCustomer($request->validated()),
            ApiMessages::MSG_SUCCESS,
            );
    }
    public function registerSaudiDriverEmployee(AuthRequest $request): JsonResponse
    {
        return createdSuccess(
            $this->authService->registerSaudiDriverEmployee($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }
    public function registerNonSaudiDriverEmployee(AuthRequest $request): JsonResponse
    {
        return createdSuccess(
            $this->authService->registerNonSaudiDriverEmployee($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }
    public function registerCompanyCustomer(AuthRequest $request): JsonResponse
    {
        return createdSuccess(
            $this->authService->registerCompanyCustomer($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }
    public function registerGovernorateCustomer(AuthRequest $request): JsonResponse
    {
        return createdSuccess(
            $this->authService->registerGovernorateCustomer($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }
    public function registerDriverCompany(AuthRequest $request): JsonResponse
    {
        return createdSuccess(
            $this->authService->registerDriverCompany($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function registerCustomClearenceCompany(AuthRequest $request): JsonResponse
    {
        return createdSuccess(
            $this->authService->registerCustomClearenceCompany($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function loginDriverCompany(AuthRequest $request): JsonResponse
    {
        return createdSuccess(
            $this->authService->loginDriverCompany($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function loginCustomClearenceCompany(AuthRequest $request): JsonResponse
    {
        return createdSuccess(
            $this->authService->loginCustomClearenceCompany($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }
    public function loginDriver(AuthRequest $request): JsonResponse
    {
        // dd(9);
        return createdSuccess(
            $this->authService->loginDriver($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function loginCustomer(AuthRequest $request): JsonResponse
    {
        return createdSuccess(
            $this->authService->loginCustomer($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }
    public function loginParent(AuthRequest $request): JsonResponse
    {
        // dd(9);
        return createdSuccess(
            $this->authService->loginForParent($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }
    public function loginTeacher(AuthRequest $request): JsonResponse
    {
        // dd(9);
        return createdSuccess(
            $this->authService->loginForTeacher($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function activeSessions()
    {
        return Success(
            $this->authService->activeSessions(),
            ApiMessages::MSG_SUCCESS,
            // LoginHistoryResource::class,
        );
    }

    public function logoutSessions(Request $request): JsonResponse
    {
        return Success(
            $this->authService->logoutSessions($request->ids),
            ApiMessages::MSG_LOGOUT_SUCCESSFULLY,
        );
    }

    public function logout(Request $request): JsonResponse
    {
        return Success(
            $this->authService->logout($request->notification_token),
            ApiMessages::MSG_LOGOUT_SUCCESSFULLY,
        );
    }

    public function logoutAll(Request $request): JsonResponse
    {
        return Success(
            $this->authService->logoutAllDevices(),
            ApiMessages::MSG_LOGOUT_SUCCESSFULLY,
        );
    }

    public function refresh(Request $request): JsonResponse
    {
        return Success(
            $this->authService->refresh(),
            ApiMessages::MSG_SUCCESS,
        );
    }
}

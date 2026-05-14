<?php

namespace App\Http\Controllers\Administration\Auth;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\Auth\AuthRequest;
use App\Http\Resources\Users\Profile\LoginHistoryResource;
use App\Services\Administration\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function login(AuthRequest $request): JsonResponse
    {
        return createdSuccess(
            $this->authService->login($request->validated()),
            ApiMessages::MSG_CREATED,
        );
    }

    public function activeSessions()
    {
        return Success(
            $this->authService->activeSessions(),
            ApiMessages::MSG_SUCCESS,
            LoginHistoryResource::class,
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

<?php

namespace App\Http\Controllers\Users;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Services\Users\UserHomeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserHomeController extends Controller
{
    public function __construct(
        protected UserHomeService $userHomeService
    ) {}

    public function home(Request $request): JsonResponse
    {
        return success(
            $this->userHomeService->home(),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function overview(): JsonResponse
    {
        return success(
            $this->userHomeService->overview(),
            ApiMessages::MSG_SUCCESS,
        );
    }
}

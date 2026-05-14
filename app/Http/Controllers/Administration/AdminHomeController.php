<?php

namespace App\Http\Controllers\Administration;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Services\Administration\AdminHomeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminHomeController extends Controller
{
    public function __construct(
        protected AdminHomeService $adminHomeService
    ) {}
    public function processing($value): JsonResponse
    {
        return success(
            $this->adminHomeService->processing($value),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function home(Request $request): JsonResponse
    {
        return success(
            $this->adminHomeService->home(),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function overview(): JsonResponse
    {
        return success(
            $this->adminHomeService->overview(),
            ApiMessages::MSG_SUCCESS,
        );
    }
}

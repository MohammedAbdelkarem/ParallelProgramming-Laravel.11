<?php

namespace App\Http\Controllers\Administration\Log;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\Log\BanLogRequest;
use App\Http\Resources\Administration\Log\BanLogResource;
use App\Models\Administration\Log\BanLog;
use App\Models\User;
use App\Services\Administration\Log\BanLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BanLogController extends Controller
{
    public function __construct(
        protected BanLogService $banLogService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $per_page   = $request->per_page;
        $search     = $request->search ?? "";
        $order      = $request->order;
        $id         = $request->id;

        return success(
            $this->banLogService->index($per_page, $search, $order, $id),
            ApiMessages::MSG_SUCCESS,
            BanLogResource::class,
            true,
        );
    }

    public function show(string $id): JsonResponse
    {
        return success(
            $this->banLogService->show($id),
            ApiMessages::MSG_SUCCESS,
            BanLogResource::class
        );
    }

    public function ban(BanLogRequest $request): JsonResponse
    {
        return success(
            $this->banLogService->ban($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function unBan(BanLogRequest $request): JsonResponse
    {
        return success(
            $this->banLogService->unBan($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }
}
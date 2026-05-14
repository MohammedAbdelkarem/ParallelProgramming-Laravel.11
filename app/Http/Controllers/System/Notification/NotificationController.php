<?php

namespace App\Http\Controllers\System\Notification;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\System\Notification\NotificationRequest;
use App\Http\Resources\System\Notification\AdminNotificationResource;
use App\Http\Resources\System\Notification\NotificationResource;
use App\Models\System\Notification\Notification;
use App\Services\System\Notification\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function index(Request $request): JsonResponse
    {
        return success(
            $this->notificationService->index($request->per_page),
            ApiMessages::MSG_SUCCESS,
            AdminNotificationResource::class,
            true
        );
    }

    public function preStore(): JsonResponse
    {
        return success(
            $this->notificationService->preStore(),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function storePrivate(NotificationRequest $request): JsonResponse
    {
        return createdSuccess(
            $this->notificationService->storePrivate($request->validated()),
            ApiMessages::MSG_CREATED
        );
    }

    public function storePublic(NotificationRequest $request): JsonResponse
    {
        return createdSuccess(
            $this->notificationService->storePublic($request->validated()),
            ApiMessages::MSG_CREATED
        );
    }

    public function show(string $id): JsonResponse
    {
        return success(
            $this->notificationService->show($id),
            ApiMessages::MSG_SUCCESS,
            NotificationResource::class
        );
    }

    public function showAdmin(string $id): JsonResponse
    {
        return success(
            $this->notificationService->showAdmin($id),
            ApiMessages::MSG_SUCCESS,
            AdminNotificationResource::class
        );
    }

    public function getMyNotifications(Request $request): JsonResponse
    {
        return success(
            $this->notificationService->getMyNotifications($request->per_page),
            ApiMessages::MSG_SUCCESS,
            NotificationResource::class,
            true
        );
    }

    public function destroy(string $id): JsonResponse
    {
        return success(
            $this->notificationService->destroy($id),
            ApiMessages::MSG_DELETED,
        );
    }
}

<?php

namespace App\Http\Controllers\Administration\Profile;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\Profile\AdminProfileRequest;
use App\Http\Resources\Administration\Profile\AdminListResource;
use App\Http\Resources\Administration\Profile\AdminProfileResource;
use App\Http\Resources\Users\Profile\LoginHistoryResource;
use App\Services\Administration\Profile\AdminProfileService;
use App\Services\Users\Profile\LoginHistoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminProfileController extends Controller
{
    public function __construct(
        protected AdminProfileService $adminProfileService,
        protected LoginHistoryService $loginHistoryService
    ) {}

    public function adminSugs(request $request): JsonResponse
    {
        return success(
            $this->adminProfileService->adminSugs($request->search),
            ApiMessages::MSG_SUCCESS,
            AdminListResource::class,
        );
    }

    public function index(Request $request)
    {
        $per_page       = $request->per_page;
        $search         = $request->search;
        $cities         = $request->cities;
        $role_id        = $request->role_id;
        $active_status  = $request->active_status;
        $start_date     = $request->start_date;
        $end_date       = $request->end_date;

        return success(
            $this->adminProfileService->index(
                $per_page,
                $search,
                $cities,
                $role_id,
                $active_status,
                $start_date,
                $end_date
            ),
            ApiMessages::MSG_SUCCESS,
            AdminListResource::class,
            true
        );
    }

    public function store(AdminProfileRequest $request)
    {
        return createdSuccess(
            $this->adminProfileService->storeAdmin($request->validated()),
            ApiMessages::MSG_CREATED,
        );
    }

    public function show(string $id): JsonResponse
    {
        return success(
            $this->adminProfileService->show($id),
            ApiMessages::MSG_SUCCESS,
            AdminProfileResource::class
        );
    }

    public function updateProfileImage(AdminProfileRequest $request)
    {
        return success(
            $this->adminProfileService->updateProfileImage($request->validated(), $request->id),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function update(AdminProfileRequest $request)
    {
        return success(
            $this->adminProfileService->updateAdmin($request->validated(), $request->id),
            ApiMessages::MSG_UPDATED,
        );
    }

    public function deactivateAccount(int $id)
    {
        $data = $this->adminProfileService->deactivateAccount($id);
        return success(
            null,
            $data ? ApiMessages::MSG_ACCOUNT_DEACTIVATED : ApiMessages::MSG_ACCOUNT_ACTIVATED,
        );
    }

    public function changeLang(AdminProfileRequest $request): JsonResponse
    {
        return success(
            $this->adminProfileService->changeLang($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function changeNotificationState(): JsonResponse
    {
        return success(
            $this->adminProfileService->changeNotificationStatus(),
            ApiMessages::MSG_SUCCESS,
        );
    }
    public function loginHistory(Request $request): JsonResponse
    {
        return success(
            $this->loginHistoryService->index($request->per_page, $request->id),
            ApiMessages::MSG_SUCCESS,
            LoginHistoryResource::class,
            true
        );
    }
}

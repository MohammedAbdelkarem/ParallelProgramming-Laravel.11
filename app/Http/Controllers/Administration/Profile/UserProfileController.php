<?php

namespace App\Http\Controllers\Administration\Profile;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\Profile\UserProfileRequest;
use App\Http\Requests\Finance\TransferRequest;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\Users\Profile\ProfileResource;
use App\Http\Resources\Users\Profile\UserListResource;
use App\Http\Resources\Users\Profile\UserSugResource;
use App\Services\Administration\Profile\UserProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function __construct(
        protected UserProfileService $userProfileService
    ) {}

    public function changeIsPrefferedStatus(Request $request): JsonResponse
    {
        return success(
            $this->userProfileService->changeIsPreffered($request->all()),
            ApiMessages::MSG_SUCCESS,
        );
    }


    public function userSugs(request $request): JsonResponse
    {
        return success(
            $this->userProfileService->userSugs($request->search, $request->with_deleted),
            ApiMessages::MSG_SUCCESS,
            UserSugResource::class,
        );
    }

    public function index(Request $request): JsonResponse
    {
        $per_page       = $request->per_page;
        $search         = $request->search;
        $cities         = $request->cities;
        $active_status  = $request->active_status;
        $banned_status  = $request->banned_status;
        $deleted_status = $request->deleted_status;
        $start_date     = $request->start_date;
        $end_date       = $request->end_date;
        $role_id       = $request->role_id;

        return success(
            $this->userProfileService->index(
                $per_page,
                $search,
                $cities,
                $active_status,
                $banned_status,
                $deleted_status,
                $start_date,
                $end_date,
                $role_id
            ),
            ApiMessages::MSG_SUCCESS,
            UserListResource::class,
            true
        );
    }

    public function show(string $id): JsonResponse
    {
        return success(
            $this->userProfileService->show($id),
            ApiMessages::MSG_SUCCESS,
            ProfileResource::class
        );
    }

    public function restore(UserProfileRequest $request)
    {
        return success(
            $this->userProfileService->restore($request->validated()),
            ApiMessages::MSG_RESTORED,
        );
    }
}

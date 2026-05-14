<?php

namespace App\Http\Controllers\Users\Profile;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Profile\ProfileRequest;
use App\Http\Resources\Users\Profile\LoginHistoryResource;
use App\Http\Resources\Users\Profile\ProfileResource;
use App\Http\Resources\Users\Profile\UserListResource;
use App\Http\Resources\Users\Profile\UserSugResource;
use App\Services\Users\FavoriteService;
use App\Services\Users\Profile\LoginHistoryService;
use App\Services\Users\Profile\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(
        protected ProfileService $profileService,
        protected LoginHistoryService $loginHistoryService,
    ) {}

    public function completeProfile(ProfileRequest $request): JsonResponse
    {
        return success(
            $this->profileService->completeProfile($request->validated()),
            ApiMessages::MSG_SUCCESS,
            UserSugResource::class,
        );
    }

    //TODO:TEMPLATE CHECK FOR ID : If app allow only to see my profile
    public function show($id): JsonResponse
    {
        // dd($this->profileService->show($id));
        return success(
            $this->profileService->show($id),
            ApiMessages::MSG_SUCCESS,
            ProfileResource::class
        );
    }

    public function updateProfileImage(ProfileRequest $request)
    {
        return success(
            $this->profileService->updateProfileImage($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function update(ProfileRequest $request)
    {
        return success(
            $this->profileService->update($request->validated()),
            ApiMessages::MSG_UPDATED,
            ProfileResource::class
        );
    }

    public function changeLang(ProfileRequest $request): JsonResponse
    {
        return success(
            $this->profileService->changeLang($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function changeNotificationState(): JsonResponse
    {
        return success(
            $this->profileService->changeNotificationStatus(),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function loginHistory(Request $request): JsonResponse
    {
        return success(
            $this->loginHistoryService->index($request->per_page),
            ApiMessages::MSG_SUCCESS,
            LoginHistoryResource::class,
            true
        );
    }

    public function deactivateAccount(Request $request)
    {
        return success(
            $this->profileService->deactivateAccount(),
            ApiMessages::MSG_ACCOUNT_DEACTIVATED,
        );
    }

    public function deleteProfile(ProfileRequest $request): JsonResponse
    {
        return success(
            $this->profileService->deleteProfile($request->validated()),
            ApiMessages::MSG_ACCOUNT_DELETED,
        );
    }
}
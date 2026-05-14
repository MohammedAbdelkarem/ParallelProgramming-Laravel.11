<?php

namespace App\Services\Users\Profile;

use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Constants\Resources;
use App\Services\MainService;
use App\Traits\StorageHelper;
use Illuminate\Support\Carbon;
use App\Constants\MediaCollection;
use App\Http\Resources\Media\MediaResource;
use App\Services\JWTTokensService;
use App\Services\User\UserService;
use Illuminate\Support\Facades\DB;
use App\Models\Users\Profile\UserDevice;
use App\Models\Users\Profile\ArchivedUser;

class ProfileService extends MainService
{
    use StorageHelper;

    public function __construct(
        protected JWTTokensService $jwtService,
        protected UserService $userService,
    ) {}

    public function completeProfile($validatedData)
    {
        /**
         * @var \App\Models\User $user
         */
        $user = auth()->user();

        $user->name         = $validatedData["name"];
        $user->email        = $validatedData["email"];
        $user->birth_date   = $validatedData["birth_date"];
        $user->is_male      = $validatedData["is_male"];

        //Store Image
        if(isset($validatedData['avatar']))
            uploadFileOnMedia($validatedData['avatar'] , $user , MediaCollection::USER_COLLECTION);
        

        $user->save();
        return $user;
    }

    public function show($id)
    {
        


        return 
        findByIdOrFail(
                model: User::class,
                modelId: $id,
                resource: Resources::RES_USER,
                type: 'male',
                with: ['profile'],
                asQuery: true,
            )
            // ->usersSearchCriteria(checkBan: false)
            //     ->with("bans", function ($query) {
            //         $query->where("banned_until", ">=", Carbon::now())->latest()->take(1);
            //     })
                ->first();
    }

    public function update($validatedData)
    {
        /**
         * @var \App\Models\User $user
         */
        $user = auth()->user();
        $user->update([
            "name"          => $validatedData["name"],
            "birth_date"    => $validatedData["birth_date"],
            "is_male"       => $validatedData["is_male"],
            "email"         => $validatedData["email"],
        ]);

        if(isset($validatedData['avatar']))
            updateFileOnMedia($validatedData['avatar'] , $user , MediaCollection::USER_COLLECTION);
        

        return $user;
    }

    public function updateProfileImage($validatedData): array
    {
        /**
         * @var \App\Models\User $user
         */
        $user = auth()->user();

        //Check to add new image
        if(isset($validatedData['avatar']))
            updateFileOnMedia($validatedData['avatar'] , $user , MediaCollection::USER_COLLECTION);
        

        //return image url
        return ["img" => MediaResource::make($user->getFirstMedia(MediaCollection::USER_COLLECTION))];
    }

    public function deactivateAccount()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = auth()->user();

        $user->deactive_at = Carbon::now()->format('Y-m-d H:i:s');
        $user->save();

        $this->jwtService->InvalidateAllTokensByUserID($user->id);

        UserDevice::where('user_id', $user->id)->delete();
    }

    public function deleteProfile($validatedData)
    {
        // dd(9);
        /**
         * @var \App\Models\User $user
         */
        $user = auth()->user();

        //Archive Account
        ArchivedUser::create([
            "user_id" => $user->id,
            "phone_number"  => $user->phone_number,
        ]);
        //Deactive Date
        $user->deactive_at = Carbon::now()->format('Y-m-d H:i:s');
        $user->active_notifications = 0;
        $user->phone_number = null;

        //Delete Devices
        UserDevice::where('user_id', $user->id)->delete();

        // $user->favorites()->delete();    //TODO:CHECK
        $user->save();

        //Delete Tokens
        $this->jwtService->InvalidateAllTokensByUserID($user->id);

        //Delete the user
        $user->delete();
    }

    public function changeLang($validatedData)
    {
        /**
         * @var \App\Models\User $user
         */
        $user = auth()->user();

        $user->language = $validatedData["lang"];
        $user->save();
    }

    public function changeNotificationStatus()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = auth()->user();

        $user->active_notifications = !$user->active_notifications;
        $user->save();
    }

}

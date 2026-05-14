<?php

namespace App\Services\Administration\Profile;

use Carbon\Carbon;
use App\Models\User;
use App\Constants\Resources;
use App\Services\MainService;
use App\Exceptions\ApiException;
use App\Constants\MediaCollection;
use App\Services\JWTTokensService;
use Illuminate\Support\Facades\DB;
use App\Constants\ExceptionMessages;
use App\Http\Resources\Media\MediaResource;
use Illuminate\Support\Facades\Hash;
use App\Models\Users\Profile\UserDevice;
use App\Models\Administration\Profile\AdminProfile;

class AdminProfileService extends MainService
{
    public function __construct(
        protected JWTTokensService $jwtService,
    ) {}

    public function adminSugs($search)
    {
        return User::query()->select(["id", "role_id", "name", "email", "deleted_at", "deactive_at", "created_at"])
            ->whereIn("role_id", [1,2])
            ->when(
                $search,
                function ($q) use ($search) {
                    $q->searchName($search);
                }
            )->with('archivedAccount')->limit(8)->get();
    }

    /**
     * @param mixed $active_status  1 => All | 2 => Active | 3 => Inactive
     */
    public function index(
        $per_page,
        $search,
        $cities,
        $role_id,
        $active_status,
        $start_date,
        $end_date,
    ) {
        return User::query()
            ->whereIn('role_id', [1,2])
            ->when($role_id, function ($query) use ($role_id) {
                $query->where('role_id', $role_id);
            })
            ->whereNotNull(['name', 'account_verified_at'])
            //Active Status
            ->when($active_status, function ($query) use ($active_status) {
                if ($active_status == "2")
                    $query->whereNull('deactive_at');
                elseif ($active_status == "3")
                    $query->whereNotNull('deactive_at');
            })
            //Search By name or phone number
            ->when($search, function ($query) use ($search) {
                $query->searchName($search);
            })
            //Filter By Join Date (min)
            ->when($start_date, function ($query) use ($start_date) {
                $query->where('created_at', ">=", $start_date);
            })
            // Filter By Join Date (max)
            ->when($end_date, function ($query) use ($end_date) {
                $query->where('created_at', "<=", $end_date);
            })
            //Get Other Need Info
            ->with(['archivedAccount'])
            ->paginate($per_page);
    }

    public function storeAdmin($validatedData)
    {
        //Create User (Admin)
        $user = User::create([
            "role_id"       => $validatedData["role_id"],
            "name"          => $validatedData["name"],
            "birth_date"    => $validatedData["birth_date"],
            "is_male"       => $validatedData["is_male"],
            "email"         => $validatedData["email"],
            "language"      => "ar",
            "phone_number"  => $validatedData["phone_number"],
            "active_notifications"  => true,
            "account_verified_at"   => Carbon::now()->format("Y-m-d H:i:s"),
        ]);
        //Create Admin Profile
        AdminProfile::create([
            "user_id"       => $user->id,
            "password"      => Hash::make($validatedData["password"]),
            "created_by"    => auth()->id(),
        ]);
        //Store User Image
        if(isset($validatedData['avatar']))
            uploadFileOnMedia($validatedData['avatar'] , $user , MediaCollection::USER_COLLECTION);
        
    }

    public function show($id)
    {
        return findByIdOrFail(
            model: User::class,
            modelId: $id,
            resource: Resources::RES_USER,
            type: 'male',
            with: ['archivedAccount'],
            withTrashed: true,
            asQuery: true,
        )
            ->with('adminProfile.creator')
            ->findOrFail($id);
    }

    public function updateAdmin($validatedData, $id)
    {
        /**
         * @var \App\Models\User $user
         */
        $user = User::findByIdOrFail($id);

        //Delete Tokens so user have to login again to get the new abilities
        if ($user->role_id != $validatedData["role_id"]) {
            $this->jwtService->InvalidateAllTokensByUserID($user->id);
            UserDevice::where('user_id', $user->id)->delete();
        }
        //Create User (Admin)
        $user->update([
            "role_id"       => $validatedData["role_id"],
            "name"          => $validatedData["name"],
            "birth_date"    => $validatedData["birth_date"],
            "is_male"       => $validatedData["is_male"],
            "email"         => $validatedData["email"],
            "phone_number"  => $validatedData["phone_number"],
        ]);
        //Update User Image
        if(isset($validatedData['avatar']))
            updateFileOnMedia($validatedData['avatar'] , $user , MediaCollection::USER_COLLECTION);
        
        if (!empty($validatedData["password"]))
            $user->adminProfile()->update([
                "password"      => Hash::make($validatedData["password"]),
            ]);
    }

    public function updateProfileImage($validatedData, $id)
    {
        /**
         * @var \App\Models\User $user
         */
        $user = User::findByIdOrFail($id);


        if(isset($validatedData['avatar']))
            updateFileOnMedia($validatedData['avatar'] , $user , MediaCollection::USER_COLLECTION);
        
        //return image url
        return ["img" => MediaResource::make($user->getFirstMedia(MediaCollection::USER_COLLECTION))];
    }

    public function deactivateAccount($id)
    {
        $user = User::findByIdOrFail($id);
        /**
         * To not deactive user 1 OR a super admin from other admin || or normal user
         */
        if (($user->role_id == 1 && $user->id != auth()->id()) || $user->id == 1 || $user->role_id == 3) {
            throw new ApiException(null, trans(ExceptionMessages::MSG_ACCEESS_DENIED), 400);
        }

        $user->deactive_at ?
            $user->deactive_at = null
            : $user->deactive_at = Carbon::now()->format('Y-m-d H:i:s');
        $user->save();

        $this->jwtService->InvalidateAllTokensByUserID($user->id);

        UserDevice::where('user_id', $user->id)->delete();
        return (bool) $user->deactive_at;
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

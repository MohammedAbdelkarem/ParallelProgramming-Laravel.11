<?php

namespace App\Services\Administration\Profile;

use Carbon\Carbon;
use App\Models\User;
use App\Constants\Resources;
use App\Services\MainService;

class UserProfileService extends MainService
{
    public function __construct(
        
    ) {}

    
    public function changeIsPreffered($data)
    {
        $model = getModel($data['actor_type']);

        $record = $model::findByIdOrFail($data['actor_id']);

        $record->update([
            'is_preffered' => ! $record->is_preffered,
        ]);
    }

    public function userSugs($search, $with_deleted)
    {
        $users = $with_deleted ?  User::withTrashed() : User::query();

        return $users->select(["id", "role_id", 'name', "phone_number", "deleted_at", "deactive_at"])
            ->whereIn("role_id", [3,4,5])
            ->when(
                $search,
                function ($q) use ($search) {
                    $q->searchName($search);
                }
            )
            ->with('archivedAccount')
            ->limit(8)
            ->get();
    }

    /**
     * @param mixed $active_status  1 => All | 2 => Active | 3 => Inactive
     * @param mixed $banned_status  1 => All | 2 => Banned | 3 => Not Banned
     * @param mixed $deleted_status 1 => All | 2 => Deleted | 3 => Not Deleted
     */
    public function index(
        $per_page,
        $search,
        $cities,
        $active_status,
        $banned_status,
        $deleted_status,
        $start_date,
        $end_date,
        $role_id
    ) {
        // dd($role_id);
        return User::query()
            ->whereIn('role_id' , [3,4,5])
            ->when($role_id, function ($query) use ($role_id) {
                $query->where('role_id', $role_id);
            })
            //To not get unverified uncompleted accounts
            ->whereNotNull(['name', 'account_verified_at'])
            //Deleted Status
            ->when($deleted_status, function ($query) use ($deleted_status) {
                if ($deleted_status == "1")
                    $query->withTrashed();
                elseif ($deleted_status == "2")
                    $query->onlyTrashed();
            })
            //Active Status
            ->when($active_status, function ($query) use ($active_status) {
                if ($active_status == "2")
                    $query->whereNull('deactive_at');
                elseif ($active_status == "3")
                    $query->whereNotNull('deactive_at');
            })
            //Ban Status
            ->when($banned_status, function ($query) use ($banned_status) {
                if ($banned_status == "2")
                    $query->whereHas("profile", function ($query) {
                        $query->where("banned_until", "<", Carbon::now())
                            ->orWhereNull("banned_until");
                    });
                elseif ($banned_status == "3")
                    $query->whereHas("profile", function ($query) {
                        $query->where("banned_until", ">=", Carbon::now());
                    });
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
            ->with(['profile', 'archivedAccount'])
            ->paginate($per_page);
    }

    public function show($id)
    {
        return findByIdOrFail(
            model: User::class,
            modelId: $id,
            resource: Resources::RES_USER,
            type: 'male',
            // where: ['role_id' => 3],
            with: ['profile', 'archivedAccount'],
            withTrashed: true,
            asQuery: true,
        )
            ->with("bans", function ($query) {
                $query->where("banned_until", ">=", Carbon::now())->latest()->take(1);
            })
            ->first();
    }

    public function restore($validatedData)
    {
        //already validated
        $user = User::onlyTrashed()
            ->with('archivedAccount')
            ->findOrFail($validatedData["user_id"]);

        $user->restore();
        $user->phone_number = $user->archivedAccount->phone_number;
        $user->save();
        $user->archivedAccount->delete();
    }

    
    public function getStudentProfile($id)
    {
        $user =  User::findByIdOrFail($id, [
            'profile',
        ]);

        return $user;
    }

}
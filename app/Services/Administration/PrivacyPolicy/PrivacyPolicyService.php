<?php

namespace App\Services\Administration\PrivacyPolicy;

use App\Constants\ExceptionMessages;
use App\Models\PrivacyPolicy;

/**
 * Class PrivacyPolicyService.
 */
class PrivacyPolicyService
{
    public function get()
    {
        return PrivacyPolicy::all();
    }

    public function create($data)
    {
        if(PrivacyPolicy::where('actor_type' , $data['actor_type'])->exists())
        {
            return forbiddenFailure([] , ExceptionMessages::MSG_PRIVACY_POLICY_ALREADY_EXISTS_FOR_THIS_ACTOR);
        }
        PrivacyPolicy::create($data);
    }

    public function update($privacy_policy_id , $data)
    {
        $privacy_policy = PrivacyPolicy::find($privacy_policy_id);
        $privacy_policy->update($data);
        $privacy_policy->save();
    }

    public function getForUsers($actor_type)
    {
        return PrivacyPolicy::where('actor_type' , $actor_type)->first();
    }
}

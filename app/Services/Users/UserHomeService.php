<?php

namespace App\Services\Users;

use App\Http\Resources\Administration\ProductManagement\ProductListResource;
use App\Http\Resources\System\Category\CategorySelectResource;
use App\Http\Resources\System\Category\SubCategorySelectResource;
use App\Http\Resources\Users\Banner\BannerResource;
use App\Http\Resources\Users\Profile\UserSugResource;
use App\Models\System\Category\Category;
use App\Models\Users\Banner\Banner;
use App\Services\MainService;
use App\Services\Users\Product\ProductService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class UserHomeService.
 */
class UserHomeService extends MainService
{
    public function home()
    {
        //TODO:TEMPLATE
    }

    public function overview()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = auth()->user();

        return [
            "user"              => new UserSugResource($user),
            "new_notifications" => $user->notifications()->wherePivot("is_read", 0)->count(),
        ];
    }
}
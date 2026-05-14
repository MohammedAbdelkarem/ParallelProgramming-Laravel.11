<?php

namespace App\Models;

use App\Constants\Resources;
use App\Enums\GenderEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivacyPolicy extends Model
{
    use HasFactory;
    
    protected $guarded = [
        'id'
    ];


    /**
     * @return \App\Models\PrivacyPolicy
     */
    public static function findByIdOrFail($id, $with = [], $withTrashed = false, $selectedColumns = null)
    {
        return findByIdOrFail(
            self::class,
            $id,
            GenderEnum::MALE,
            Resources::RES_MODEL,
            $with,
            $withTrashed,
            $selectedColumns
        );
    }
}

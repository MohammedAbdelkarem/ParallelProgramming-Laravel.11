<?php

namespace App\Models;

use App\Constants\MediaCollection;
use App\Constants\Resources;
use App\Enums\GenderEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Spatie\MediaLibrary\HasMedia;
//use Spatie\MediaLibrary\InteractsWithMedia;

class Wallet extends Model //implements HasMedia
{
    use HasFactory;
    //use HasFactory , InteractsWithMedia;
    
    protected $guarded = [
        'id'
    ];

    //public function registerMediaCollections(): void
    //{
    //    $this->addMediaCollection(MediaCollection::EXAMPLE_COLLECTION)->singleFile();
    //}


    /**
     * @return \App\Models\Wallet
     */
    public static function findByIdOrFail($id, $with = [], $withTrashed = false, $selectedColumns = null)
    {
        return findByIdOrFail(
            self::class,
            $id,
            GenderEnum::MALE,
            Resources::RES_WALLET,
            $with,
            $withTrashed,
            $selectedColumns
        );
    }

    // Polymorphic owner (customer, driver, company, platform)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Wallet transactions
    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }
}

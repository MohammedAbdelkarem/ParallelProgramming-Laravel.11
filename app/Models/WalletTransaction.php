<?php

namespace App\Models;

use App\Constants\MediaCollection;
use App\Constants\Resources;
use App\Enums\GenderEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Spatie\MediaLibrary\HasMedia;
//use Spatie\MediaLibrary\InteractsWithMedia;

class WalletTransaction extends Model //implements HasMedia
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
     * @return \App\Models\WalletTransaction
     */
    public static function findByIdOrFail($id, $with = [], $withTrashed = false, $selectedColumns = null)
    {
        return findByIdOrFail(
            self::class,
            $id,
            GenderEnum::MALE,
            Resources::RES_WALLET_TRANSACTIONS,
            $with,
            $withTrashed,
            $selectedColumns
        );
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    // Polymorphic reference (order, settlement, dispute, refund)
    public function reference()
    {
        return $this->morphTo();
    }

    public function from()
    {
        return $this->morphTo();
    }

    public function to()
    {
        return $this->morphTo();
    }
}

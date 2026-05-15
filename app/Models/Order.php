<?php

namespace App\Models;

use App\Constants\MediaCollection;
use App\Constants\Resources;
use App\Enums\GenderEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Spatie\MediaLibrary\HasMedia;
//use Spatie\MediaLibrary\InteractsWithMedia;

class Order extends Model //implements HasMedia
{
    use HasFactory;
    //use HasFactory , InteractsWithMedia;
    
    protected $guarded = [
        'id'
    ];

    //protected static function booted()
    //{
    //    static::addGlobalScope(new ExampleScope);
    //}

    //public function registerMediaCollections(): void
    //{
    //    $this->addMediaCollection(MediaCollection::EXAMPLE_COLLECTION)->singleFile();
    //}


    /**
     * @return \App\Models\Order
     */
    public static function findByIdOrFail($id, $with = [], $withTrashed = false, $selectedColumns = null)
    {
        return findByIdOrFail(
            self::class,
            $id,
            GenderEnum::MALE,
            Resources::ITEM,
            $with,
            $withTrashed,
            $selectedColumns
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function walletTransactions()
    {
        return $this->morphMany(WalletTransaction::class, 'reference');
    }

    public function scopeFilter($query , $data)
    {
        return $query
            ->when(isset($data['status']) , function($q) use ($data){
                $q->where('status' , $data['status']);
            })
            ->when(! auth()->user()->isAdmin(), function($q){
                $q->where('user_id' , auth()->id());
            });
    }
}

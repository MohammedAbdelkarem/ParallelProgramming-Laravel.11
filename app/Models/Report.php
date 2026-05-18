<?php

namespace App\Models;

use App\Constants\MediaCollection;
use App\Constants\Resources;
use App\Enums\GenderEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Spatie\MediaLibrary\HasMedia;
//use Spatie\MediaLibrary\InteractsWithMedia;

class Report extends Model //implements HasMedia
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
     * @return \App\Models\Report
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
}

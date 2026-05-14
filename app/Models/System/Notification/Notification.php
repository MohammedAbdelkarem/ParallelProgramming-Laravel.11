<?php

namespace App\Models\System\Notification;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Notification extends Model
{
    use HasFactory;
    protected $table = "notifications";
    protected $primaryKey = "id";
    protected $timestamp = true;
    protected $guarded = ['id'];

    protected $casts = [
        'title'      => 'array',
        'body'       => 'array',
        'extra_data' => 'array',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function receivers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_notification')
            ->withTimestamps()
            ->withPivot('is_read');
    }

    public function views(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_notification')
            ->where('is_read', true)
            ->withTimestamps()
            ->withPivot('is_read');
    }
}

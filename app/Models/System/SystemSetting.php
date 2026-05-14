<?php

namespace App\Models\System;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemSetting extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function updated_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'update_by');
    }
}

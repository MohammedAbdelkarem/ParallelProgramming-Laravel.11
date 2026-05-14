<?php

namespace App\Models\Users\Profile;

use App\Models\JWTPersonalTokens;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LoginHistory extends Model
{
    use HasFactory;

    protected $table = "login_history";
    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function token(): HasOne
    {
        return $this->hasOne(JWTPersonalTokens::class, "login_history_id");
    }
}

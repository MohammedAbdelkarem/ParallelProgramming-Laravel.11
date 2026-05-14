<?php

namespace App\Models;

use App\Models\Users\Profile\LoginHistory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class JWTPersonalTokens extends Model
{
    use HasFactory;

    protected $table = 'jwt_personal_tokens';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function access_token(): BelongsTo
    {
        return $this->belongsTo(JWTPersonalTokens::class, "related_to");
    }

    public function refresh_token(): HasOne
    {
        return $this->hasOne(JWTPersonalTokens::class, "related_to");
    }

    public function loginHistory(): BelongsTo
    {
        return $this->belongsTo(LoginHistory::class, "login_history_id");
    }
}
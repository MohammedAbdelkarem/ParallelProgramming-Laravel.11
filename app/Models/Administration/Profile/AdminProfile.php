<?php

namespace App\Models\Administration\Profile;

use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminProfile extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $hidden = [
        'password',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function sentTransactions()
    {
        return $this->morphMany(WalletTransaction::class, 'from');
    }

    public function receivedTransactions()
    {
        return $this->morphMany(WalletTransaction::class, 'to');
    }
}

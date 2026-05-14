<?php

namespace App\Models\System\Role;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'created_by'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_id');
    }

    public function abilities(): BelongsToMany
    {
        return $this->belongsToMany(Ability::class, 'role_ability', 'role_id', 'ability_id')
            ->withTimestamps();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function setNameAttribute($value): void
    {
        $this->attributes['name'] = ucfirst(strtolower($value));
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    protected $guarded = ['id'];

    public function Group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'groupid');
    }

    public function Users(): HasMany
    {
        return $this->hasMany(User::class, 'userid');
    }
}

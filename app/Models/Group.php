<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Group extends Model
{
    protected $guarded = ['id'];

    public function Owners(): HasMany
    {
        return $this->hasMany(User::class, 'id', 'ownerid');
    }

    public function Goal(): HasOne
    {
        return $this->hasOne(Goal::class, 'groupid');
    }

    public function Members(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, Member::class, 'groupid', 'id', 'id', 'userid');
    }
}

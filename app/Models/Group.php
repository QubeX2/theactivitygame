<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Group extends Model
{
    protected $guarded = ['id'];

    public function Owners(): HasMany
    {
        return $this->hasMany(User::class, 'id', 'ownerid');
    }

    public function Goals(): HasMany
    {
        return $this->hasMany(Goal::class, 'groupid');
    }

    public function Members(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, Member::class, 'groupid', 'id', 'id', 'userid');
    }

    public function History(): HasMany
    {
        return $this->hasMany(History::class, 'groupid');
    }
}

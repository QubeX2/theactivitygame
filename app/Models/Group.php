<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    protected $guarded = ['id'];

    public function Owners(): HasMany
    {
        return $this->hasMany(User::class, 'id', 'ownerid');
    }
}

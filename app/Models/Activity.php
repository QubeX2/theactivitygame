<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function Group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'groupid');
    }

    public function History(): HasMany
    {
        return $this->hasMany(History::class, 'activityid');
    }
}

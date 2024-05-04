<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class History extends Model
{
    protected $table = 'history';
    protected $guarded = ['id'];

    public function Group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'groupid');
    }

    public function User(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userid');
    }

    public function Activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class, 'activityid');
    }
}

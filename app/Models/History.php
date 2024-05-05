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

    public function scopeGoalType($query, $typeid = 1)
    {
        switch($typeid) {
            case Goal::TYPE_DAILY:
                return $query->whereRaw('date(created_at) = curdate()');
            case Goal::TYPE_WEEKLY:
                return $query->whereRaw('yearweek(created_at, 1) = yearweek(curdate(), 1)')
                    ->whereRaw('year(created_at) = year(curdate())');
            case Goal::TYPE_MONTHLY:
                return $query->whereRaw('month(created_at) = month(curdate())')
                    ->whereRaw('year(created_at) = year(curdate())');
            default:
                return 0;
        }
    }
}

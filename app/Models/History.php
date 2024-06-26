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

    public function scopeNotToday($query)
    {
        return $query->whereRaw('date(created_at) != current_date()');
    }

    public function scopeToday($query)
    {
        return $query->whereRaw('date(created_at) = current_date()');
    }

    public function scopeThisWeek($query)
    {
        return $query->whereRaw('yearweek(created_at, 1) = yearweek(curdate(), 1)')
            ->whereRaw('year(created_at) = year(curdate())');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereRaw('month(created_at) = month(curdate())')
            ->whereRaw('year(created_at) = year(curdate())');
    }

    public function scopeUserItems($query, $userid)
    {
        return $query->where('userid', $userid);
    }

    public function scopeGoalType($query, $typeid = 1)
    {
        switch($typeid) {
            case Goal::TYPE_DAILY:
                return $query->today();
            case Goal::TYPE_WEEKLY:
                return $query->thisWeek();
            case Goal::TYPE_MONTHLY:
                return $query->thisMonth();
            default:
                return 0;
        }
    }
}

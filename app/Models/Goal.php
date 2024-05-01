<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Goal extends Model
{
    const TYPE_DAILY = 1;
    const TYPE_WEEKLY = 2;
    const TYPE_MONTHLY = 3;

    protected $guarded = ['id'];

    public function Group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'groupid');
    }

    public function getText($points = 0): string
    {
        $list = [
            self::TYPE_DAILY => __('a day'),
            self::TYPE_WEEKLY => __('a week'),
            self::TYPE_MONTHLY => __('a month'),
        ];
        return sprintf("%d %s %d&#9733; %s", $points, __('of'), $this->points, $list[$this->typeid] ?? '');
    }
}

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

    public function getTypeText(): string
    {
        return self::typeText($this->typeid);
     }

     public static function typeText($type): string
     {
         $list = [
             self::TYPE_DAILY => __('daily'),
             self::TYPE_WEEKLY => __('weekly'),
             self::TYPE_MONTHLY => __('monthly'),
         ];
         return $list[$type] ?? '';
     }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    const TYPE_DAILY = 1;
    const TYPE_WEEKLY = 2;
    const TYPE_MONTHLY = 3;

}

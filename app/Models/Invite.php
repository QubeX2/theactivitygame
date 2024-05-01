<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invite extends Model
{
    protected $table = 'invitations';
    protected $guarded = ['id'];

    public function Group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function User(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

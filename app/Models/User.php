<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\BelongsToRelationship;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function OwnedGroup(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'id', 'ownerid');
    }

    public function Group(): HasOneThrough
    {
        return $this->hasOneThrough(Group::class, Member::class, 'userid', 'id', 'id', 'groupid');
    }

    public function Member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'id', 'userid');
    }

    public function History(): HasMany
    {
        return $this->hasMany(History::class, 'userid');
    }

    public function goalFullfilled(): bool
    {
        return $this->getPoints() >= $this->group->goal->points;
    }

    public function getPoints()
    {
        switch(auth()->user()->group->goal->typeid) {
            case Goal::TYPE_DAILY:
                return auth()->user()->history()
                    ->whereRaw('date(created_at) = curdate()')
                    ->sum('points');
            case Goal::TYPE_WEEKLY:
                return auth()->user()->history()
                    ->whereRaw('yearweek(created_at) = yearweek(curdate())')
                    ->whereRaw('year(created_at) = year(curdate())')
                    ->sum('points');
            case Goal::TYPE_MONTHLY:
                return auth()->user()->history()
                    ->whereRaw('month(created_at) = month(curdate())')
                    ->whereRaw('year(created_at) = year(curdate())')
                    ->sum('points');
            default:
                return 0;
        }
    }
}

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

    public function Goal(): HasOne
    {
        return $this->hasOne(Goal::class, 'userid');
    }

    public function Member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'id', 'userid');
    }

    public function History(): HasMany
    {
        return $this->hasMany(History::class, 'userid');
    }

    public function getInfo(): object
    {
        $info = (object)[
            'userid' => $this->id,
            'groupid' => $this->group?->id ?? 0,
            'points' => $this->getPoints(),
            'goal' => $this->goal?->points ?? 0,
            'goalType' => $this->goal?->typeid ?? 0,
            'goalTypeText' => $this->goal?->getTypeText() ?? '',
            'left' => max(0, ($this->goal?->points - $this->getPoints() ?? 0)),
            'mandatory' => Activity::whereDoesntHave('history', fn($q) => $q->today()->userItems($this->id))
                ->where('groupid', ($this->group?->id ?? 0))->where('mandatory', true)->count(),
            'weekly' => [0, 0, 0, 0, 0, 0, 0],
            'history' => History::with(['activity'])->whereRaw('date(created_at) = current_date()')
                ->where('userid', $this->id)->orderBy('created_at')->get()->toArray(),
        ];
        $history =History::whereRaw('yearweek(created_at, 1) = yearweek(curdate(), 1)')
            ->selectRaw('sum(points) as points, weekday(created_at) as day')->groupBy('day')->pluck('points', 'day')->toArray();
        foreach($info->weekly as $day => $points) {
            $info->weekly[$day] = (int)($history[$day] ?? 0);
        }
        return $info;
    }

    public function getPoints()
    {
        switch($this->goal?->typeid ?? 0) {
            case Goal::TYPE_DAILY:
                return $this->history()
                    ->whereRaw('date(created_at) = curdate()')
                    ->sum('points');
            case Goal::TYPE_WEEKLY:
                return $this->history()
                    ->whereRaw('yearweek(created_at, 1) = yearweek(curdate(), 1)')
                    ->whereRaw('year(created_at) = year(curdate())')
                    ->sum('points');
            case Goal::TYPE_MONTHLY:
                return $this->history()
                    ->whereRaw('month(created_at) = month(curdate())')
                    ->whereRaw('year(created_at) = year(curdate())')
                    ->sum('points');
            default:
                return 0;
        }
    }
}

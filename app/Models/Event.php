<?php

namespace App\Models;

use App\Models\Scopes\EventScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'date',
        'time',
        'event_for',
        'event_type_id',
        'event_condition',
        'created_by',
        'created_to'
    ];

    public const MYSELF = 'MYSELF';
    public const OTHER = 'OTHER';

    public const PAST = 'PAST';
    public const TODAY = 'TODAY';
    public const UPCOMING = 'UPCOMING';

    protected static function booted()
    {
        static::addGlobalScope(new EventScope);
    }

    public function eventType()
    {
        return $this->belongsTo(EventType::class);
    }
    public function eventUsers()
    {
        return $this->hasMany(EventUser::class);
    }
    public function galleries()
    {
        return $this->hasMany(EventGallery::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'event_users')
            ->withPivot('status', 'deleted_at')
            ->wherePivotNull('deleted_at')
            ->withTimestamps();
    }

    public function rejectedUsers()
    {
    return $this->belongsToMany(User::class, 'event_users')
        ->withPivot('status', 'deleted_at')
        ->wherePivotNotNull('deleted_at')
        ->withTimestamps();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function createdTo()
    {
        return $this->belongsTo(User::class, 'created_to');
    }
    public function getStatusAttribute()
    {
        $eventDateTime = \Carbon\Carbon::parse($this->date . ' ' . $this->time);

        if ($eventDateTime->isPast()) {
            return Event::PAST;
        } elseif ($eventDateTime->isToday()) {
            return Event::TODAY;
        } elseif ($eventDateTime->isFuture()) {
            return Event::UPCOMING;
        }
    }
    public function scopeUpcoming($query)
    {
        return $query->where(function ($q) {
            $q->where('date', '>', now()->toDateString())
            ->orWhere(function ($q2) {
                $q2->where('date', '=', now()->toDateString())
                    ->where('time', '>=', now()->format('H:i:s'));
            });
        });
    }
}

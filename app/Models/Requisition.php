<?php

namespace App\Models;

use App\Models\Scopes\RequisitionScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Requisition extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'event_id',
        'visibility',
        'created_by',
    ];

    public const PUBLIC = false;
    public const PRIVATE = true;

    protected static function booted()
    {
        static::addGlobalScope(new RequisitionScope);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
    public function items()
    {
        return $this->hasMany(RequisitionItem::class);
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

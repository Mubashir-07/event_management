<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequisitionItem extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'requisition_id',
        'item',
        'is_claimed',
        'claimed_by',
        'optional',
    ];

    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }
    public function claimedBy()
    {
        return $this->belongsTo(User::class, 'claimed_by');
    }
    
}

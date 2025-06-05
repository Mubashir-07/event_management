<?php

namespace App\Models\Scopes;

use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class EventScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
       $builder->where(function ($query) {
            $query->where('created_to', auth()->user()->id)
                ->orWhere('created_by',auth()->user()->id)
                ->orWhereHas('users', function ($query) {
                    $query->where('user_id', auth()->user()->id);
                });
        });
    }
}

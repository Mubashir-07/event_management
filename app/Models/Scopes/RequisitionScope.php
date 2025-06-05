<?php

namespace App\Models\Scopes;

use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class RequisitionScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where(function ($query) {
            $query->where('visibility', 0)
                ->orWhere(function($query){
                    $query->whereHas('event',function($query){
                        $query->where('created_by', auth()->user()->id)
                            ->orWhere('created_to', auth()->user()->id);
                    })
                    ->orWhereHas('event.eventUsers', function ($query) {
                        $query->where('user_id', auth()->user()->id)
                            ->where('status', 1)
                            ->where('deleted_at', null);
                    });
                });
        });
    }
}

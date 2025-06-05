<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;

class RequisitionPolicy
{
    public function claim(User $user, Requisition $requisition): bool
    {
        if (!Gate::allows('upcomingEvent', $requisition->event)) {
            return false;
        }

      
        $exist = $requisition->event()
            ->where(function ($query) use ($user) {
                $query->where(function($query)use($user){
                    $query->where('created_by', $user->id);

                    })
                    ->orWhereHas('eventUsers', function ($subQuery) use ($user) {
                    $subQuery->where('user_id', $user->id)
                        ->where('status', 1)
                        ->where('deleted_at', null);
                    });
            })
            ->exists();

            if($exist){
                return true;
            }
            return false;
    }
}

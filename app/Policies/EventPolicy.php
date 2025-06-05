<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Access\Response;

class EventPolicy
{

    /**
     * Determine whether user can see some values.
     */
    public function creatorPrivilege(User $user, Event $event): bool
    {
        if ($event->created_by == $user->id || $event->created_to == $user->id) {
            return true;
        }
        return false;
    }

    public function upcomingEvent(User $user, Event $event): bool
    {
        $exist = Carbon::createFromFormat('Y-m-d H:i:s', $event->date . ' ' . $event->time)->isFuture();

        if ($exist) {
            return true;
        }

        return false;
    }
}

<?php

use App\Models\EventType;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use App\Models\Event;

new class extends Component {
    public $events = [];

    public $eventTypes = [];

    public $users = [];

    public array $invited_users = [];

    public $otherUsers = [];


    public function mount(): void
    {
        $this->events = Event::orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->where(function ($query) {
                $userId = Auth::id();

                $query->where('created_by', $userId)
                    ->orWhere('created_to', $userId)
                    ->orWhereHas('users', function ($query) use ($userId) {
                        $query->where('user_id', $userId)->where('status', 1);
                    });
            })
            ->with(['eventType', 'createdBy', 'users', 'rejectedUsers'])
            ->get();

        $this->eventTypes = EventType::active()->get();
    }

    public function inviteUsers($eventId)
    {
        $event = Event::find($eventId);
        $event->users()->sync($this->invited_users);

        $this->dispatch('item-claimed', itemId: $eventId);
        $this->redirect(route('event.index'), navigate: true);
    }

    public function updatedEventFor($value)
    {
        if ($value == Event::OTHER) {
            $this->otherUsers = User::where('id', '!=', auth()->user()->id)
                ->get();
        } else {
            $this->otherUsers = [];
        }
    }

    public function deleteEvent(int $id): void
    {
        $event = Event::find($id);
        if ($event) {
            if ($event->eventUsers()->exists()) {
                $this->dispatch('event-deletion-failed', id: $id);
                return;
            }
            $event->delete();
            $this->dispatch('event-type-deleted', id: $id);
            $this->redirect(route('event.index'), navigate: true);
        }
    }

};

?>
<section>
    <x-table>
        <x-slot:headers>
            <x-table-header>Title</x-table-header>
            <x-table-header>Date</x-table-header>
            <x-table-header>Time</x-table-header>
            <x-table-header>Event For</x-table-header>
            <x-table-header>Event Type</x-table-header>
            <x-table-header>Event Condition</x-table-header>
            <x-table-header>Status</x-table-header>
            <x-table-header>Created By</x-table-header>
            <x-table-header>Created To</x-table-header>
            <x-table-header>Invited users</x-table-header>
            <x-table-header>Gallery</x-table-header>
            <x-table-header colspan="3">Action</x-table-header>
        </x-slot:headers>
        <x-slot:rows>
            @forelse ($events as $event)
                <tr class="border border-gray-300">
                    <x-table-data>{{ $event->title }}</x-table-data>
                    <x-table-data>{{ $event->date }}</x-table-data>
                    <x-table-data>{{ $event->time }}</x-table-data>
                    <x-table-data>{{ $event->event_for }}</x-table-data>
                    <x-table-data>{{ $event->eventType?->name }}</x-table-data>
                    <x-table-data>{{ $event->event_condition }}</x-table-data>
                    <x-table-data>{{$event->status}}</x-table-data>
                    <x-table-data>{{ $event->createdBy?->name }}</x-table-data>
                    <x-table-data>{{ $event->createdTo?->name }}</x-table-data>
                    <x-table-data>
                        <x-primary-button
                            x-data
                            x-on:click="$dispatch('open-modal', 'user-list-modal-{{ $event->id }}')"
                        >
                            View Users
                        </x-primary-button>


                        <x-modal name="user-list-modal-{{ $event->id }}">
                            <div class="p-6">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Users for Event: {{ $event->title }}
                                </h2>

                                <div class="mt-4 text-gray-800 dark:text-gray-200 text-sm">
                                    @forelse($event->users as $user)
                                        • {{ $user->name }} @can('creatorPrivilege',$event)
                                            - Status: {{ $user->pivot->status == 0 ? 'Pending':'Accepted' }}
                                        @endcan <br>
                                    @empty
                                        No users assigned.
                                    @endforelse

                                    {{-- For creators only --}}
                                    @can('creatorPrivilege',$event)
                                        <h3 class="mt-4 font-semibold">Rejected Users:</h3>
                                        @forelse($event->rejectedUsers as $user)
                                            • {{ $user->name }} <br>
                                        @empty
                                            No users rejected.
                                        @endforelse
                                    @endcan

                                </div>

                                <div class="mt-6 flex justify-end">
                                    <x-secondary-button
                                        x-on:click="$dispatch('close-modal', 'user-list-modal-{{ $event->id }}')">
                                        Close
                                    </x-secondary-button>
                                </div>
                            </div>
                        </x-modal>
                    </x-table-data>
                    <x-table-data>
                        <a href="{{ route('gallery.index',$event->id) }}">
                            <x-secondary-button>
                                {{ __('Gallery') }}
                            </x-secondary-button>
                        </a>
                    </x-table-data>
                    <x-table-data>
                        @can('creatorPrivilege', $event)
                            <x-primary-button
                                x-data
                                x-on:click="$dispatch('open-modal', 'invite-user-modal-{{ $event->id }}')"
                                class="text-lg px-6 py-3">

                                Invite
                            </x-primary-button>
                        @endcan

                        <x-modal name="invite-user-modal-{{ $event->id }}">
                            <div class="p-6">
                                <form wire:submit.prevent="inviteUsers({{ $event->id }})" class="p-6">
                                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        Users :
                                        <x-select wire:model="invited_users" id="invited_users" name="invited_users[]"
                                                  multiple
                                                  class="mt-1 block w-full">
                                            <option value="">{{__('Select')}}</option>
                                            @foreach(User::query()->where('id','!=', $event->created_by)->where('id','!=', $event->created_to)
                                            ->whereDoesntHave('invitedEvents')
                                            ->orWhereHas('invitedEvents', function ($query) use($event) {
                                                  $query->where('event_id', '!=',$event->id);
                                            })->get() as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </x-select>
                                    </h2>
                                    <div class="mt-6 flex justify-end">
                                        <x-secondary-button
                                            x-on:click="$dispatch('close-modal', 'invite-user-modal-{{ $event->id }}')">
                                            Cancel
                                        </x-secondary-button>
                                        <x-primary-button>Invite</x-primary-button>
                                    </div>
                                </form>
                            </div>
                        </x-modal>
                    </x-table-data>
                    <x-table-data>
                        @can('creatorPrivilege', $event)
                            <a href="{{ route('event.edit',$event->id) }}">
                                <x-secondary-button>
                                    {{ __('Edit Event') }}
                                </x-secondary-button>
                            </a>
                        @endcan
                    </x-table-data>
                    <x-table-data>
                        @can('creatorPrivilege', $event)
                            <button wire:click="deleteEvent({{$event->id}})" class="text-red-500 hover:text-red-700">
                                Delete
                            </button>
                        @endcan
                    </x-table-data>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-gray-500 px-4 py-2">No events found.</td>
                </tr>
            @endforelse
        </x-slot:rows>
    </x-table>
    <x-action-message class="me-3" on="event-deletion-failed">
        {{ __('Event Deletion Failed. Users has been invited for this event.') }}
    </x-action-message>
</section>

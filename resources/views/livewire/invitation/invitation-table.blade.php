<?php

use Livewire\Volt\Component;
use App\Models\Event;

new class extends Component {
  public $events = [];

  public function getInvitations(){
    return Event::orderBy('date', 'asc')
        ->orderBy('time', 'asc')
        ->whereHas('eventUsers', function ($query) {
            $query->where('user_id', auth()->user()->id)
                ->where('status', 0);
        })
        ->with(['eventType', 'createdBy'])
        ->get();
  }
  public function mount(): void
  {
    $this->events = $this->getInvitations();
  }

  public function accept(int $id): void
  {
      $event = Event::find($id);
      if($event)
        $event->users()->where('user_id', auth()->user()->id)->update(['status' => 1]);

      $this->dispatch('event-accepted', id: $id);
      $this->events = $this->getInvitations();

  }
  public function reject(int $id): void
  {
      $event = Event::find($id);
      if($event)
        $event->eventUsers()->where('user_id', auth()->user()->id)->delete();

      $this->dispatch('event-rejected', id: $id);
      $this->events = $this->getInvitations();
  }
};

?>
<section>
  <x-table>
    <x-slot:headers>
      <x-table-header>Title</x-table-header>
      <x-table-header>Date</x-table-header>
      <x-table-header>Time</x-table-header>
      <x-table-header>Event Type</x-table-header>
      <x-table-header>Event Condition</x-table-header>
      <x-table-header>Status</x-table-header>
      <x-table-header>Created By</x-table-header>
      <x-table-header>Action</x-table-header>
    </x-slot:headers>
    <x-slot:rows>
      @forelse ($events as $event)
        <tr class="border border-gray-300">
          <x-table-data>{{ $event->title }}</x-table-data>
          <x-table-data>{{ $event->date }}</x-table-data>
          <x-table-data>{{ $event->time }}</x-table-data>
          <x-table-data>{{ $event->eventType?->name }}</x-table-data>
          <x-table-data>{{ $event->event_condition }}</x-table-data>
          <x-table-data>{{$event->status}}</x-table-data>
          <x-table-data>{{ $event->createdBy?->name }}</x-table-data>
          <x-table-data>
          <div class="flex space-x-2">
            @can('upcomingEvent', $event)
              <x-primary-button wire:click="accept({{ $event->id }})">
                Accept
              </x-primary-button>
              <x-danger-button wire:click="reject({{ $event->id }})">
                Reject
              </x-danger-button>
            @endcan
          </div>
          </x-table-data>
        </tr>
      @empty
        <tr>
          <td colspan="7" class="text-center text-gray-500 px-4 py-2">No invitation found.</td>
        </tr>
      @endforelse
    </x-slot:rows>
  </x-table>
  <x-action-message class="me-3" on="event-accepted">
    {{ __('Invitation Accepted.') }}
  </x-action-message>
  <x-action-message class="me-3" on="event-rejected">
    {{ __('Invitation Rejected.') }}
  </x-action-message>
</section>

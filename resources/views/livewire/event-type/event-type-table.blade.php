<?php

use Livewire\Volt\Component;
use App\Models\EventType;

new class extends Component {
  public $eventTypes = [];
  public function mount(): void
  {
    $this->eventTypes = EventType::all();
  }
  public function updateStatus(int $id):void{
      $eventType = EventType::find($id);
      if ($eventType) {
          $eventType->is_active = !$eventType->is_active;
          $eventType->save();
          $this->dispatch('status-updated', id: $id, status: $eventType->is_active);
      }
  }
  public function deleteEventType(int $id): void
  {
      $eventType = EventType::find($id);
      if ($eventType) {
          if ($eventType->events()->exists()) {
              $this->dispatch('event-type-deletion-failed', id: $id);
              return;
          }
          $eventType->delete();
          $this->dispatch('event-type-deleted', id: $id);
          $this->eventTypes = EventType::all();
      }
  }

};

?>
<section>
  <x-table>
    <x-slot:headers>
      <th class="border border-gray-300 px-4 py-2">Title</th>
      <th class="border border-gray-300 px-4 py-2">Status</th>
      <th class="border border-gray-300 px-4 py-2">Actions</th>
    </x-slot:headers>
  
    <x-slot:rows>
      @forelse ($eventTypes as $eventType)
        <tr class="border border-gray-300">
          <td class="border border-gray-300 px-4 py-2">{{ $eventType->name }}</td>
          <td class="border border-gray-300 px-4 py-2">
              <input type="checkbox" {{ $eventType->is_active ? 'checked' : '' }} wire:click="updateStatus({{$eventType->id}})">
          </td>
          <td class="border border-gray-300 px-4 py-2">
              <button wire:click="deleteEventType({{$eventType->id}})" class="text-red-500 hover:text-red-700">Delete</button>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="2" class="text-center text-gray-500 px-4 py-2">No event types found.</td>
        </tr>
      @endforelse
    </x-slot:rows>
  </x-table>
  <x-action-message class="me-3" on="status-updated">
    {{ __('Status Updated.') }}
  </x-action-message>  
  <x-action-message class="me-3" on="event-type-deleted">
    {{ __('Event Type Deleted.') }}
  </x-action-message>
  <x-action-message class="me-3" on="event-type-deletion-failed">
    {{ __('Event Type Deletion Failed. It is used in some events.') }}
  </x-action-message>
</section>

<?php

use Livewire\Volt\Component;
use App\Models\Event;
use App\Models\Requisition;

new class extends Component {

    public $events = [];
    public $items = [];
    public $event = '';
    public $event_visibility = '';

    public function mount(): void
    {
      $this->events = Event::upcoming()
          ->orderBy('date', 'asc')
          ->orderBy('time', 'asc')
          ->where(function($query)  {
            $query->where('created_by', auth()->user()->id)->orWhere('created_to', auth()->user()->id)
                ->orWhereHas('eventUsers', function ($subQuery) {
                    $subQuery->where('user_id', auth()->user()->id)
                        ->where('status', 1);
                });
          })
          ->get();

      $this->items[] = '';

    }
    public function addRow()
    {
        $this->items[] = '';
    }

    public function removeRow($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function createRequisition():void
    {
        $validated = $this->validate([
            'event' => ['required', 'exists:events,id'],
            'event_visibility' => ['required', 'boolean'],
            'items.*' => ['required', 'string', 'max:255'],
        ]);

        DB::beginTransaction();
        try {
            $requisition = Requisition::create([
                'event_id' => $validated['event'],
                'visibility' => $validated['event_visibility'],
                'created_by' => auth()->user()->id,
            ]);


            foreach ($validated['items'] as $item) {
            $requisition->items()->create([
                'item' => $item,
            ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            info('Error creating requisition: ' . $e->getMessage());
            DB::rollBack();
            session()->flash('error', 'Failed to create requisition. Please try again.');
            return;
        }

        $this->reset('event', 'event_visibility', 'items');
        $this->dispatch('requisition-created', title: $this->event);
        $this->redirect(route('requisition.index'), navigate: true);

    }



}; ?>

<section>
    <form wire:submit="createRequisition" class="mt-6 space-y-6">
      <div>
        <x-input-label for="event" :value="__('Event Type')" />
        <x-select wire:model="event" id="event" name="event" class="mt-1 block w-full" required>
            <option value="" disabled selected>{{ __('Select Event') }}</option>
            @foreach($events as $event)
                <option value="{{ $event->id }}">{{ $event->title }}</option>
            @endforeach
        </x-select>
        <x-input-error class="mt-2" :messages="$errors->get('event')" />
    </div>
    <div>
        <x-input-label for="event_visibility" :value="__('Event Visibility')" />
        <x-select wire:model="event_visibility" id="event_visibility" name="event_visibility" class="mt-1 block w-full" required>
            <option value="" disabled selected>{{ __('Select Event Visibility') }}</option>
            <option value="0">Public</option>
            <option value="1">Private</option>
        </x-select>
        <x-input-error class="mt-2" :messages="$errors->get('event_visibility')" />
    </div>
        <x-table>
            <x-slot:headers>
                <x-table-header>Item</x-table-header>
                <x-table-header>Action</x-table-header>
            </x-slot:headers>
            <x-slot:rows>
                @foreach($items as $index => $item)
                    <tr>
                        <x-table-data>
                            <x-text-input wire:model="items.{{ $index }}" class="mt-1 block w-full" required />
                        </x-table-data>
                        <x-table-data>
                            @if($index == 0)
                                <x-primary-button type="button" wire:click="addRow" class="btn btn-success">+</x-primary-button>
                            @else
                                <x-danger-button type="button" wire:click="removeRow({{ $index }})" class="btn btn-danger">-</x-danger-button>
                            @endif
                        </x-table-data>
                    </tr>
                @endforeach
            </x-slot:rows>
        </x-table>


        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
        </div>
        <x-action-message class="me-3" on="requisition-created">
            {{ __('Saved.') }}
        </x-action-message>
        <x-session-error/>
    </form>
</section>

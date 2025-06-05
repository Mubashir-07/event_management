<?php

use Illuminate\Support\Facades\DB;
use Livewire\Volt\Component;
use App\Models\EventType;
use App\Models\Event;
use App\Models\User;

new class extends Component {
    public string $title = '';
    public string $date = '';
    public string $time = '';
    public string $event_type = '';
    public string $event_for = '';
    public string $event_for_others = '';
    public string $event_condition = '';
    public string $created_to = '';

    public $eventTypes = [];
    public $otherUsers = [];

    public function mount(): void
    {
        $this->eventTypes = EventType::active()->get();
    }

    public function createEvent(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time' => ['required', 'date_format:H:i'],
            'event_type' => ['required', 'exists:event_types,id'],
            'event_for' => ['required', 'in:' . Event::MYSELF . ',' . Event::OTHER],
            'event_for_others' => ['sometimes'],
            'created_to' => ['sometimes'],
            'event_condition' => ['nullable', 'string'],
        ]);

//        dd($validated, $validated['created_to']);

        Event::create([
            'title' => $validated['title'],
            'date' => $validated['date'],
            'time' => $validated['time'],
            'event_type_id' => $validated['event_type'],
            'event_for' => $validated['event_for'],
            'event_condition' => $validated['event_condition'],
            'created_by' => auth()->user()->id,
            'created_to' => $validated['created_to'] ?? auth()->user()->id,
        ]);

        $this->reset('title', 'date', 'time', 'event_type', 'event_for', 'event_for_others', 'event_condition');
        $this->dispatch('event-created', title: $this->title);
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

}; ?>

<section>
    <form wire:submit="createEvent" class="mt-6 space-y-6">
        <div>
            <x-input-label for="title" :value="__('Title')"/>
            <x-text-input wire:model="title" id="title" name="title" type="text" class="mt-1 block w-full" required
                          autofocus autocomplete="title"/>
            <x-input-error class="mt-2" :messages="$errors->get('title')"/>
        </div>

        <div>
            <x-input-label for="date" :value="__('Date')"/>
            <x-text-input wire:model="date" id="date" name="date" type="date" class="mt-1 block w-full" required
                          autocomplete="date"/>
            <x-input-error class="mt-2" :messages="$errors->get('date')"/>
        </div>

        <div>
            <x-input-label for="time" :value="__('Time')"/>
            <x-text-input wire:model="time" id="time" name="time" type="time" class="mt-1 block w-full" required
                          autocomplete="time"/>
            <x-input-error class="mt-2" :messages="$errors->get('time')"/>
        </div>

        <div>
            <x-input-label for="event_type" :value="__('Event Type')"/>
            <x-select wire:model="event_type" id="event_type" name="event_type" class="mt-1 block w-full" required>
                <option value="" disabled selected>{{ __('Select Event Type') }}</option>
                @foreach($eventTypes as $eventType)
                    <option value="{{ $eventType->id }}">{{ $eventType->name }}</option>
                @endforeach
            </x-select>
            <x-input-error class="mt-2" :messages="$errors->get('event_type')"/>
        </div>

        <div>
            <x-input-label for="event_for" :value="__('Event For')"/>
            <x-select wire:model.live="event_for" id="event_for" name="event_for" class="mt-1 block w-full" required>
                <option value="" disabled selected>{{ __('Select Event For') }}</option>
                <option value="{{Event::MYSELF}}">{{Event::MYSELF}}</option>
                <option value="{{Event::OTHER}}">{{Event::OTHER}}</option>
            </x-select>
            <x-input-error class="mt-2" :messages="$errors->get('event_for')"/>
        </div>

        @if($otherUsers)
            <div>
                <x-input-label for="created_to" :value="__('Event For Others')"/>
                <x-select wire:model="created_to" id="created_to" name="created_to"
                          class="mt-1 block w-full">
                    <option value="">{{__('Select')}}</option>
                    @foreach($otherUsers as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </x-select>
                <x-input-error class="mt-2" :messages="$errors->get('created_to')"/>
            </div>
        @endif

        <div>
            <x-input-label for="event_condition" :value="__('Event Condition')"/>
            <x-textarea wire:model="event_condition" id="event_condition" name="event_condition"
                        class="mt-1 block w-full"></x-textarea>
            <x-input-error class="mt-2" :messages="$errors->get('event_condition')"/>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            <x-action-message class="me-3" on="event-created">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
        <x-session-error/>
    </form>
</section>

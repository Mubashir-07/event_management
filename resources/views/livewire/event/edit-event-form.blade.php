<?php

use Illuminate\Support\Facades\DB;
use Livewire\Volt\Component;
use App\Models\EventType;
use App\Models\Event;
use App\Models\User;

new class extends Component {

    public Event $event;
    public string $title = '';
    public string $date = '';
    public string $time = '';
    public string $event_type = '';
    public string $event_for = '';
    public string $created_to = '';
    public string $event_condition = '';

    public $eventTypes = [];
    public $otherUsers = [];

    public function mount(Event $event): void
    {
        $this->event = $event;

        $this->title = $event->title;
        $this->date = $event->date;
        $this->time = $event->time;
        $this->event_type = $event->event_type_id;
        $this->event_for = $event->event_for;
        $this->event_condition = $event->event_condition;
        $this->created_to = $event->created_to;
        $this->eventTypes = EventType::active()->get();

        if ($this->event_for == Event::OTHER) {
            $this->otherUsers = User::where('id', '!=', $event->created_by)->orWhere('id','!=', $event->created_to)->get();
        }
    }

    public function updateEvent(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time' => ['required'],
            'event_type' => ['required', 'exists:event_types,id'],
            'event_for' => ['required', 'in:' . Event::MYSELF . ',' . Event::OTHER],
            'created_to' => ['sometimes'],
            'event_condition' => ['nullable', 'string'],
        ]);

        Event::query()->updateOrCreate(['id' => $this->event->id],[
            'title' => $validated['title'],
            'date' => $validated['date'],
            'time' => $validated['time'],
            'event_type_id' => $validated['event_type'],
            'event_for' => $validated['event_for'],
            'event_condition' => $validated['event_condition'],
            'created_to' => $validated['event_for'] == 'OTHER' ? $validated['created_to'] : auth()->user()->id,
        ]);

        $this->dispatch('event-updated', title: $this->title);
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
    <form wire:submit="updateEvent" class="mt-6 space-y-6">
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
                <option value="" disabled>{{ __('Select Event Type') }}</option>
                @foreach($eventTypes as $eventType)
                    <option value="{{ $eventType->id }}">{{ $eventType->name }}</option>
                @endforeach
            </x-select>
            <x-input-error class="mt-2" :messages="$errors->get('event_type')"/>
        </div>

        <div>
            <x-input-label for="event_for" :value="__('Event For')"/>
            <x-select wire:model.live="event_for" id="event_for" name="event_for" class="mt-1 block w-full" required>
                <option value="" disabled>{{ __('Select Event For') }}</option>
                <option value="{{Event::MYSELF}}">{{Event::MYSELF}}</option>
                <option value="{{Event::OTHER}}">{{Event::OTHER}}</option>
            </x-select>
            <x-input-error class="mt-2" :messages="$errors->get('event_for')"/>
        </div>

        @if($event_for === App\Models\Event::OTHER && $otherUsers)
            <div>
                <x-input-label for="created_to" :value="__('Event For Others')"/>
                <x-select wire:model="created_to" id="created_to" name="created_to"
                          class="mt-1 block w-full">
                    <option value="">{{ __('Select') }}</option>
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
            <x-primary-button>{{ __('Update') }}</x-primary-button>

            <x-action-message class="me-3" on="event-updated">
                {{ __('Updated.') }}
            </x-action-message>
        </div>
        <x-session-error/>
    </form>
</section>

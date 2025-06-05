<?php

use Livewire\Volt\Component;
use App\Models\EventType;

new class extends Component {
    public string $title = '';

    public function createEventType(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255','unique:event_types,name'],
        ]);

        EventType::create([
            'name' => $validated['title'],
        ]);

        $this->reset('title');

        $this->dispatch('event-type-created', title: $this->title);

        $this->redirect(route('event-type.index'), navigate: true);

    }
    

}; ?>

<section>
    <form wire:submit="createEventType" class="mt-6 space-y-6">
        <div>
            <x-input-label for="title" :value="__('Title')" />
            <x-text-input wire:model="title" id="title" name="title" type="text" class="mt-1 block w-full" required autofocus autocomplete="title" />
            <x-input-error class="mt-2" :messages="$errors->get('title')" />
        </div>
        

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            <x-action-message class="me-3" on="event-type-created">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>

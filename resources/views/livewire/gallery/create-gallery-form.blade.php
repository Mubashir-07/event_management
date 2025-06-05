<?php

use Livewire\Volt\Component;
use App\Models\EventGallery;
use Livewire\WithFileUploads;
use App\Models\Event;

new class extends Component {
    use WithFileUploads;

    public array $images = [];
    public Event $event;
    
    public function createGallery(): void
    {
        try {
            $validated = $this->validate([
                'images' => ['required', 'array'],
                'images.*' => ['image', 'mimes:jpeg,png,jpg,gif,svg'],
            ]);

            $eventId = $this->event->id; 
            $eventFolder = "event_$eventId";

            foreach ($validated['images'] as $image) {
                $filename = $image->hashName();
                $path = $image->store("event_gallery/{$eventFolder}", 'public');

                EventGallery::create([
                    'event_id' => $eventId,
                    'image_path' => $path,
                ]);
            }
            $this->dispatch('event-gallery-created');
            $this->redirect(route('gallery.index', $this->event->id), navigate: true);
        } catch (\Exception $e) {
            info('Error creating event gallery: ' . $e->getMessage());
            session()->flash('error', 'Failed to create event gallery. Please try again.');
        }
        $this->reset('images');
    }
    
}; ?>

<section>
    <form wire:submit="createGallery" class="mt-6 space-y-6" enctype="multipart/form-data">
        <div>
            <x-input-label for="images" :value="__('Images')" />
            <input wire:model="images" id="images" name="images[]" type="file" multiple accept="image/*" />
        </div>
        

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            <x-action-message class="me-3" on="event-gallery-created">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
    <x-session-error/>
</section>

<?php

use Livewire\Volt\Component;
use App\Models\Event;

new class extends Component {
    public  $items = [];
    public Event $event;


    public function mount(): void
    {
        $this->items = $this->event->galleries;
    }

    public function deleteGallery(int $id): void
    {
        $gallery = $this->event->galleries()->find($id);
        if ($gallery) {
            if ($gallery->image_path && Storage::disk('public')->exists($gallery->image_path)) {
                Storage::disk('public')->delete($gallery->image_path);
            }

            $gallery->delete();

            $this->items = $this->event->galleries;

            $this->dispatch('gallery-deleted', id: $id);
        }
    }
}; ?>

<div class="relative grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
    @foreach($items as $item)
        <div class="relative p-4 border rounded shadow hover:shadow-md transition">
            <x-danger-button
                wire:click="deleteGallery({{ $item->id }})"
                class="absolute top-2 right-2 text-xs px-2 py-1">
                Delete
            </x-danger-button>
            <a href="{{ asset('storage/'.$item->image_path) }}" target="_blank">
                <img
                    src="{{ asset('storage/'.$item->image_path) }}"
                    alt="Event Gallery"
                    class="w-full h-40 object-cover rounded"
                >
            </a>
        </div>
    @endforeach
</div>

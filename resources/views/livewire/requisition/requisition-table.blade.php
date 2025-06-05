<?php

use Livewire\Volt\Component;
use App\Models\Requisition;
use App\Models\RequisitionItem;

new class extends Component {
    public $requisitions = [];
    public $claimDetails = "";

    public function mount(): void
    {
        $this->requisitions = $this->getRequisitions();
        
    }
    public function getRequisitions()
    {
        return Requisition::orderBy('created_at', 'desc')
        ->with([
            'event' => fn ($query) => $query->withoutGlobalScopes(),
        ])
        ->get();
    }
    public function claim($itemId)
    {
        $item = RequisitionItem::find($itemId);
        $item->is_claimed = true;
        $item->claimed_by = auth()->user()->id;
        $item->optional = $this->claimDetails ?? '';
        $item->save();
        $this->claimDetails = "";
        $this->dispatch('item-claimed', itemId: $itemId);
        $this->requisitions = $this->getRequisitions();
    }
}; ?>



<section>
    <x-table>
      <x-slot:headers>
        <x-table-header>Event</x-table-header>
        <x-table-header>Status</x-table-header>
        <x-table-header>Visibility</x-table-header>
        <x-table-header>Created by</x-table-header>
        <x-table-header>Action</x-table-header>
      </x-slot:headers>
      <x-slot:rows>
        @forelse ($requisitions as $requisition)
        <tr>
            <x-table-data>{{ $requisition->event?->title }}</x-table-data>
            <x-table-data>{{ $requisition->event?->status }}</x-table-data>
            <x-table-data>{{ $requisition->visibility ? 'Private' : 'Public' }}</x-table-data>
            <x-table-data>{{ $requisition->createdBy?->name }}</x-table-data>
            <x-table-data>
                <x-primary-button
                x-data
                x-on:click="$dispatch('open-modal', 'item-modal-{{ $requisition->id }}')"
                class="text-lg px-6 py-3">

                View Items

            </x-primary-button>
            
              
            <x-modal name="item-modal-{{ $requisition->id }}">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Items for Event: {{ $requisition->event?->title }}
                    </h2>
            
                    <div class="mt-4 text-gray-800 dark:text-gray-200 text-sm">
                            @forelse($requisition->items as $item)
                            • {{ $item->item }}
                                @if (!$item->is_claimed)
                                    @can('claim', $requisition)
                                        <x-primary-button
                                            x-data
                                            x-on:click="$dispatch('open-modal', 'claim-modal-{{ $item->id }}')"
                                        >
                                            Claim
                                        </x-primary-button>

                                        <x-modal name="claim-modal-{{ $item->id }}">
                                            <form wire:submit.prevent="claim({{ $item->id }})" class="p-6">
                                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                    Claim Item: {{ $item->item }}
                                                </h2>

                                                <div class="mt-4">
                                                    <x-input-label for="claim-details-{{ $item->id }}" value="Details" />
                                                    <x-text-input
                                                        id="claim-details-{{ $item->id }}"
                                                        type="text"
                                                        wire:model.blur="claimDetails"
                                                        class="mt-1 block w-full"
                                                    />
                                                </div>

                                                <div class="mt-6 flex justify-end">
                                                    <x-secondary-button x-on:click="$dispatch('close-modal', 'claim-modal-{{ $item->id }}')">
                                                        Cancel
                                                    </x-secondary-button>
                                                    <x-primary-button >Claim</x-primary-button>
                                                </div>
                                            </form>
                                        </x-modal>
                                    @else
                                        <span class="text-yellow-600">Pending</span>
                                    @endcan
                                @else
                                    <span class="text-green-600">Claimed</span>
                                @endif
                                {{$item->optional ? 'Optional :'. $item->optional : ''}}
                            <br>
                            @empty
                                No items.
                            @endforelse                            
                    </div>
                    <div class="mt-6 flex justify-end">
                        <x-secondary-button x-on:click="$dispatch('close-modal', 'item-modal-{{ $requisition->id }}')">
                            Close
                        </x-secondary-button>
                    </div>
                </div>
            </x-modal>
            </x-table-data>
        </tr>
          
        @empty
          <tr>
            <td colspan="7" class="text-center text-gray-500 px-4 py-2">No invitation found.</td>
          </tr>
        @endforelse
      </x-slot:rows>
    </x-table>
    <x-action-message class="me-3" on="item-claimed">
      {{ __('Claimed.') }}
    </x-action-message>
  </section>
  
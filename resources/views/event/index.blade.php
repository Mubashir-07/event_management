<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          {{ __('Event') }}
      </h2>
  </x-slot>

  <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6 text-gray-900 dark:text-gray-100">
                  <a href="{{ route('event.create') }}">
                    <x-secondary-button>
                      {{ __('Create Event') }}
                    </x-secondary-button>
                  </a>
              </div>
              <div class="p-6 text-gray-900 dark:text-gray-100">
                <livewire:event.event-table />
              </div>
          </div>
      </div>
  </div>
</x-app-layout>

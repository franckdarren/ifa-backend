<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight mt-4 md:mt-0">
            {{ __('Liste des catégories') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-x-auto w-full">
                @livewire('list-categorie')
            </div>
        </div>
    </div>
</x-app-layout>

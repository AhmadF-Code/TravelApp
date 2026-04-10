<x-filament-panels::page>
    <form wire:submit="submit">
        {{ $this->form }}

        <div class="mt-6 flex items-center gap-4">
            <x-filament::button type="submit" color="danger">
                Proses Refund Sekarang
            </x-filament::button>

            <x-filament::button color="gray" :href="App\Filament\Resources\BookingResource::getUrl('view', ['record' => $record])" tag="a">
                Batal
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>

<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">Informasi Jadwal</x-slot>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <span class="text-gray-500 block text-xs uppercase font-bold tracking-tight mb-1">Paket Trip</span>
                    <span class="text-sm font-semibold text-gray-900 line-clamp-1">{{ $this->schedule->trip->title }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs uppercase font-bold tracking-tight mb-1">Keberangkatan</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $this->schedule->departure_date->format('d M Y') }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs uppercase font-bold tracking-tight mb-1">Kepulangan</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $this->schedule->return_date->format('d M Y') }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs uppercase font-bold tracking-tight mb-1">Kuota Pax</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $this->schedule->quota }} Pax</span>
                </div>
            </div>
        </x-filament::section>

        <div>
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>

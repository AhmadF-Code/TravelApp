<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Jadwal Keberangkatan Terdekat (Stable Mode)</x-slot>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Trip Package</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Keberangkatan</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Kuota Sisa</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($this->schedules as $s)
                    <tr class="hover:bg-gray-50/10 transition-colors">
                        <td class="px-4 py-3">
                            <div class="text-sm font-bold text-gray-900 line-clamp-1">{{ optional($s->trip)->title }}</div>
                            <div class="text-[10px] text-gray-500 uppercase">{{ optional($s->trip)->destination_country }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm font-semibold text-gray-800">{{ $s->departure_date->format('d M Y') }}</div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $booked = $s->travelers()->whereIn('booking_travelers.status', ['paid', 'pending'])->count();
                                $remaining = $s->quota - $booked;
                                $color = $remaining < 3 ? 'text-rose-600 font-black' : 'text-emerald-600';
                            @endphp
                            <div class="text-sm {{ $color }} font-bold">{{ $remaining }} Pax</div>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <x-filament::button size="xs" color="gray" :href="App\Filament\Resources\ScheduleResource::getUrl('manifest', ['record' => $s->id])" tag="a" icon="heroicon-m-users">
                                Manifest
                            </x-filament::button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-400 text-xs italic">Belum ada keberangkatan terjadwal.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

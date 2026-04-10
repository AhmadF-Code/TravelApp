<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">Informasi Jadwal (Stable Mode)</x-slot>
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

        <!-- MANUAL TABS (STABLE) -->
        <div class="flex items-center gap-1 p-1 bg-gray-100 rounded-lg w-fit border border-gray-200">
            @foreach(['all' => 'Semua', 'paid' => 'Sudah Bayar', 'pending' => 'Menunggu', 'cancelled' => 'Dibatalkan'] as $key => $label)
                <a href="?status={{ $key }}" 
                   class="px-4 py-2 text-sm font-medium rounded-md transition-all {{ $statusFilter == $key ? 'bg-white text-gray-900 shadow-sm border border-gray-200' : 'text-gray-500 hover:text-gray-700' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <div class="overflow-x-auto bg-white border border-gray-200 rounded-xl shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-600">ID Peserta</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-600">Nama</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-600">Group Booking</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-600">Status</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-600 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($this->travelers as $t)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 text-sm font-bold text-gray-700 uppercase">{{ $t->traveler_code }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-gray-900">{{ $t->name }}</div>
                            <div class="text-xs text-gray-500">{{ $t->phone }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-700">{{ $t->booking->booking_code }}</div>
                            <div class="text-xs text-gray-500">{{ $t->booking->customer_name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $color = match($t->status) {
                                    'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    'pending' => 'bg-amber-50 text-amber-700 border-amber-100',
                                    'cancelled' => 'bg-rose-50 text-rose-700 border-rose-100',
                                    default => 'bg-gray-50 text-gray-600 border-gray-100'
                                };
                            @endphp
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full border {{ $color }} uppercase tracking-wider">
                                {{ $t->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <x-filament::button size="sm" color="gray" :href="App\Filament\Resources\BookingResource::getUrl('view', ['record' => $t->booking_id])" tag="a" icon="heroicon-m-eye">
                                Detail
                            </x-filament::button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">
                            Belum ada peserta di kategori ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>

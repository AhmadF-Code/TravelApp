<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Produk Trip Terlaris (Stable Mode)</x-slot>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Paket Trip</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Terjual</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($this->trips as $t)
                    <tr class="hover:bg-gray-50/10 transition-colors">
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ $t->title }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="text-sm font-black text-amber-600">{{ $t->filtered_bookings_count }} Unit</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="px-4 py-8 text-center text-gray-400 text-xs italic">Data tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

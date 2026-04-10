<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Profit Branch Teratas (Stable Mode)</x-slot>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Nama Branch</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Total Income</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($this->branches as $b)
                    <tr class="hover:bg-gray-50/10 transition-colors">
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ $b->name }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="text-sm font-black text-emerald-600">Rp {{ number_format($b->total_income, 0, ',', '.') }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="px-4 py-8 text-center text-gray-400 text-xs italic">Data belum tersedia.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

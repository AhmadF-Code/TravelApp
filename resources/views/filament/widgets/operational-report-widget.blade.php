<x-filament-widgets::widget>
    <x-filament::section class="ring-gray-100/50 shadow-lg border-none bg-white">
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <div class="p-1.5 bg-amber-500 rounded-md text-white">
                    <x-heroicon-m-document-chart-bar class="w-5 h-5" />
                </div>
                <span class="text-xl font-black text-gray-950 tracking-tight">Executive Report Hub</span>
            </div>
        </x-slot>
        <x-slot name="description">
            <span class="text-xs text-gray-500">Akses pusat pelaporan strategis Travel Agent. Seluruh data diunduh dalam format Excel yang rapi.</span>
        </x-slot>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
            <!-- SALES CARD -->
            <div class="relative group p-5 bg-gray-50/50 hover:bg-emerald-50/50 rounded-2xl border border-gray-100 hover:border-emerald-200 transition-all duration-300">
                <div class="absolute top-4 right-4 text-emerald-300 group-hover:text-emerald-500 transition-colors">
                    <x-heroicon-o-banknotes class="w-8 h-8 opacity-40 group-hover:opacity-100" />
                </div>
                <div class="flex flex-col h-full justify-between">
                    <div>
                        <div class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-1">Sales Data</div>
                        <div class="text-sm font-bold text-gray-900 leading-tight">Laporan Penjualan Lunas</div>
                        <p class="text-[10px] text-gray-400 mt-2 line-clamp-2 italic">Rekap seluruh transaksi yang berhasil diverifikasi masuk kas.</p>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100/50">
                        {{ $this->downloadSalesReportAction->size('sm')->outlined()->color('emerald') }}
                    </div>
                </div>
            </div>

            <!-- CANCEL CARD -->
            <div class="relative group p-5 bg-gray-50/50 hover:bg-rose-50/50 rounded-2xl border border-gray-100 hover:border-rose-200 transition-all duration-300">
                <div class="absolute top-4 right-4 text-rose-300 group-hover:text-rose-500 transition-colors">
                    <x-heroicon-o-x-circle class="w-8 h-8 opacity-40 group-hover:opacity-100" />
                </div>
                <div class="flex flex-col h-full justify-between">
                    <div>
                        <div class="text-[10px] font-black text-rose-600 uppercase tracking-widest mb-1">Cancellation</div>
                        <div class="text-sm font-bold text-gray-900 leading-tight">Laporan Pembatalan</div>
                        <p class="text-[10px] text-gray-400 mt-2 line-clamp-2 italic">Data pembatalan untuk analisis loss profit & alasan operasional.</p>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100/50">
                        {{ $this->downloadCancelReportAction->size('sm')->outlined()->color('rose') }}
                    </div>
                </div>
            </div>

            <!-- REFUND CARD -->
            <div class="relative group p-5 bg-gray-50/50 hover:bg-amber-50/50 rounded-2xl border border-gray-100 hover:border-amber-200 transition-all duration-300">
                <div class="absolute top-4 right-4 text-amber-300 group-hover:text-amber-500 transition-colors">
                    <x-heroicon-o-arrow-path-rounded-square class="w-8 h-8 opacity-40 group-hover:opacity-100" />
                </div>
                <div class="flex flex-col h-full justify-between">
                    <div>
                        <div class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-1">Financial Refund</div>
                        <div class="text-sm font-bold text-gray-900 leading-tight">Laporan Pengeluaran Refund</div>
                        <p class="text-[10px] text-gray-400 mt-2 line-clamp-2 italic">Audit dana keluar yang telah dikreditkan kembali ke pelanggan.</p>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100/50">
                        {{ $this->downloadRefundReportAction->size('sm')->outlined()->color('amber') }}
                    </div>
                </div>
            </div>

            <!-- MANIFEST CARD -->
            <div class="relative group p-5 bg-gray-50/50 hover:bg-sky-50/50 rounded-2xl border border-gray-100 hover:border-sky-200 transition-all duration-300">
                <div class="absolute top-4 right-4 text-sky-300 group-hover:text-sky-500 transition-colors">
                    <x-heroicon-o-user-group class="w-8 h-8 opacity-40 group-hover:opacity-100" />
                </div>
                <div class="flex flex-col h-full justify-between">
                    <div>
                        <div class="text-[10px] font-black text-sky-600 uppercase tracking-widest mb-1">Operation</div>
                        <div class="text-sm font-bold text-gray-900 leading-tight">Passenger Manifest</div>
                        <p class="text-[10px] text-gray-400 mt-2 line-clamp-2 italic">Daftar manifest harian seluruh peserta untuk kebutuhan trip organizer.</p>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100/50">
                        {{ $this->downloadManifestAction->size('sm')->outlined()->color('info') }}
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

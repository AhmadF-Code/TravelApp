<x-filament-widgets::widget>
    <div class="relative overflow-hidden rounded-3xl bg-gray-900 border border-gray-800 shadow-2xl p-8 mb-6">
        <!-- BACKGROUND DECORATION -->
        <div class="absolute -top-10 -right-10 w-64 h-64 bg-amber-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-10 -left-10 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="flex-1 text-center md:text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-amber-500 text-[10px] font-black uppercase tracking-widest mb-4">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                    </span>
                    Live Dashboard System
                </div>
                <h1 class="text-3xl md:text-5xl font-black text-white mb-2 leading-tight">
                    Welcome back, <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-200 to-amber-500">{{ auth()->user()->name }}</span>
                </h1>
                <p class="text-gray-400 text-sm md:text-lg max-w-2xl font-medium italic">
                    "Helping more people explore the world, one booking at a time."
                </p>
                
                <div class="flex items-center gap-6 mt-8 flex-wrap justify-center md:justify-start">
                    <div class="flex flex-col">
                        <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">Active Schedules</span>
                        <span class="text-2xl font-black text-white leading-none mt-1">{{ $this->summary['active_trip'] }} <span class="text-[10px] text-amber-500">Trip</span></span>
                    </div>
                    <div class="w-px h-8 bg-white/10 hidden sm:block"></div>
                    <div class="flex flex-col">
                        <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">Confirmed pax</span>
                        <span class="text-2xl font-black text-white leading-none mt-1">{{ $this->summary['upcoming_travelers'] }} <span class="text-[10px] text-blue-500">Pax</span></span>
                    </div>
                    <div class="w-px h-8 bg-white/10 hidden sm:block"></div>
                    <div class="flex flex-col">
                        <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">Needs Followup</span>
                        <span class="text-2xl font-black text-rose-500 leading-none mt-1">{{ $this->summary['pending_followup'] }} <span class="text-[10px] uppercase">Task</span></span>
                    </div>
                </div>
            </div>
            
            <div class="hidden lg:flex items-center justify-center p-4 bg-white/5 border border-white/10 rounded-2xl backdrop-blur-md">
                 <div class="text-center px-4">
                    <div class="text-[10px] text-gray-500 font-bold uppercase">Today</div>
                    <div class="text-4xl font-black text-white leading-tight uppercase">{{ now()->format('d M') }}</div>
                    <div class="text-xs text-amber-500 font-bold uppercase tracking-widest">{{ now()->format('Y') }}</div>
                 </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>

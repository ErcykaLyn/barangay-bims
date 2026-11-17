@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 space-y-8">
    <!-- Header (emerald gradient, same as other resident pages) -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-800 shadow-xl">
        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-2xl -mr-16 -mt-16"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/10 rounded-full blur-2xl -ml-16 -mb-16"></div>
        <div class="relative flex items-center gap-5 p-6 md:p-8">
            <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-white shadow-lg">
                <i class="fas fa-boxes text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight">Barangay Equipment</h1>
                <p class="text-white/80 text-xs font-mono uppercase tracking-wider mt-1">Bactad East – Available Assets for Residents</p>
            </div>
        </div>
    </div>

    <!-- Equipment Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($equipments as $item)
        <div class="group bg-white rounded-2xl shadow-md border border-slate-100 hover:shadow-xl transition-all duration-300 overflow-hidden hover:-translate-y-1">
            <!-- Picture header with category icon (colored gradient) -->
            <div class="h-32 bg-gradient-to-br 
                @if($item->category == 'chair') from-amber-50 to-amber-100
                @elseif($item->category == 'tent') from-sky-50 to-sky-100
                @elseif($item->category == 'medicine') from-emerald-50 to-emerald-100
                @elseif($item->category == 'sports') from-orange-50 to-orange-100
                @elseif($item->category == 'electronics') from-indigo-50 to-indigo-100
                @else from-gray-50 to-gray-100
                @endif
                flex items-center justify-center relative">
                <div class="text-5xl drop-shadow-lg transform group-hover:scale-110 transition-transform duration-300">
                    @if($item->category == 'chair')
                        <i class="fas fa-couch text-amber-700"></i>
                    @elseif($item->category == 'tent')
                        <i class="fas fa-umbrella-beach text-sky-700"></i>
                    @elseif($item->category == 'medicine')
                        <i class="fas fa-syringe text-emerald-700"></i>
                    @elseif($item->category == 'sports')
                        <i class="fas fa-futbol text-orange-700"></i>
                    @elseif($item->category == 'electronics')
                        <i class="fas fa-microphone-alt text-indigo-700"></i>
                    @else
                        <i class="fas fa-tools text-gray-600"></i>
                    @endif
                </div>
                <div class="absolute top-3 right-3">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase shadow-sm
                        {{ $item->status == 'available' ? 'bg-green-500 text-white' : ($item->status == 'damaged' ? 'bg-orange-500 text-white' : 'bg-red-500 text-white') }}">
                        {{ $item->status }}
                    </span>
                </div>
            </div>

            <!-- Card content -->
            <div class="p-5">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-bold text-gray-800 text-md uppercase">{{ $item->item_name }}</h3>
                        <p class="text-[10px] text-gray-400 font-mono mt-0.5">#EQ-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <div class="bg-gray-100 rounded-full px-2.5 py-1 text-xs font-bold text-gray-600">
                        {{ $item->quantity }} pcs
                    </div>
                </div>

                <!-- Borrower & Due Date (read‑only, emerald accents) -->
                <div class="mt-3 space-y-1 border-t border-gray-100 pt-3">
                    <div class="text-[11px] text-gray-500 flex items-center justify-between">
                        <span class="flex items-center gap-1"><i class="fas fa-user-check text-emerald-500 text-xs"></i> Borrower:</span>
                        @if($item->borrower)
                            <span class="font-semibold text-gray-700">{{ $item->borrower->first_name }} {{ $item->borrower->last_name }}</span>
                        @else
                            <span class="italic text-gray-400">No borrower</span>
                        @endif
                    </div>
                    
                    @if($item->status == 'borrowed' && $item->due_date)
                        @php
                            $remaining = $item->remaining_days;
                            $dueClass = $remaining === 0 ? 'bg-rose-100 text-rose-700' : ($remaining <= 2 ? 'bg-orange-100 text-orange-700' : 'bg-emerald-100 text-emerald-700');
                        @endphp
                        <div class="text-[11px] flex items-center justify-between">
                            <span class="flex items-center gap-1"><i class="fas fa-calendar-alt text-emerald-500 text-xs"></i> Due date:</span>
                            <div class="{{ $dueClass }} px-2 py-0.5 rounded-full text-[10px] font-bold">
                                {{ \Carbon\Carbon::parse($item->due_date)->format('M d, Y') }}
                                @if($remaining === 0)
                                    <span class="ml-1">⚠️ OVERDUE</span>
                                @elseif($remaining <= 2)
                                    <span class="ml-1">⚠️ {{ $remaining }} day(s) left</span>
                                @else
                                    <span>({{ $remaining }} days left)</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Status message button (read‑only, emerald style) -->
                <div class="mt-4 pt-2">
                    @if($item->status == 'available')
                        <div class="w-full py-2 rounded-xl bg-emerald-50 text-emerald-600 text-[10px] font-bold uppercase tracking-wider flex items-center justify-center gap-1 cursor-default">
                            <i class="fas fa-check-circle"></i> Available for request
                        </div>
                    @else
                        <div class="w-full py-2 rounded-xl bg-amber-50 text-amber-600 text-[10px] font-bold uppercase tracking-wider flex items-center justify-center gap-1 cursor-default">
                            <i class="fas fa-clock"></i> Currently {{ $item->status }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-16 bg-white rounded-2xl border-2 border-dashed border-slate-200">
            <i class="fas fa-box-open text-5xl text-slate-300 mb-3"></i>
            <p class="text-slate-500 font-semibold">No equipment records available.</p>
            <p class="text-slate-400 text-sm mt-1">Please check back later for updates.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
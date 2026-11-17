@extends('layouts.app')

@section('content')
@include('partials.resident-ui')

<div class="max-w-4xl mx-auto px-4 py-6 space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <a href="{{ route('resident.equipment.index') }}"
               class="text-xs font-bold text-emerald-600 hover:text-emerald-700 inline-flex items-center gap-1.5 mb-2">
                <i class="fas fa-arrow-left"></i> Back to Requests
            </a>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">My Requests</h1>
            <p class="text-xs text-slate-500 mt-1">Status ng lahat ng equipment at medicine requests mo</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 bg-slate-100 text-slate-700 rounded-xl text-[10px] font-black uppercase tracking-wider">
                {{ $requests->count() }} Total
            </span>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="flex items-center gap-3 px-4 py-3 bg-emerald-50 border-l-4 border-emerald-500 rounded-lg shadow-sm">
            <i class="fas fa-check-circle text-emerald-600"></i>
            <span class="text-sm font-semibold text-emerald-800 flex-1">{{ session('success') }}</span>
            <button @click="show = false" class="text-emerald-400 hover:text-emerald-600">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    @endif
    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="flex items-center gap-3 px-4 py-3 bg-rose-50 border-l-4 border-rose-500 rounded-lg shadow-sm">
            <i class="fas fa-exclamation-circle text-rose-600"></i>
            <span class="text-sm font-semibold text-rose-800 flex-1">{{ session('error') }}</span>
            <button @click="show = false" class="text-rose-400 hover:text-rose-600">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    @endif

    <!-- Requests List -->
    <div class="space-y-3">
        @forelse($requests as $req)
        @php
            $statusColor = $req->status_color;
            $statusLabel = $req->status_label;
            $isMedicine = $req->is_medicine ?? false;
        @endphp
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow">

            <!-- Status Banner -->
            @if($req->status === 'approved')
                <div class="bg-gradient-to-r {{ $isMedicine ? 'from-purple-500 to-purple-600' : 'from-blue-500 to-blue-600' }} px-4 py-2.5 flex items-center gap-2.5">
                    <div class="h-6 w-6 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0">
                        <i class="fas {{ $isMedicine ? 'fa-pills' : 'fa-box-open' }} text-white text-[10px]"></i>
                    </div>
                    <span class="text-[11px] font-black text-white uppercase tracking-wider">
                        @if($isMedicine)
                            Your medicine is ready for pickup at the Barangay Hall
                        @else
                            Please pick up this item at the Barangay Hall
                        @endif
                    </span>
                </div>
            @endif

            @if($req->status === 'dispensed')
                <div class="bg-gradient-to-r from-purple-100 to-purple-50 border-b border-purple-200 px-4 py-2.5 flex items-center gap-2.5">
                    <div class="h-6 w-6 rounded-lg bg-purple-200 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check-circle text-purple-600 text-[10px]"></i>
                    </div>
                    <span class="text-[11px] font-black text-purple-700 uppercase tracking-wider">
                        Medicine Dispensed — Thank you!
                    </span>
                </div>
            @endif

            @if($req->status === 'returned')
                <div class="bg-gradient-to-r from-emerald-100 to-emerald-50 border-b border-emerald-200 px-4 py-2.5 flex items-center gap-2.5">
                    <div class="h-6 w-6 rounded-lg bg-emerald-200 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check-double text-emerald-600 text-[10px]"></i>
                    </div>
                    <span class="text-[11px] font-black text-emerald-700 uppercase tracking-wider">
                        Item Returned — Transaction Complete
                    </span>
                </div>
            @endif

            <div class="p-4 flex items-start gap-4">
                <!-- Equipment photo -->
                <div class="h-16 w-16 rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center overflow-hidden flex-shrink-0 shadow-sm">
                    @if($req->equipment->photo_url)
                        <img src="{{ $req->equipment->photo_url }}" class="h-full w-full object-cover">
                    @else
                        <i class="fas fa-image text-xl text-slate-300"></i>
                    @endif
                </div>

                <!-- Info -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="text-sm font-black text-slate-800 uppercase truncate">
                                    {{ $req->equipment->item_name }}
                                </h3>
                                @if($isMedicine)
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-purple-100 text-purple-700 rounded text-[8px] font-black uppercase border border-purple-200">
                                        <i class="fas fa-pills text-[7px]"></i> Medicine
                                    </span>
                                @endif
                            </div>
                            <p class="text-[10px] text-slate-400 font-mono mt-0.5">
                                Request #{{ str_pad($req->id, 4, '0', STR_PAD_LEFT) }}
                            </p>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider border whitespace-nowrap flex-shrink-0 {{ $statusColor }}">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <!-- Details Grid -->
                    <div class="grid grid-cols-2 gap-x-4 gap-y-1.5 text-[11px] bg-slate-50 rounded-lg p-2.5 border border-slate-100">
                        <div class="flex items-center gap-1.5">
                            <i class="fas fa-cubes text-slate-400 text-[10px] w-3.5"></i>
                            <span class="text-slate-500">Qty:</span>
                            <span class="font-bold text-slate-700">{{ $req->quantity }} pcs</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i class="fas fa-calendar-plus text-slate-400 text-[10px] w-3.5"></i>
                            <span class="text-slate-500">Submitted:</span>
                            <span class="font-bold text-slate-700">{{ $req->created_at->format('M d, Y') }}</span>
                        </div>
                        @if($req->needed_date)
                        <div class="flex items-center gap-1.5">
                            <i class="fas fa-calendar-check text-slate-400 text-[10px] w-3.5"></i>
                            <span class="text-slate-500">Needed:</span>
                            <span class="font-bold text-slate-700">{{ $req->needed_date->format('M d, Y') }}</span>
                        </div>
                        @endif
                        @if($req->due_date && !$isMedicine)
                        <div class="flex items-center gap-1.5">
                            <i class="fas fa-calendar-times text-rose-400 text-[10px] w-3.5"></i>
                            <span class="text-slate-500">Due:</span>
                            <span class="font-bold text-rose-600">{{ $req->due_date->format('M d, Y') }}</span>
                        </div>
                        @endif
                    </div>

                    @if($req->reason)
                    <p class="mt-2.5 text-[11px] text-slate-600 italic leading-relaxed">
                        <i class="fas fa-quote-left text-slate-300 text-[9px] mr-1"></i>
                        {{ $req->reason }}
                    </p>
                    @endif

                    @if($req->status === 'rejected' && $req->rejection_reason)
                    <div class="mt-3 p-3 bg-rose-50 border-l-2 border-rose-500 rounded-r-lg">
                        <p class="text-[10px] font-black text-rose-700 uppercase tracking-wider mb-1 flex items-center gap-1">
                            <i class="fas fa-times-circle"></i> Rejection Reason
                        </p>
                        <p class="text-[11px] text-rose-700 leading-relaxed">{{ $req->rejection_reason }}</p>
                    </div>
                    @endif

                    <!-- Timestamps -->
                    <div class="mt-2.5 space-y-1">
                        @if($req->approved_at)
                        <p class="text-[10px] text-slate-400 flex items-center gap-1.5">
                            <i class="fas fa-check-circle text-emerald-500"></i>
                            Approved: {{ $req->approved_at->format('M d, Y h:i A') }}
                        </p>
                        @endif

                        @if($req->picked_up_at)
                        <p class="text-[10px] text-slate-400 flex items-center gap-1.5">
                            <i class="fas fa-hand-holding text-indigo-500"></i>
                            Picked Up: {{ $req->picked_up_at->format('M d, Y h:i A') }}
                        </p>
                        @endif

                        @if($req->dispensed_at)
                        <p class="text-[10px] text-slate-400 flex items-center gap-1.5">
                            <i class="fas fa-pills text-purple-500"></i>
                            Dispensed: {{ $req->dispensed_at->format('M d, Y h:i A') }}
                        </p>
                        @endif

                        @if($req->returned_at)
                        <p class="text-[10px] text-slate-400 flex items-center gap-1.5">
                            <i class="fas fa-undo text-emerald-500"></i>
                            Returned: {{ $req->returned_at->format('M d, Y h:i A') }}
                        </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-16 bg-white rounded-xl border-2 border-dashed border-slate-200">
            <div class="h-16 w-16 bg-gradient-to-br from-slate-100 to-slate-200 rounded-full flex items-center justify-center mx-auto mb-3 shadow-inner">
                <i class="fas fa-inbox text-2xl text-slate-400"></i>
            </div>
            <p class="text-sm font-bold text-slate-600">No requests yet</p>
            <p class="text-xs text-slate-400 mt-1 mb-4">Start by browsing available equipment or medicine.</p>
            <a href="{{ route('resident.equipment.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-emerald-600 text-white rounded-xl text-xs font-black uppercase tracking-wider hover:bg-emerald-700 transition-colors shadow-sm shadow-emerald-500/20">
                <i class="fas fa-box-open"></i> Browse Items
            </a>
        </div>
        @endforelse
    </div>
</div>
@endsection
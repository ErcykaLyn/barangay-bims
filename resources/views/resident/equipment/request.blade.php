@extends('layouts.app')

@section('content')
@php
    $isConsumable = in_array(strtolower($equipment->category ?? ''), ['medicine', 'medicines', 'supplies', 'supply']);
    $actionTitle = $isConsumable ? 'Request to Claim' : 'Request to Borrow';
    $actionIcon = $isConsumable ? 'fa-hand-holding-medical' : 'fa-hand-holding';
    $reasonLabel = $isConsumable ? 'Reason for Claiming' : 'Reason for Borrowing';
    $quantityLabel = $isConsumable ? 'Quantity to Claim' : 'Quantity to Borrow';
    $dateLabel = $isConsumable ? 'Date Needed' : 'Date Needed';
    $placeholder = $isConsumable 
        ? 'Halimbawa: Kailangan ko ng gamot para sa lagnat...' 
        : 'Halimbawa: Kailangan para sa birthday party ng anak ko...';
    $noteText = $isConsumable
        ? 'Kapag na-approve ng admin ang request mo, kailangan mong kunin ang item sa Barangay Hall. Hindi na ito kailangang ibalik.'
        : 'Kapag na-approve ng admin ang request mo, kailangan mong kunin ang item sa Barangay Hall at ibalik pagkatapos gamitin.';
@endphp

<div class="max-w-2xl mx-auto px-4 py-8 space-y-6">

    {{-- Back Button & Header --}}
    <div class="flex items-start gap-4">
        <a href="{{ route('resident.equipment.index') }}"
           class="h-10 w-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition flex-shrink-0">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">{{ $actionTitle }}</h1>
            <p class="text-sm text-slate-500 mt-1">Fill out this form. The admin will review your request.</p>
        </div>
    </div>

    {{-- Item Card --}}
    <div class="bg-gradient-to-br from-emerald-600 to-emerald-800 rounded-2xl shadow-lg overflow-hidden relative">
        <div class="absolute -top-12 -right-12 w-40 h-40 bg-white/10 rounded-full"></div>
        <div class="absolute -bottom-16 -left-8 w-32 h-32 bg-white/5 rounded-full"></div>
        <div class="relative p-5 flex items-center gap-4">
            <div class="h-20 w-20 rounded-xl bg-white/15 backdrop-blur border border-white/20 flex items-center justify-center overflow-hidden flex-shrink-0 shadow-inner">
                @if($equipment->photo_url)
                    <img src="{{ $equipment->photo_url }}" class="h-full w-full object-cover">
                @else
                    <i class="fas fa-image text-2xl text-white/70"></i>
                @endif
            </div>
            <div class="min-w-0 flex-1 text-white">
                <div class="flex items-center gap-2 mb-1">
                    <h3 class="text-base font-black uppercase tracking-tight">{{ $equipment->item_name }}</h3>
                    @if($isConsumable)
                        <span class="px-2 py-0.5 bg-purple-500 text-white text-[9px] font-black uppercase rounded-md">
                            <i class="fas fa-pills text-[7px] mr-0.5"></i> Med
                        </span>
                    @endif
                </div>
                <p class="text-[11px] text-emerald-100/80 font-mono mt-0.5">#EQ-{{ str_pad($equipment->id, 4, '0', STR_PAD_LEFT) }} • {{ $equipment->category ?? 'General' }}</p>
                <div class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 bg-white/20 backdrop-blur rounded-lg border border-white/30">
                    <i class="fas fa-cubes text-[10px]"></i>
                    <span class="text-[11px] font-black">{{ $equipment->available_stock }} pcs available</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <form action="{{ route('resident.equipment.request.submit', $equipment->id) }}" method="POST"
          class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-5">
        @csrf

        {{-- Quantity --}}
        <div>
            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-2">
                {{ $quantityLabel }} <span class="text-rose-500">*</span>
            </label>
            
            <div class="flex items-center gap-4">
                <div class="flex items-center border border-slate-300 rounded-xl overflow-hidden bg-white shadow-sm">
                    <button type="button" onclick="decrementQty()" 
                            class="h-12 w-12 flex items-center justify-center text-slate-600 hover:bg-slate-100 active:bg-slate-200 transition-colors border-r border-slate-200">
                        <i class="fas fa-minus text-xs"></i>
                    </button>
                    
                    <input type="number" 
                           name="quantity" 
                           id="quantityInput"
                           min="1" 
                           max="{{ $equipment->available_stock }}" 
                           value="1" 
                           required
                           class="w-20 h-12 text-center text-xl font-black text-slate-800 border-0 focus:ring-0 focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                    
                    <button type="button" onclick="incrementQty()" 
                            class="h-12 w-12 flex items-center justify-center text-slate-600 hover:bg-slate-100 active:bg-slate-200 transition-colors border-l border-slate-200">
                        <i class="fas fa-plus text-xs"></i>
                    </button>
                </div>

                <div class="flex-1">
                    <div class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-50 border border-emerald-200 rounded-xl">
                        <i class="fas fa-cubes text-emerald-600 text-[11px]"></i>
                        <span class="text-xs font-black text-emerald-700">
                            {{ $equipment->available_stock }} available
                        </span>
                    </div>
                </div>
            </div>
            
            <p class="text-[10px] text-slate-400 mt-2">
                <i class="fas fa-info-circle text-[9px]"></i>
                Maximum: <strong>{{ $equipment->available_stock }} pcs</strong>
            </p>
        </div>

        {{-- Date Needed --}}
        <div>
            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-2">
                {{ $dateLabel }}
            </label>
            <div class="relative">
                <i class="fas fa-calendar absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                <input type="date" name="needed_date" min="{{ date('Y-m-d') }}"
                       class="w-full pl-10 pr-3 py-2.5 border border-slate-300 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 outline-none transition">
            </div>
            <p class="text-[10px] text-slate-400 mt-1.5">Kailan mo kailangan ang item?</p>
        </div>

        {{-- Reason --}}
        <div>
            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-2">
                {{ $reasonLabel }} <span class="text-rose-500">*</span>
            </label>
            <textarea name="reason" rows="4" required maxlength="500"
                      placeholder="{{ $placeholder }}"
                      class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 outline-none transition resize-none"></textarea>
            <p class="text-[10px] text-slate-400 mt-1.5">Max 500 characters</p>
        </div>

        {{-- Info Note --}}
        <div class="flex items-start gap-2.5 p-3.5 {{ $isConsumable ? 'bg-purple-50 border-purple-200' : 'bg-blue-50 border-blue-200' }} border rounded-xl">
            <i class="fas {{ $isConsumable ? 'fa-info-circle text-purple-600' : 'fa-info-circle text-blue-600' }} mt-0.5 text-sm"></i>
            <div class="text-[11px] {{ $isConsumable ? 'text-purple-800' : 'text-blue-800' }} leading-relaxed">
                <strong>Paalala:</strong> {{ $noteText }}
            </div>
        </div>

        {{-- Actions --}}
        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row justify-end gap-2">
            <a href="{{ route('resident.equipment.index') }}"
               class="px-5 py-2.5 bg-white border border-slate-300 text-slate-600 text-xs font-bold rounded-xl hover:bg-slate-50 transition-colors text-center uppercase tracking-wider">
                Cancel
            </a>
            <button type="submit"
                    class="px-6 py-2.5 bg-gradient-to-br from-emerald-500 to-emerald-700 hover:from-emerald-600 hover:to-emerald-800 text-white text-xs font-black rounded-xl shadow-md shadow-emerald-500/30 transition-all flex items-center justify-center gap-2 uppercase tracking-wider">
                <i class="fas {{ $actionIcon }} text-[10px]"></i>
                Submit {{ $isConsumable ? 'Claim' : 'Request' }}
            </button>
        </div>
    </form>
</div>

<script>
    const maxQty = {{ $equipment->available_stock }};
    const qtyInput = document.getElementById('quantityInput');

    function incrementQty() {
        let current = parseInt(qtyInput.value) || 1;
        if (current < maxQty) {
            qtyInput.value = current + 1;
        }
    }

    function decrementQty() {
        let current = parseInt(qtyInput.value) || 1;
        if (current > 1) {
            qtyInput.value = current - 1;
        }
    }

    qtyInput.addEventListener('change', function() {
        let val = parseInt(this.value) || 1;
        if (val < 1) val = 1;
        if (val > maxQty) val = maxQty;
        this.value = val;
    });
</script>
@endsection
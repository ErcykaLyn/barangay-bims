@extends('layouts.admin')

@section('content')
@php
    $field = 'w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-800 placeholder:text-slate-400 focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 outline-none transition';

    $statusOptions = [
        'pending'   => 'Pending',
        'approved'  => 'Approved',
        'picked_up' => 'Picked up',
        'dispensed' => 'Dispensed',
        'returned'  => 'Returned',
        'rejected'  => 'Rejected',
        'partial_return' => 'Partial Return',
    ];

    $barConfig = [
        'pending'   => 'border-l-amber-400',
        'approved'  => 'border-l-sky-500',
        'picked_up' => 'border-l-indigo-500',
        'dispensed' => 'border-l-purple-500',
        'returned'  => 'border-l-emerald-500',
        'rejected'  => 'border-l-rose-500',
        'partial_return' => 'border-l-amber-500',
    ];

    $stats = [
        ['label' => 'Pending',   'icon' => 'fa-clock',        'value' => $pendingCount,  'bg' => 'from-amber-400 to-orange-600',   'shadow' => 'shadow-amber-500/25',  'span' => 'col-span-2 sm:col-span-1'],
        ['label' => 'Approved',  'icon' => 'fa-check',        'value' => $approvedCount, 'bg' => 'from-sky-400 to-blue-700',       'shadow' => 'shadow-sky-500/25',    'span' => 'col-span-2 sm:col-span-1'],
        ['label' => 'Picked up', 'icon' => 'fa-hand-holding', 'value' => $pickedUpCount, 'bg' => 'from-indigo-500 to-indigo-800',  'shadow' => 'shadow-indigo-500/25', 'span' => 'col-span-2 sm:col-span-1'],
        ['label' => 'Dispensed', 'icon' => 'fa-pills',        'value' => $dispensedCount,'bg' => 'from-violet-500 to-purple-800',  'shadow' => 'shadow-violet-500/25', 'span' => 'col-span-3 sm:col-span-1'],
        ['label' => 'Returned',  'icon' => 'fa-undo',         'value' => $returnedCount, 'bg' => 'from-emerald-500 to-emerald-800','shadow' => 'shadow-emerald-500/25','span' => 'col-span-3 sm:col-span-1'],
    ];

    $cols = 'lg:grid-cols-[1.3fr_1.4fr_56px_1.3fr_1fr_250px]';
@endphp

<div class="bims-page min-h-screen bg-gradient-to-br from-slate-50 via-emerald-50/40 to-teal-50/30">

    <div class="relative overflow-hidden bg-gradient-to-br from-emerald-700 via-emerald-800 to-teal-900">
        <div class="absolute -top-16 -right-10 w-56 h-56 bg-emerald-300/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 left-1/4 w-56 h-56 bg-teal-300/15 rounded-full blur-3xl"></div>

        <div class="relative px-3 sm:px-6 lg:px-8 py-3 sm:py-4">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                    <a href="{{ route('admin.equipment.index') }}" aria-label="Back to inventory"
                       class="h-9 w-9 sm:h-10 sm:w-10 flex items-center justify-center rounded-xl bg-white/15 hover:bg-white/25 border border-white/20 text-white transition flex-shrink-0 focus-visible:outline focus-visible:outline-2 focus-visible:outline-white">
                        <i class="fas fa-arrow-left text-sm"></i>
                    </a>
                    <div class="min-w-0">
                        <nav class="flex items-center gap-1.5 text-xs text-emerald-200/80">
                            <a href="{{ route('admin.equipment.index') }}" class="hover:text-white transition">Inventory</a>
                            <i class="fas fa-chevron-right text-[8px]"></i>
                            <span class="text-white font-medium">Borrow requests</span>
                        </nav>
                        <h1 class="bims-display text-lg sm:text-2xl text-white truncate">Equipment borrow requests</h1>
                    </div>
                </div>

                <div class="flex-shrink-0">
                    @if($pendingCount > 0)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-amber-400/25 text-amber-50 rounded-lg text-xs font-semibold border border-amber-300/40">
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-300 bims-pulse"></span> {{ $pendingCount }} pending
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-emerald-400/20 text-emerald-50 rounded-lg text-xs font-semibold border border-emerald-300/30">
                            <i class="fas fa-check-circle text-[11px]"></i> All clear
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(session('success') || session('error') || session('warning'))
        <div class="px-3 sm:px-6 lg:px-8 pt-3 space-y-2">
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     x-transition:leave="transition ease-in duration-300"
                     class="flex items-center gap-3 px-3.5 py-2.5 bg-emerald-50 border border-emerald-200 border-l-4 border-l-emerald-500 rounded-xl">
                    <i class="fas fa-check-circle text-emerald-600"></i>
                    <span class="text-sm font-medium text-emerald-800 flex-1">{{ session('success') }}</span>
                    <button @click="show = false" class="text-emerald-400 hover:text-emerald-600" aria-label="Dismiss"><i class="fas fa-times text-xs"></i></button>
                </div>
            @endif
            @if(session('warning'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
                     x-transition:leave="transition ease-in duration-300"
                     class="flex items-center gap-3 px-3.5 py-2.5 bg-amber-50 border border-amber-200 border-l-4 border-l-amber-500 rounded-xl">
                    <i class="fas fa-exclamation-triangle text-amber-600"></i>
                    <span class="text-sm font-medium text-amber-800 flex-1">{{ session('warning') }}</span>
                    <button @click="show = false" class="text-amber-400 hover:text-amber-600" aria-label="Dismiss"><i class="fas fa-times text-xs"></i></button>
                </div>
            @endif
            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                     x-transition:leave="transition ease-in duration-300"
                     class="flex items-center gap-3 px-3.5 py-2.5 bg-rose-50 border border-rose-200 border-l-4 border-l-rose-500 rounded-xl">
                    <i class="fas fa-exclamation-circle text-rose-600"></i>
                    <span class="text-sm font-medium text-rose-800 flex-1">{{ session('error') }}</span>
                    <button @click="show = false" class="text-rose-400 hover:text-rose-600" aria-label="Dismiss"><i class="fas fa-times text-xs"></i></button>
                </div>
            @endif
        </div>
    @endif

    <div class="px-3 sm:px-6 lg:px-8 py-3 sm:py-5 space-y-3 sm:space-y-5">

        <div class="bims-stats grid grid-cols-6 sm:grid-cols-5 gap-2 sm:gap-3">
            @foreach($stats as $s)
                <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-gradient-to-br {{ $s['bg'] }} px-3 py-2.5 sm:px-4 sm:py-3.5 text-white shadow-lg {{ $s['shadow'] }} {{ $s['span'] }} transition-transform duration-300 hover:-translate-y-1">
                    <div class="absolute -top-8 -right-8 w-24 h-24 rounded-full bg-white/10"></div>
                    <i class="fas {{ $s['icon'] }} absolute -bottom-2 -right-1 text-5xl text-white/15 group-hover:scale-110 transition-transform duration-500" aria-hidden="true"></i>
                    <div class="relative">
                        <p class="text-[11px] sm:text-xs font-semibold text-white/85 leading-tight">{{ $s['label'] }}</p>
                        <p class="bims-display bims-figure text-2xl sm:text-3xl mt-0.5">{{ $s['value'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm p-3 sm:p-4">
            <form method="GET" class="grid grid-cols-12 gap-2 sm:gap-3 items-center">
                <div class="col-span-12 sm:col-span-6 relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by resident or item"
                           class="{{ $field }} pl-8" aria-label="Search">
                </div>
                <div class="col-span-12 sm:col-span-3">
                    <select name="status" class="{{ $field }} cursor-pointer" aria-label="Status">
                        <option value="">All status</option>
                        @foreach($statusOptions as $val => $text)
                            <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $text }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-12 sm:col-span-3 flex items-center gap-2">
                    <button type="submit"
                            class="flex-1 px-4 py-1.5 bg-gradient-to-br from-emerald-500 to-emerald-700 hover:from-emerald-600 hover:to-emerald-800 text-white text-sm font-semibold rounded-lg shadow-sm shadow-emerald-600/30 transition flex items-center justify-center gap-1.5">
                        <i class="fas fa-search text-[11px]"></i> Filter
                    </button>
                    <a href="{{ route('admin.equipment.requests') }}" title="Reset filters"
                       class="px-3.5 py-1.5 bg-white border border-slate-300 text-slate-600 text-sm font-semibold rounded-lg hover:bg-slate-50 transition flex items-center justify-center gap-1.5">
                        <i class="fas fa-redo text-[11px]"></i><span class="sm:hidden">Reset</span>
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-4 py-2.5 border-b border-slate-100 bg-gradient-to-r from-emerald-50 via-white to-white flex items-center gap-2.5">
                <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-emerald-400 to-emerald-700 flex items-center justify-center text-white shadow shadow-emerald-500/30">
                    <i class="fas fa-clipboard-list text-xs"></i>
                </div>
                <div>
                    <h2 class="bims-display text-base text-slate-800">Borrow requests</h2>
                    <p class="text-xs text-slate-500 leading-tight">{{ $requests->count() }} total</p>
                </div>
            </div>

            @if($requests->isEmpty())
                <div class="px-4 py-14 text-center">
                    <div class="h-14 w-14 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-inbox text-xl text-slate-300"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-slate-700">No requests found</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Borrow requests from residents will appear here.</p>
                </div>
            @else
                <div class="hidden lg:grid {{ $cols }} gap-3 pl-5 pr-4 py-2.5 bg-slate-50/70 border-b border-slate-200 text-xs font-semibold text-slate-500">
                    <div>Resident</div>
                    <div>Item</div>
                    <div class="text-center">Qty</div>
                    <div>Reason</div>
                    <div>Status</div>
                    <div class="text-center">Actions</div>
                </div>

                <div class="p-2.5 sm:p-3 space-y-2 lg:p-0 lg:space-y-0 lg:divide-y lg:divide-slate-100">
                    @foreach($requests as $req)
                        @php
                            $bar          = $barConfig[$req->status] ?? 'border-l-slate-300';
                            $residentName = trim(($req->resident->first_name ?? '') . ' ' . ($req->resident->last_name ?? ''));
                            $remaining    = max(0, ($req->quantity_borrowed ?? $req->quantity) - ($req->quantity_returned ?? 0));
                        @endphp
                        <div class="grid grid-cols-2 gap-x-2 gap-y-1.5 lg:gap-3 lg:items-center {{ $cols }}
                                    rounded-xl border border-slate-200 border-l-4 {{ $bar }} bg-white px-3 py-3 shadow-sm
                                    lg:rounded-none lg:border-0 lg:border-l-4 lg:shadow-none lg:px-4 lg:py-2.5 lg:hover:bg-emerald-50/40 transition-colors">

                            <div class="min-w-0 order-1 lg:order-none">
                                <p class="text-sm font-semibold text-slate-800 leading-tight truncate">{{ $req->resident->last_name ?? 'N/A' }}, {{ $req->resident->first_name ?? '' }}</p>
                                <p class="text-xs text-slate-400 truncate">{{ $req->resident->email ?? 'N/A' }}</p>
                            </div>

                            <div class="order-2 lg:order-none justify-self-end lg:justify-self-start text-right lg:text-left">
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-semibold border whitespace-nowrap {{ $req->status_color }}">{{ $req->status_label }}</span>
                                @if($req->due_date && !$req->is_medicine)
                                    <p class="text-[11px] text-slate-400 mt-0.5">Due: {{ $req->due_date->format('M d, Y') }}</p>
                                @endif
                                @if($req->status === 'partial_return')
                                    <p class="text-[11px] text-amber-600 font-semibold mt-0.5">
                                        <i class="fas fa-exclamation-triangle text-[9px]"></i>
                                        Kulang: {{ $req->shortage }} pcs
                                    </p>
                                @endif
                            </div>

                            <div class="col-span-2 lg:col-span-1 min-w-0 order-3 lg:order-none">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <p class="text-sm font-semibold text-slate-700 leading-tight">{{ $req->equipment->item_name ?? 'N/A' }}</p>
                                    <span class="lg:hidden inline-flex items-center px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-bold">×{{ $req->quantity }}</span>
                                    @if($req->is_medicine)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-purple-50 text-purple-700 rounded-full text-[10px] font-semibold ring-1 ring-inset ring-purple-200">
                                            <i class="fas fa-pills text-[8px]"></i> Medicine
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-400">{{ $req->created_at->diffForHumans() }}</p>
                            </div>

                            <div class="hidden lg:block text-center text-sm font-bold text-slate-800">{{ $req->quantity }}</div>

                            <div class="col-span-2 lg:col-span-1 min-w-0 order-4 lg:order-none">
                                <p class="text-xs text-slate-600 line-clamp-2 lg:truncate" title="{{ $req->reason }}">{{ $req->reason ?? '—' }}</p>
                            </div>

                            <div class="col-span-2 lg:col-span-1 order-5 lg:order-none mt-1 lg:mt-0 pt-2.5 lg:pt-0 border-t border-slate-100 lg:border-0">
                                <div class="flex items-center gap-2 lg:justify-center">
                                    @if($req->isPending())
                                        <button type="button" onclick="openApprove(this)"
                                                data-id="{{ $req->id }}"
                                                data-item="{{ $req->equipment->item_name ?? '' }}"
                                                data-qty="{{ $req->quantity }}"
                                                data-resident="{{ $residentName }}"
                                                data-medicine="{{ $req->is_medicine ? '1' : '0' }}"
                                                class="flex-1 lg:flex-none h-8 px-3 bg-gradient-to-br from-emerald-500 to-emerald-700 hover:from-emerald-600 hover:to-emerald-800 text-white text-xs font-semibold rounded-lg shadow-sm flex items-center justify-center gap-1.5 transition">
                                            <i class="fas fa-check text-[10px]"></i> Approve
                                        </button>
                                        <button type="button" onclick="openReject(this)"
                                                data-id="{{ $req->id }}"
                                                data-item="{{ $req->equipment->item_name ?? '' }}"
                                                class="flex-1 lg:flex-none h-8 px-3 bg-gradient-to-br from-rose-500 to-rose-700 hover:from-rose-600 hover:to-rose-800 text-white text-xs font-semibold rounded-lg shadow-sm flex items-center justify-center gap-1.5 transition">
                                            <i class="fas fa-times text-[10px]"></i> Reject
                                        </button>
                                    @elseif($req->isApproved())
                                        @if($req->is_medicine)
                                            <form action="{{ route('admin.equipment.requests.dispensed', $req->id) }}" method="POST" class="flex-1 lg:flex-none">
                                                @csrf
                                                <button class="w-full h-8 px-3 bg-gradient-to-br from-violet-500 to-purple-700 hover:from-violet-600 hover:to-purple-800 text-white text-xs font-semibold rounded-lg shadow-sm flex items-center justify-center gap-1.5 transition">
                                                    <i class="fas fa-pills text-[10px]"></i> Mark dispensed
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.equipment.requests.picked-up', $req->id) }}" method="POST" class="flex-1 lg:flex-none">
                                                @csrf
                                                <button class="w-full h-8 px-3 bg-gradient-to-br from-indigo-500 to-indigo-700 hover:from-indigo-600 hover:to-indigo-800 text-white text-xs font-semibold rounded-lg shadow-sm flex items-center justify-center gap-1.5 transition">
                                                    <i class="fas fa-hand-holding text-[10px]"></i> Mark picked up
                                                </button>
                                            </form>
                                        @endif
                                    @elseif($req->isPickedUp() || $req->isPartialReturn())
                                        <form action="{{ route('admin.equipment.requests.returned', $req->id) }}" method="POST" class="flex-1 lg:flex-none flex items-center gap-1.5">
                                            @csrf
                                            <div class="flex items-center border border-slate-300 rounded-lg overflow-hidden bg-white shadow-sm">
                                                <button type="button" onclick="this.nextElementSibling.stepDown()" class="h-8 w-7 flex items-center justify-center text-slate-500 hover:bg-slate-100 border-r border-slate-200 transition">
                                                    <i class="fas fa-minus text-[8px]"></i>
                                                </button>
                                                <input type="number" name="returned_quantity" value="{{ $remaining }}" min="1" max="{{ $remaining }}"
                                                       class="w-10 h-8 text-center text-xs font-bold text-slate-800 border-0 focus:ring-0 focus:outline-none p-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                                <button type="button" onclick="this.previousElementSibling.stepUp()" class="h-8 w-7 flex items-center justify-center text-slate-500 hover:bg-slate-100 border-l border-slate-200 transition">
                                                    <i class="fas fa-plus text-[8px]"></i>
                                                </button>
                                            </div>
                                            <button class="h-8 px-3 bg-gradient-to-br from-emerald-500 to-emerald-700 hover:from-emerald-600 hover:to-emerald-800 text-white text-xs font-semibold rounded-lg shadow-sm flex items-center justify-center gap-1.5 transition">
                                                <i class="fas fa-undo text-[10px]"></i> Return
                                            </button>
                                        </form>
                                    @elseif($req->isDispensed())
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-purple-50 text-purple-700 rounded-full text-xs font-semibold ring-1 ring-inset ring-purple-200">
                                            <i class="fas fa-check-circle text-[10px]"></i> Completed
                                        </span>
                                    @elseif($req->isReturned())
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-semibold ring-1 ring-inset ring-emerald-200">
                                            <i class="fas fa-check-circle text-[10px]"></i> Completed
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400 italic">No action</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<div id="approveModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="approveTitle">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
        <div class="px-4 py-3 bg-gradient-to-r from-emerald-600 to-emerald-800 flex justify-between items-center">
            <h3 id="approveTitle" class="bims-display text-base text-white flex items-center gap-2"><i class="fas fa-check-circle text-emerald-200"></i> Approve request</h3>
            <button type="button" onclick="closeModal('approveModal')" class="h-7 w-7 rounded-lg text-white/80 hover:text-white hover:bg-white/15 transition" aria-label="Close"><i class="fas fa-times"></i></button>
        </div>
        <form id="approveForm" method="POST" class="p-4 space-y-3">
            @csrf
            <div class="rounded-xl bg-slate-50 border border-slate-100 px-3 py-2.5 space-y-1 text-sm text-slate-600">
                <p class="flex justify-between gap-3"><span>Item</span> <strong id="approveItemName" class="text-slate-800 text-right"></strong></p>
                <p class="flex justify-between gap-3"><span>Resident</span> <strong id="approveResident" class="text-slate-800 text-right"></strong></p>
                <p class="flex justify-between gap-3"><span>Quantity</span> <span><strong id="approveQty" class="text-emerald-700"></strong> pcs</span></p>
            </div>

            <div id="dueDateGroup">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Due date <span class="text-rose-500">*</span></label>
                <input type="date" name="due_date" id="approveDueDate" min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="{{ $field }}">
            </div>

            <div id="medicineNotice" class="hidden items-start gap-2 p-3 bg-purple-50 border border-purple-200 rounded-xl">
                <i class="fas fa-info-circle text-purple-600 mt-0.5 text-xs"></i>
                <p class="text-xs text-purple-700 leading-relaxed">
                    <strong>Medicine:</strong> no due date needed. After approval, the resident claims the medicine and you mark it as <strong>Dispensed</strong> (no return).
                </p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Notes <span class="text-slate-400 font-normal">(optional)</span></label>
                <textarea name="notes" rows="2" class="{{ $field }}"></textarea>
            </div>

            <div class="pt-1 flex justify-end gap-2">
                <button type="button" onclick="closeModal('approveModal')" class="px-4 py-2 bg-white border border-slate-300 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-50 transition">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-gradient-to-br from-emerald-500 to-emerald-700 hover:from-emerald-600 hover:to-emerald-800 text-white text-sm font-semibold rounded-xl shadow-sm shadow-emerald-600/30 transition">Approve</button>
            </div>
        </form>
    </div>
</div>

<div id="rejectModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="rejectTitle">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
        <div class="px-4 py-3 bg-gradient-to-r from-rose-600 to-rose-800 flex justify-between items-center">
            <h3 id="rejectTitle" class="bims-display text-base text-white flex items-center gap-2"><i class="fas fa-ban text-rose-200"></i> Reject request</h3>
            <button type="button" onclick="closeModal('rejectModal')" class="h-7 w-7 rounded-lg text-white/80 hover:text-white hover:bg-white/15 transition" aria-label="Close"><i class="fas fa-times"></i></button>
        </div>
        <form id="rejectForm" method="POST" class="p-4 space-y-3">
            @csrf
            <div class="rounded-xl bg-slate-50 border border-slate-100 px-3 py-2.5 text-sm text-slate-600">
                <p class="flex justify-between gap-3"><span>Item</span> <strong id="rejectItemName" class="text-slate-800 text-right"></strong></p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Rejection reason <span class="text-rose-500">*</span></label>
                <textarea name="rejection_reason" rows="3" required maxlength="500"
                          placeholder="Ipaliwanag kung bakit ire-reject..."
                          class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-800 placeholder:text-slate-400 focus:ring-2 focus:ring-rose-500/25 focus:border-rose-500 outline-none transition"></textarea>
            </div>
            <div class="pt-1 flex justify-end gap-2">
                <button type="button" onclick="closeModal('rejectModal')" class="px-4 py-2 bg-white border border-slate-300 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-50 transition">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-gradient-to-br from-rose-500 to-rose-700 hover:from-rose-600 hover:to-rose-800 text-white text-sm font-semibold rounded-xl shadow-sm shadow-rose-600/30 transition">Reject</button>
            </div>
        </form>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,800&family=Figtree:wght@400;500;600;700&display=swap');

    .bims-page { font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif; }
    .bims-display {
        font-family: 'Bricolage Grotesque', 'Figtree', ui-sans-serif, system-ui, sans-serif;
        font-weight: 800;
        letter-spacing: -0.02em;
        line-height: 1.1;
    }
    .bims-figure { font-variant-numeric: tabular-nums; letter-spacing: -0.03em; }

    @keyframes bimsRise {
        from { opacity: 0; transform: translateY(14px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .bims-stats > div { animation: bimsRise .5s cubic-bezier(.22, 1, .36, 1) both; }
    .bims-stats > div:nth-child(2) { animation-delay: .06s; }
    .bims-stats > div:nth-child(3) { animation-delay: .12s; }
    .bims-stats > div:nth-child(4) { animation-delay: .18s; }
    .bims-stats > div:nth-child(5) { animation-delay: .24s; }

    .bims-pulse { animation: bimsPulse 2s ease-in-out infinite; }
    @keyframes bimsPulse { 50% { opacity: .4; } }

    @media (prefers-reduced-motion: reduce) {
        .bims-stats > div, .bims-pulse { animation: none; }
    }
</style>

<script>
    function openModal(id) {
        const m = document.getElementById(id);
        m.classList.remove('hidden');
        m.classList.add('flex');
    }

    function closeModal(id) {
        const m = document.getElementById(id);
        m.classList.add('hidden');
        m.classList.remove('flex');
    }

    function openApprove(btn) {
        const d = btn.dataset;
        const isMedicine = d.medicine === '1';

        document.getElementById('approveItemName').textContent = d.item;
        document.getElementById('approveQty').textContent = d.qty;
        document.getElementById('approveResident').textContent = d.resident;

        const dueDateGroup = document.getElementById('dueDateGroup');
        const dueDateInput = document.getElementById('approveDueDate');
        const medicineNotice = document.getElementById('medicineNotice');

        if (isMedicine) {
            dueDateGroup.classList.add('hidden');
            dueDateInput.removeAttribute('required');
            dueDateInput.value = '';
            medicineNotice.classList.remove('hidden');
            medicineNotice.classList.add('flex');
        } else {
            dueDateGroup.classList.remove('hidden');
            dueDateInput.setAttribute('required', 'required');
            medicineNotice.classList.add('hidden');
            medicineNotice.classList.remove('flex');
        }

        document.getElementById('approveForm').action = "{{ url('admin/equipment/requests') }}/" + d.id + "/approve";
        openModal('approveModal');
    }

    function openReject(btn) {
        const d = btn.dataset;
        document.getElementById('rejectItemName').textContent = d.item;
        document.getElementById('rejectForm').action = "{{ url('admin/equipment/requests') }}/" + d.id + "/reject";
        openModal('rejectModal');
    }

    ['approveModal', 'rejectModal'].forEach(function (modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) closeModal(modalId);
            });
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeModal('approveModal');
            closeModal('rejectModal');
        }
    });
</script>
@endsection
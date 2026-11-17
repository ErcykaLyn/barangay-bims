@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-slate-100">

    <!-- ============ TOP ACTION BAR ============ -->
    <div class="bg-white border-b border-slate-200 shadow-sm">
        <div class="px-4 sm:px-6 lg:px-8 py-3">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="h-9 w-9 flex items-center justify-center rounded-lg bg-amber-100 text-amber-600 flex-shrink-0">
                        <i class="fas fa-boxes text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <nav class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                            <span>Dashboard</span>
                            <i class="fas fa-chevron-right text-[8px]"></i>
                            <span class="text-slate-700">Inventory</span>
                        </nav>
                        <h1 class="text-sm sm:text-base font-black text-slate-800 tracking-tight truncate">
                            Equipment Inventory
                        </h1>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="{{ route('admin.equipment.requests') }}"
                       class="relative inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-lg text-[10px] font-bold uppercase tracking-wider border border-indigo-200 hover:bg-indigo-100 transition-colors">
                        <i class="fas fa-clipboard-list text-[10px]"></i>
                        <span class="hidden sm:inline">All Requests</span>
                        <span class="sm:hidden">Requests</span>
                        @php
                            $pendingReqs = \App\Models\EquipmentRequest::where('status', 'pending')->count();
                        @endphp
                        @if($pendingReqs > 0)
                            <span class="absolute -top-1.5 -right-1.5 h-4 min-w-[16px] bg-amber-500 text-white text-[8px] font-black rounded-full flex items-center justify-center px-1 animate-pulse">
                                {{ $pendingReqs }}
                            </span>
                        @endif
                    </a>
                    <button onclick="document.getElementById('addModal').classList.remove('hidden')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[10px] font-bold uppercase tracking-wider shadow-sm transition-colors">
                        <i class="fas fa-plus text-[10px]"></i> Add Item
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ============ ALERT MESSAGES ============ -->
    <div class="px-4 sm:px-6 lg:px-8 pt-4 space-y-2">
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 x-transition:leave="transition ease-in duration-300"
                 class="flex items-center gap-3 px-4 py-3 bg-emerald-50 border-l-4 border-emerald-500 rounded-lg shadow-sm">
                <i class="fas fa-check-circle text-emerald-600"></i>
                <span class="text-sm font-semibold text-emerald-800 flex-1">{{ session('success') }}</span>
                <button @click="show = false" class="text-emerald-400 hover:text-emerald-600"><i class="fas fa-times text-xs"></i></button>
            </div>
        @endif
        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                 x-transition:leave="transition ease-in duration-300"
                 class="flex items-center gap-3 px-4 py-3 bg-rose-50 border-l-4 border-rose-500 rounded-lg shadow-sm">
                <i class="fas fa-exclamation-circle text-rose-600"></i>
                <span class="text-sm font-semibold text-rose-800 flex-1">{{ session('error') }}</span>
                <button @click="show = false" class="text-rose-400 hover:text-rose-600"><i class="fas fa-times text-xs"></i></button>
            </div>
        @endif
    </div>

    <!-- ============ MAIN CONTENT ============ -->
    <div class="px-4 sm:px-6 lg:px-8 py-6 space-y-4">

        <!-- ===== STATS ROW ===== -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-white border border-slate-200 rounded-lg px-4 py-3 flex items-center gap-3">
                <div class="h-10 w-10 bg-slate-100 rounded-lg flex items-center justify-center text-slate-600 flex-shrink-0">
                    <i class="fas fa-cubes text-base"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Total Items</p>
                    <p class="text-lg font-black text-slate-800 leading-tight">{{ $totalItems }}</p>
                </div>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg px-4 py-3 flex items-center gap-3">
                <div class="h-10 w-10 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600 flex-shrink-0">
                    <i class="fas fa-check-circle text-base"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Available</p>
                    <p class="text-lg font-black text-emerald-600 leading-tight">{{ $totalAvailable }}</p>
                </div>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg px-4 py-3 flex items-center gap-3">
                <div class="h-10 w-10 bg-amber-100 rounded-lg flex items-center justify-center text-amber-600 flex-shrink-0">
                    <i class="fas fa-hand-holding text-base"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Borrowed</p>
                    <p class="text-lg font-black text-amber-600 leading-tight">{{ $totalBorrowed }}</p>
                </div>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg px-4 py-3 flex items-center gap-3">
                <div class="h-10 w-10 bg-rose-100 rounded-lg flex items-center justify-center text-rose-600 flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-base"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Damaged</p>
                    <p class="text-lg font-black text-rose-600 leading-tight">{{ $totalDamaged }}</p>
                </div>
            </div>
        </div>

        <!-- ===== INFO BANNER ===== -->
        <div class="bg-gradient-to-r from-indigo-50 to-indigo-100/50 border border-indigo-200 rounded-lg p-4 flex items-start gap-3">
            <div class="h-9 w-9 bg-indigo-500 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-info-circle text-white text-sm"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-xs font-black text-indigo-800 uppercase tracking-wider mb-0.5">Request-Based System</h3>
                <p class="text-[11px] text-indigo-700 leading-relaxed">
                    Residents submit <strong>borrow requests</strong> for equipment and <strong>claim requests</strong> for medicines/supplies through their portal. Use the <strong>Requests</strong> page to approve or reject. Stock is automatically deducted when items are picked up and restored when returned. <strong class="text-purple-700">Medicine items</strong> follow a <strong>dispensing flow</strong> (no return required).
                </p>
            </div>
        </div>

        <!-- ===== INVENTORY GRID ===== -->
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="h-7 w-7 bg-slate-200 rounded-md flex items-center justify-center text-slate-600">
                        <i class="fas fa-list text-xs"></i>
                    </div>
                    <div>
                        <h2 class="text-xs font-black text-slate-800 uppercase tracking-wider">Inventory Items</h2>
                        <p class="text-[10px] text-slate-500">{{ $totalItems }} items in stock</p>
                    </div>
                </div>
            </div>

            <div class="p-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @forelse($equipments as $item)
                    @php
                        $isConsumable = in_array(strtolower($item->category ?? ''), ['medicine', 'medicines', 'supplies', 'supply']);
                        $requestLabel = $isConsumable ? 'Claim' : 'Borrow';
                        $requestIcon = $isConsumable ? 'fa-hand-holding-medical' : 'fa-clipboard-list';
                        $requestColor = $isConsumable ? 'purple' : 'indigo';
                    @endphp
                    <div class="bg-white border border-slate-200 rounded-lg overflow-hidden hover:border-emerald-300 hover:shadow-md transition-all">
                        <!-- Photo + Status -->
                        <div class="h-32 bg-slate-100 relative overflow-hidden">
                            @if($item->photo_url)
                                <img src="{{ $item->photo_url }}" alt="{{ $item->item_name }}" class="h-full w-full object-cover">
                            @else
                                <div class="h-full w-full flex items-center justify-center text-slate-300">
                                    <i class="fas fa-image text-4xl"></i>
                                </div>
                            @endif
                            <div class="absolute top-2 right-2 flex flex-col gap-1 items-end">
                                <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase
                                    @if($item->status == 'available') bg-emerald-500 text-white
                                    @elseif($item->status == 'borrowed') bg-amber-500 text-white
                                    @elseif($item->status == 'damaged') bg-rose-500 text-white
                                    @else bg-slate-500 text-white @endif">
                                    {{ $item->status }}
                                </span>
                                {{-- Medicine / Consumable Badge --}}
                                @if($isConsumable)
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase bg-purple-500 text-white">
                                        <i class="fas fa-pills text-[7px] mr-0.5"></i> Med
                                    </span>
                                @endif
                                @php
                                    $itemPendingReqs = $item->requests()->where('status', 'pending')->count();
                                @endphp
                                @if($itemPendingReqs > 0)
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase bg-indigo-500 text-white animate-pulse">
                                        {{ $itemPendingReqs }} pending
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-3">
                            <h3 class="text-xs font-black text-slate-800 uppercase truncate">{{ $item->item_name }}</h3>
                            <p class="text-[9px] text-slate-400 font-mono mt-0.5">#EQ-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</p>

                            <!-- Stock breakdown -->
                            <div class="mt-2 grid grid-cols-3 gap-1 text-center">
                                <div class="bg-emerald-50 rounded px-1 py-1 border border-emerald-100">
                                    <p class="text-[10px] text-emerald-700 font-black">{{ $item->available_stock }}</p>
                                    <p class="text-[7px] text-emerald-600 uppercase font-bold">Avail</p>
                                </div>
                                <div class="bg-amber-50 rounded px-1 py-1 border border-amber-100">
                                    <p class="text-[10px] text-amber-700 font-black">{{ $item->borrowed_stock }}</p>
                                    <p class="text-[7px] text-amber-600 uppercase font-bold">Out</p>
                                </div>
                                <div class="bg-rose-50 rounded px-1 py-1 border border-rose-100">
                                    <p class="text-[10px] text-rose-700 font-black">{{ $item->damaged_stock }}</p>
                                    <p class="text-[7px] text-rose-600 uppercase font-bold">Dmg</p>
                                </div>
                            </div>

                            <!-- Actions (Request-Based Flow) -->
                            <div class="mt-3 flex items-center gap-1">
                                <a href="{{ route('admin.equipment.requests', ['equipment' => $item->id]) }}"
                                   class="flex-1 h-7 flex items-center justify-center bg-{{ $requestColor }}-600 hover:bg-{{ $requestColor }}-700 text-white rounded text-[9px] font-bold uppercase transition-colors">
                                    <i class="fas {{ $requestIcon }} text-[9px] mr-1"></i>{{ $requestLabel }} Requests
                                </a>
                                <a href="{{ route('admin.equipment.history', $item->id) }}"
                                   class="h-7 w-7 flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-600 rounded transition-colors"
                                   title="History">
                                    <i class="fas fa-history text-[10px]"></i>
                                </a>
                                <form action="{{ route('admin.equipment.destroy', $item->id) }}" method="POST" class="inline"
                                      data-confirm="Permanenteng mabubura ang item na &quot;{{ $item->item_name }}&quot; sa inventory."
                                      data-confirm-title="Burahin ang item?"
                                      data-confirm-label="Delete">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="h-7 w-7 flex items-center justify-center bg-rose-100 hover:bg-rose-200 text-rose-600 rounded transition-colors" title="Delete">
                                        <i class="fas fa-trash text-[10px]"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full text-center py-16">
                        <div class="h-16 w-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-box-open text-2xl text-slate-300"></i>
                        </div>
                        <p class="text-sm font-bold text-slate-500">No equipment yet</p>
                        <p class="text-xs text-slate-400 mt-1">Click "Add Item" to start</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========== ADD ITEM MODAL ========== -->
<div id="addModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="px-5 py-3 border-b border-slate-200 bg-slate-50 flex justify-between items-center sticky top-0 z-10">
            <h3 class="text-sm font-black text-slate-800 uppercase">Add New Equipment</h3>
            <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('admin.equipment.store') }}" method="POST" enctype="multipart/form-data" class="p-5 space-y-4">
            @csrf
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">Item Name *</label>
                <input type="text" name="item_name" required class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 outline-none">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">Category *</label>
                <select name="category" id="categorySelect" required onchange="toggleMedicineNote()"
                        class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 outline-none">
                    <option value="chair">Chair / Furniture</option>
                    <option value="tent">Tent / Cover</option>
                    <option value="medicine">Medicine / Medical</option>
                    <option value="sports">Sports Equipment</option>
                    <option value="electronics">Electronics</option>
                    <option value="other">Other</option>
                </select>

                <!-- Medicine Note (auto-shows when medicine is selected) -->
                <div id="medicineNote" class="hidden mt-2 flex items-start gap-2 p-2.5 bg-purple-50 border border-purple-200 rounded-md">
                    <i class="fas fa-info-circle text-purple-600 mt-0.5 text-xs"></i>
                    <p class="text-[10px] text-purple-700 leading-relaxed">
                        <strong>Medicine category:</strong> Items marked as medicine follow a <strong>dispensing flow</strong> (no return required). Residents will <strong>claim</strong> these items directly.
                    </p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">Quantity *</label>
                    <input type="number" name="quantity" min="1" required class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">Unit Price</label>
                    <input type="number" name="unit_price" step="0.01" min="0" class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 outline-none">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">Description</label>
                <textarea name="description" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 outline-none"></textarea>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">Photo</label>
                <input type="file" name="photo" accept="image/*" class="w-full text-xs file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                <p class="text-[9px] text-slate-400 mt-1">JPG, PNG, WEBP • Max 2MB</p>
            </div>
            <div class="pt-3 border-t flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')"
                        class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-md">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-md uppercase tracking-wider">
                    <i class="fas fa-save mr-1"></i> Save Item
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Close modal on backdrop click
    ['addModal'].forEach(function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        }
    });

    // Toggle medicine note based on category
    function toggleMedicineNote() {
        const category = document.getElementById('categorySelect').value;
        const note = document.getElementById('medicineNote');
        if (category === 'medicine') {
            note.classList.remove('hidden');
        } else {
            note.classList.add('hidden');
        }
    }
</script>
@endsection
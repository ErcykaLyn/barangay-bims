@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-slate-100">

    {{-- ============ TOP ACTION BAR ============ --}}
    <div class="bg-white border-b border-slate-200 shadow-sm">
        <div class="px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="h-11 w-11 flex items-center justify-center rounded-xl bg-gradient-to-br from-slate-600 to-slate-800 text-white shadow-md flex-shrink-0">
                        <i class="fas fa-history text-base"></i>
                    </div>
                    <div class="min-w-0">
                        <nav class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                            <span>Dashboard</span>
                            <i class="fas fa-chevron-right text-[8px]"></i>
                            <a href="{{ route('admin.requests.index') }}" class="hover:text-emerald-600 transition">Requests</a>
                            <i class="fas fa-chevron-right text-[8px]"></i>
                            <span class="text-emerald-600">Logs</span>
                        </nav>
                        <h1 class="text-base sm:text-lg font-black text-slate-800 tracking-tight truncate">
                            Request Logs
                        </h1>
                    </div>
                </div>
                <a href="{{ route('admin.requests.index') }}" 
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[10px] font-black uppercase tracking-wider shadow-sm transition-colors">
                    <i class="fas fa-arrow-left text-[10px]"></i>
                    Back to Active Requests
                </a>
            </div>
        </div>
    </div>

    {{-- ============ MAIN CONTENT ============ --}}
    <div class="px-4 sm:px-6 lg:px-8 py-6 space-y-4">

        {{-- ===== STATS ROW ===== --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            {{-- Total Logs --}}
            <div class="bg-white border border-slate-200 rounded-xl px-4 py-3 flex items-center gap-3 shadow-sm hover:shadow-md transition-shadow">
                <div class="h-11 w-11 bg-gradient-to-br from-slate-500 to-slate-700 rounded-xl flex items-center justify-center text-white flex-shrink-0 shadow-md">
                    <i class="fas fa-archive text-base"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Total Logs</p>
                    <p class="text-xl font-black text-slate-800 leading-tight">{{ $logStats['total'] }}</p>
                </div>
            </div>

            {{-- Completed --}}
            <div class="bg-white border border-slate-200 rounded-xl px-4 py-3 flex items-center gap-3 shadow-sm hover:shadow-md transition-shadow">
                <div class="h-11 w-11 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl flex items-center justify-center text-white flex-shrink-0 shadow-md">
                    <i class="fas fa-check-double text-base"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Completed</p>
                    <p class="text-xl font-black text-emerald-600 leading-tight">{{ $logStats['completed'] }}</p>
                </div>
            </div>

            {{-- Denied --}}
            <div class="bg-white border border-slate-200 rounded-xl px-4 py-3 flex items-center gap-3 shadow-sm hover:shadow-md transition-shadow">
                <div class="h-11 w-11 bg-gradient-to-br from-rose-500 to-rose-700 rounded-xl flex items-center justify-center text-white flex-shrink-0 shadow-md">
                    <i class="fas fa-times-circle text-base"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Denied</p>
                    <p class="text-xl font-black text-rose-600 leading-tight">{{ $logStats['denied'] }}</p>
                </div>
            </div>
        </div>

        {{-- ===== FILTER CARD ===== --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm">
            <div class="px-5 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="h-7 w-7 bg-slate-200 rounded-md flex items-center justify-center text-slate-600">
                        <i class="fas fa-filter text-xs"></i>
                    </div>
                    <div>
                        <h2 class="text-xs font-black text-slate-800 uppercase tracking-wider">Filter Logs</h2>
                        <p class="text-[10px] text-slate-500">Search archived requests</p>
                    </div>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.requests.logs') }}" class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                    {{-- Search --}}
                    <div class="lg:col-span-5">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1.5">Search</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Resident name, username, or request ID..."
                                   class="w-full pl-9 pr-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                        </div>
                    </div>

                    {{-- Document Type --}}
                    <div class="lg:col-span-4">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1.5">Document Type</label>
                        <select name="document_type"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all cursor-pointer">
                            <option value="">All Documents</option>
                            <option value="Barangay Residency Certificate" {{ request('document_type') == 'Barangay Residency Certificate' ? 'selected' : '' }}>Barangay Residency Certificate</option>
                            <option value="Barangay Indigency Certificate" {{ request('document_type') == 'Barangay Indigency Certificate' ? 'selected' : '' }}>Barangay Indigency Certificate</option>
                            <option value="Barangay Clearance Certification" {{ request('document_type') == 'Barangay Clearance Certification' ? 'selected' : '' }}>Barangay Clearance Certification</option>
                            <option value="Good Moral Certificate" {{ request('document_type') == 'Good Moral Certificate' ? 'selected' : '' }}>Good Moral Certificate</option>
                            <option value="Barangay Business Clearance" {{ request('document_type') == 'Barangay Business Clearance' ? 'selected' : '' }}>Barangay Business Clearance</option>
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="lg:col-span-3">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1.5">Status</label>
                        <select name="status"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all cursor-pointer">
                            <option value="">All Status</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="denied" {{ request('status') == 'denied' ? 'selected' : '' }}>Denied</option>
                        </select>
                    </div>

                    {{-- Buttons --}}
                    <div class="lg:col-span-12 flex items-end gap-2 justify-end">
                        <button type="submit" 
                            class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-md shadow-sm transition-colors flex items-center justify-center gap-1.5">
                            <i class="fas fa-search text-[10px]"></i>
                            Filter
                        </button>
                        <a href="{{ route('admin.requests.logs') }}" 
                           class="px-5 py-2 bg-white border border-slate-300 text-slate-600 text-xs font-bold rounded-md hover:bg-slate-50 transition-colors flex items-center justify-center gap-1.5">
                            <i class="fas fa-redo text-[10px]"></i>
                            Reset
                        </a>
                    </div>
                </div>

                {{-- Active Filters --}}
                @if(request('search') || request('status') || request('document_type'))
                    <div class="mt-3 pt-3 border-t border-slate-200 flex flex-wrap items-center gap-2">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Active:</span>
                        @if(request('search'))
                            <span class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-1 rounded-md border border-emerald-200">
                                <i class="fas fa-search text-[8px]"></i>
                                "{{ request('search') }}"
                            </span>
                        @endif
                        @if(request('document_type'))
                            <span class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-1 rounded-md border border-emerald-200">
                                <i class="fas fa-file-alt text-[8px]"></i>
                                {{ request('document_type') }}
                            </span>
                        @endif
                        @if(request('status'))
                            <span class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-1 rounded-md border border-emerald-200">
                                <i class="fas fa-tag text-[8px]"></i>
                                {{ ucfirst(request('status')) }}
                            </span>
                        @endif
                    </div>
                @endif
            </form>
        </div>

        {{-- ===== LOGS TABLE ===== --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            {{-- Table Header --}}
            <div class="px-5 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="h-7 w-7 bg-slate-200 rounded-md flex items-center justify-center text-slate-600">
                        <i class="fas fa-archive text-xs"></i>
                    </div>
                    <div>
                        <h2 class="text-xs font-black text-slate-800 uppercase tracking-wider">Archived Requests</h2>
                        <p class="text-[10px] text-slate-500">{{ $logs->count() }} {{ $logs->count() === 1 ? 'record' : 'records' }} found</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider hidden sm:block">
                    <i class="fas fa-lock mr-1"></i> Read-only
                </span>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[1200px]">
                    <thead class="bg-slate-50/50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-[10px] font-black text-slate-500 uppercase tracking-wider w-12">#</th>
                            <th class="px-4 py-3 text-[10px] font-black text-slate-500 uppercase tracking-wider">Resident</th>
                            <th class="px-4 py-3 text-[10px] font-black text-slate-500 uppercase tracking-wider">Document</th>
                            <th class="px-4 py-3 text-[10px] font-black text-slate-500 uppercase tracking-wider">Purpose</th>
                            <th class="px-4 py-3 text-[10px] font-black text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-[10px] font-black text-slate-500 uppercase tracking-wider">Processed</th>
                            <th class="px-4 py-3 text-[10px] font-black text-slate-500 uppercase tracking-wider text-center w-24">Print</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($logs as $index => $log)
                        @php
                            $badge = $log->status === 'completed'
                                ? 'bg-emerald-100 text-emerald-700 border-emerald-200'
                                : 'bg-rose-100 text-rose-700 border-rose-200';
                            $icon = $log->status === 'completed' ? 'fa-check-double' : 'fa-times-circle';
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            {{-- Row Number --}}
                            <td class="px-4 py-3 text-xs text-slate-500 font-mono text-center">
                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                            </td>

                            {{-- Resident --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-lg overflow-hidden flex-shrink-0 ring-1 ring-slate-200 flex items-center justify-center bg-gradient-to-br from-slate-500 to-slate-700">
                                        @if($log->resident && $log->resident->profile_photo_url)
                                            <img src="{{ $log->resident->profile_photo_url }}" alt="{{ $log->resident->first_name }}" class="h-full w-full object-cover">
                                        @else
                                            <span class="text-white text-xs font-black">
                                                {{ strtoupper(substr($log->resident->first_name ?? 'X', 0, 1) . substr($log->resident->last_name ?? 'X', 0, 1)) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-slate-800 leading-tight uppercase">
                                            {{ $log->resident->last_name ?? 'N/A' }}, {{ $log->resident->first_name ?? '' }}
                                        </p>
                                        <p class="text-[10px] text-slate-400 font-mono mt-0.5">
                                            {{ '@' . ($log->resident->username ?? 'N/A') }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- Document --}}
                            <td class="px-4 py-3">
                                <p class="text-sm font-bold text-slate-700">{{ $log->document_type }}</p>
                                <p class="text-[10px] text-slate-400 font-mono mt-0.5">
                                    {{ $log->reference_number }}
                                </p>
                            </td>

                            {{-- Purpose --}}
                            <td class="px-4 py-3 max-w-xs">
                                <p class="text-xs text-slate-600 truncate" title="{{ $log->purpose }}">
                                    {{ $log->purpose }}
                                </p>
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider border {{ $badge }}">
                                    <i class="fas {{ $icon }} text-[9px]"></i>
                                    {{ ucfirst($log->status) }}
                                </span>
                            </td>

                            {{-- Processed Date --}}
                            <td class="px-4 py-3">
                                @if($log->completed_at)
                                    <p class="text-xs text-slate-700 font-mono font-semibold">
                                        {{ $log->completed_at->format('M d, Y') }}
                                    </p>
                                    <p class="text-[10px] text-slate-400 flex items-center gap-1 mt-0.5">
                                        <i class="far fa-clock text-[8px]"></i>
                                        {{ $log->completed_at->diffForHumans() }}
                                    </p>
                                @elseif($log->processed_at)
                                    <p class="text-xs text-slate-700 font-mono font-semibold">
                                        {{ $log->processed_at->format('M d, Y') }}
                                    </p>
                                    <p class="text-[10px] text-slate-400 flex items-center gap-1 mt-0.5">
                                        <i class="far fa-clock text-[8px]"></i>
                                        {{ $log->processed_at->diffForHumans() }}
                                    </p>
                                @else
                                    <span class="text-slate-400 text-xs">—</span>
                                @endif
                            </td>

                            {{-- Print --}}
                            <td class="px-4 py-3 text-center">
                                @if($log->status === 'completed')
                                    <a href="{{ route('admin.requests.print', $log->id) }}" 
                                       target="_blank"
                                       class="h-8 w-8 inline-flex items-center justify-center rounded-md bg-slate-800 hover:bg-slate-900 text-white transition-colors shadow-sm"
                                       title="Print Document">
                                        <i class="fas fa-print text-[10px]"></i>
                                    </a>
                                @else
                                    <span class="text-slate-300 text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-16 w-16 bg-slate-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-archive text-2xl text-slate-300"></i>
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-700 mb-1">No logs yet</h3>
                                    <p class="text-xs text-slate-400">Completed and denied requests will appear here.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-slate-100">

    <!-- ============ TOP ACTION BAR ============ -->
    <div class="bg-white border-b border-slate-200 shadow-sm">
        <div class="px-4 sm:px-6 lg:px-8 py-3">
            <div class="flex items-center justify-between gap-4">
                <!-- Left: Breadcrumb -->
                <div class="flex items-center gap-3 min-w-0">
                    <div class="h-9 w-9 flex items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 flex-shrink-0">
                        <i class="fas fa-user-tie text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <nav class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                            <span>Dashboard</span>
                            <i class="fas fa-chevron-right text-[8px]"></i>
                            <span class="text-slate-700">Staff</span>
                        </nav>
                        <h1 class="text-sm sm:text-base font-black text-slate-800 tracking-tight truncate">
                            Staff Account Management
                        </h1>
                    </div>
                </div>

                <!-- Right: Actions -->
                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="{{ route('admin.users.create') }}" 
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[10px] font-bold uppercase tracking-wider shadow-sm transition-colors">
                        <i class="fas fa-user-plus text-[10px]"></i>
                        <span class="hidden sm:inline">Create New Staff</span>
                        <span class="sm:hidden">New</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ============ ALERT MESSAGES ============ -->
    <div class="px-4 sm:px-6 lg:px-8 pt-4">
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 x-transition:leave="transition ease-in duration-300"
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
                 x-transition:leave="transition ease-in duration-300"
                 class="flex items-center gap-3 px-4 py-3 bg-rose-50 border-l-4 border-rose-500 rounded-lg shadow-sm">
                <i class="fas fa-exclamation-circle text-rose-600"></i>
                <span class="text-sm font-semibold text-rose-800 flex-1">{{ session('error') }}</span>
                <button @click="show = false" class="text-rose-400 hover:text-rose-600">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        @endif
    </div>

    <!-- ============ MAIN CONTENT ============ -->
    <div class="px-4 sm:px-6 lg:px-8 py-6 space-y-4">

        <!-- ===== STATS ROW ===== -->
        @php
            $totalStaff = $users->count();
            $totalAdmins = $users->where('role', 'admin')->count();
            $totalBhw = $users->where('role', 'bhw')->count();
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <!-- Total Staff -->
            <div class="bg-white border border-slate-200 rounded-lg px-4 py-3 flex items-center gap-3">
                <div class="h-10 w-10 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600 flex-shrink-0">
                    <i class="fas fa-users text-base"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Total Staff</p>
                    <p class="text-lg font-black text-slate-800 leading-tight">{{ $totalStaff }}</p>
                </div>
            </div>

            <!-- Admins -->
            <div class="bg-white border border-slate-200 rounded-lg px-4 py-3 flex items-center gap-3">
                <div class="h-10 w-10 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600 flex-shrink-0">
                    <i class="fas fa-user-shield text-base"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Administrators</p>
                    <p class="text-lg font-black text-slate-800 leading-tight">{{ $totalAdmins }}</p>
                </div>
            </div>

            <!-- BHW -->
            <div class="bg-white border border-slate-200 rounded-lg px-4 py-3 flex items-center gap-3">
                <div class="h-10 w-10 bg-cyan-100 rounded-lg flex items-center justify-center text-cyan-600 flex-shrink-0">
                    <i class="fas fa-user-nurse text-base"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">BHW Personnel</p>
                    <p class="text-lg font-black text-slate-800 leading-tight">{{ $totalBhw }}</p>
                </div>
            </div>
        </div>

        <!-- ===== TABLE CARD ===== -->
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
            <!-- Table Header -->
            <div class="px-5 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="h-7 w-7 bg-slate-200 rounded-md flex items-center justify-center text-slate-600">
                        <i class="fas fa-list text-xs"></i>
                    </div>
                    <div>
                        <h2 class="text-xs font-black text-slate-800 uppercase tracking-wider">Staff Directory</h2>
                        <p class="text-[10px] text-slate-500">{{ $totalStaff }} {{ $totalStaff === 1 ? 'account' : 'accounts' }} registered</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider hidden sm:block">
                    Newest First
                </span>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead class="bg-slate-50/50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-[10px] font-black text-slate-500 uppercase tracking-wider text-center w-12">#</th>
                            <th class="px-4 py-3 text-[10px] font-black text-slate-500 uppercase tracking-wider">Name</th>
                            <th class="px-4 py-3 text-[10px] font-black text-slate-500 uppercase tracking-wider">Email</th>
                            <th class="px-4 py-3 text-[10px] font-black text-slate-500 uppercase tracking-wider">Role</th>
                            <th class="px-4 py-3 text-[10px] font-black text-slate-500 uppercase tracking-wider">Created</th>
                            <th class="px-4 py-3 text-[10px] font-black text-slate-500 uppercase tracking-wider text-center w-32">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $index => $user)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <!-- Row Number -->
                            <td class="px-4 py-3 text-xs text-slate-500 font-mono text-center">
                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                            </td>

                            <!-- Name & Avatar -->
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-lg overflow-hidden flex-shrink-0 ring-1 ring-slate-200 flex items-center justify-center bg-gradient-to-br {{ $user->role === 'admin' ? 'from-indigo-500 to-indigo-700' : 'from-cyan-500 to-cyan-700' }}">
                                        @if($user->profile_photo_url ?? false)
                                            <img src="{{ $user->profile_photo_url }}" alt="{{ $user->first_name }}" class="h-full w-full object-cover">
                                        @else
                                            <span class="text-white text-xs font-black">
                                                {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-slate-800 leading-tight">
                                            {{ $user->first_name }} {{ $user->last_name }}
                                        </p>
                                        @if($user->id === auth()->id())
                                            <span class="inline-flex items-center gap-1 text-[9px] font-black text-emerald-600 uppercase tracking-wider mt-0.5">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                You
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Email -->
                            <td class="px-4 py-3">
                                <span class="text-xs text-slate-600 font-mono">{{ $user->email }}</span>
                            </td>

                            <!-- Role -->
                            <td class="px-4 py-3">
                                @if($user->role === 'admin')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-indigo-100 text-indigo-700 rounded-md text-[10px] font-black uppercase tracking-wider border border-indigo-200">
                                        <i class="fas fa-user-shield text-[9px]"></i>
                                        Administrator
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-cyan-100 text-cyan-700 rounded-md text-[10px] font-black uppercase tracking-wider border border-cyan-200">
                                        <i class="fas fa-user-nurse text-[9px]"></i>
                                        BHW
                                    </span>
                                @endif
                            </td>

                            <!-- Created -->
                            <td class="px-4 py-3">
                                <p class="text-xs font-bold text-slate-700">{{ $user->created_at->format('M d, Y') }}</p>
                                <p class="text-[10px] text-slate-400">{{ $user->created_at->diffForHumans() }}</p>
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center">
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this account?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="h-8 w-8 flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition-all border border-slate-200 hover:border-rose-200"
                                                    title="Delete Account">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-600 rounded-md text-[10px] font-black uppercase tracking-wider border border-emerald-200">
                                            <i class="fas fa-lock text-[9px]"></i>
                                            Protected
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-16 w-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-users text-2xl text-slate-300"></i>
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-700 mb-1">No staff accounts yet</h3>
                                    <p class="text-xs text-slate-400 mb-4">Get started by creating your first staff member.</p>
                                    <a href="{{ route('admin.users.create') }}" 
                                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-md shadow-sm transition-colors">
                                        <i class="fas fa-user-plus text-[10px]"></i>
                                        Create First Staff
                                    </a>
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
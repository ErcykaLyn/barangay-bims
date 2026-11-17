@extends('layouts.auth')

@section('title', 'BIMS | Admin Login')
@section('badge-icon', 'fas fa-user-shield')
@section('badge-text', 'Administrator Portal')
@section('visual-title', 'Administrator')
@section('visual-highlight', 'Control Center')
@section('visual-description', 'Full access to resident records, service requests, inventory, and system-wide management tools.')
@section('visual-footer', 'Administrator Access Only')

@section('form-content')
    @if($errors->any())
        <div class="mb-5 p-3 bg-rose-50 border border-rose-200 rounded-xl" role="alert">
            <div class="flex items-start gap-2.5">
                <i class="fas fa-circle-exclamation text-rose-500 text-sm mt-0.5"></i>
                <ul class="text-xs text-rose-700 space-y-0.5 flex-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="mb-6">
        <h2 class="text-2xl font-extrabold text-emerald-950">Admin sign in</h2>
        <p class="text-sm text-slate-500 mt-1">Use your staff account to open the portal.</p>
    </div>

    <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
        @csrf
        <input type="hidden" name="role" value="admin">

        <div class="input-wrapper">
            <label for="email" class="lbl">Staff email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" class="custom-input" placeholder="admin@bactadeast.gov.ph" required autofocus>
            <i class="fas fa-user-shield input-icon"></i>
        </div>

        <div class="input-wrapper">
            <label for="password" class="lbl">Password</label>
            <input type="password" name="password" id="password" class="custom-input" placeholder="••••••••••••" required>
            <i class="fas fa-lock input-icon"></i>
            <button type="button" class="toggle-password" data-target="password" aria-label="Show password"><i class="fas fa-eye-slash"></i></button>
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" class="check">
                <span class="text-xs text-slate-600">Keep me logged in</span>
            </label>
            <a href="#" class="text-xs text-link">Forgot password?</a>
        </div>

        <button type="submit" class="login-btn">
            <span>Login as Admin</span><i class="fas fa-arrow-right-long text-xs"></i>
        </button>
    </form>

    <p class="mt-6 pt-5 border-t border-emerald-900/10 text-center text-xs text-slate-500">
        <i class="fas fa-lock mr-1"></i> Admin accounts are created by existing administrators only.
    </p>
@endsection
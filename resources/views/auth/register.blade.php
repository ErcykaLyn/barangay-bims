<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="theme-color" content="#ffffff">
    <title>BIMS | Register Account — Bactad East</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --ink:    #0f172a;
            --text:   #334155;
            --muted:  #64748b;
            --faint:  #7a889d;
            --line:   #e4e9f0;
            --soft:   #f6f8fb;
            --em:     #10b981;
            --em-d:   #059669;
            --em-bg:  #ecfdf5;
            --rose:   #e11d48;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        [hidden] { display: none !important; }
        html { -webkit-text-size-adjust: 100%; }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background: #fff;
            color: var(--ink);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        .rg-wrap { position: relative; max-width: 780px; margin: 0 auto; padding: 32px 16px 64px; }

        .rg-top { text-align: center; padding: 8px 8px 6px; }
        .rg-logo {
            display: block;
            width: 72px; height: 72px;
            margin: 0 auto 16px;
            border-radius: 22px;
            object-fit: cover;
            background: #fff;
            border: 1px solid var(--line);
            box-shadow: 0 16px 30px -16px rgba(15, 23, 42, .35);
        }
        .rg-brand {
            font-size: 11px; font-weight: 800;
            letter-spacing: .22em; text-transform: uppercase;
            color: var(--em-d);
        }
        .rg-top h1 { margin-top: 8px; font-size: 26px; font-weight: 800; letter-spacing: -.02em; line-height: 1.2; }
        .rg-top p  { max-width: 440px; margin: 10px auto 0; font-size: 14px; line-height: 1.6; color: var(--muted); }
        .rg-login  { margin-top: 14px; font-size: 13px; color: var(--muted); }
        .rg-login a { font-weight: 700; color: var(--em-d); text-decoration: none; }
        .rg-login a:hover { text-decoration: underline; }
        @media (min-width: 640px) { .rg-top h1 { font-size: 32px; } }

        .rg-alert {
            display: flex; align-items: flex-start; gap: 12px;
            margin-top: 22px;
            padding: 14px 16px;
            border-radius: 18px;
            font-size: 13px; line-height: 1.55;
            border: 1px solid;
        }
        .rg-alert > i { margin-top: 2px; }
        .rg-alert ul { margin: 0; padding-left: 16px; display: grid; gap: 3px; }
        .rg-alert--ok  { background: #ecfdf5; border-color: #a7f3d0; color: #065f46; }
        .rg-alert--err { background: #fff1f2; border-color: #fecdd3; color: #9f1239; }

        .rg-form { margin-top: 8px; }
        .rg-card {
            margin-top: 18px;
            padding: 22px 18px 24px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 28px;
            box-shadow: 0 22px 44px -34px rgba(15, 23, 42, .3);
        }
        @media (min-width: 640px) { .rg-card { padding: 30px 32px 34px; border-radius: 32px; } }

        .rg-head { display: flex; align-items: center; gap: 14px; margin-bottom: 24px; }
        .rg-num {
            flex: none;
            width: 34px; height: 34px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 12px;
            font-size: 14px; font-weight: 800;
            color: #fff; background: var(--ink);
        }
        .rg-head h2 { font-size: 16px; font-weight: 800; letter-spacing: -.01em; }
        .rg-head p  { margin-top: 3px; font-size: 12.5px; color: var(--faint); }

        .rg-grid { display: grid; grid-template-columns: 1fr; gap: 20px; }
        @media (min-width: 640px) { .rg-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 22px 18px; } }
        .rg-full { grid-column: 1 / -1; }

        .rg-lbl { display: block; margin-bottom: 8px; font-size: 13px; font-weight: 700; color: var(--text); }
        .rg-lbl .req { color: var(--rose); margin-left: 2px; }
        .rg-lbl .opt { font-weight: 500; color: var(--faint); font-size: 12px; }

        .rg-field { position: relative; }
        .rg-field .ic {
            position: absolute; left: 16px; top: 50%;
            transform: translateY(-50%);
            font-size: 14px; color: #a6b2c3;
            pointer-events: none;
            transition: color .2s;
        }
        .rg-in {
            display: block;
            width: 100%;
            height: 52px;
            padding: 0 16px 0 44px;
            border: 1.5px solid var(--line);
            border-radius: 16px;
            background: #fff;
            font: inherit; font-size: 16px; font-weight: 500;
            color: var(--ink);
            outline: 0;
            -webkit-appearance: none; appearance: none;
            transition: border-color .2s, box-shadow .2s, background .2s;
        }
        .rg-in::placeholder { color: #9aa7ba; font-weight: 400; }
        .rg-in:hover { border-color: #d2dae5; }
        .rg-in:focus { border-color: var(--em); box-shadow: 0 0 0 4px rgba(16, 185, 129, .14); }
        .rg-in:focus + .ic { color: var(--em-d); }

        /* Specific styles for textarea */
        textarea.rg-in {
            height: auto;
            min-height: 100px;
            padding: 16px 16px 16px 44px;
            resize: vertical;
            line-height: 1.5;
        }

        input[type="date"].rg-in { text-align: left; min-height: 52px; }
        input[type="date"].rg-in::-webkit-date-and-time-value { text-align: left; }

        select.rg-in {
            padding-right: 44px;
            cursor: pointer;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.4' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 16px;
        }
        select.rg-in:required:invalid { color: #9aa7ba; }
        select.rg-in option { color: var(--ink); }
        select.rg-in:disabled { background-color: var(--soft); color: #b4bfcd; cursor: not-allowed; }

        .rg-eye {
            position: absolute; right: 6px; top: 50%;
            transform: translateY(-50%);
            width: 44px; height: 44px;
            border: 0; border-radius: 12px;
            background: none; color: var(--faint);
            cursor: pointer;
        }
        .rg-eye:hover { color: var(--ink); }
        .rg-in.has-eye { padding-right: 50px; }

        .rg-photo { display: flex; flex-direction: column; align-items: center; gap: 18px; text-align: center; }
        @media (min-width: 640px) { .rg-photo { flex-direction: row; text-align: left; gap: 24px; } }

        .rg-avatar { position: relative; flex: none; width: 112px; height: 112px; }
        .rg-avatar-ph, .rg-avatar img {
            width: 100%; height: 100%;
            border-radius: 50%;
        }
        .rg-avatar-ph {
            display: flex; align-items: center; justify-content: center;
            font-size: 40px; color: #b8c3d1;
            background: var(--soft);
            border: 2px dashed #cdd6e2;
        }
        .rg-avatar img {
            display: block;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 0 0 1px var(--line), 0 16px 30px -14px rgba(15, 23, 42, .45);
        }

        .rg-photo-actions { display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; }
        @media (min-width: 640px) { .rg-photo-actions { justify-content: flex-start; } }
        .rg-pick {
            display: inline-flex; align-items: center; gap: 9px;
            height: 46px; padding: 0 20px;
            border: 0; border-radius: 14px;
            font: inherit; font-size: 13.5px; font-weight: 700;
            color: #fff; background: var(--ink);
            cursor: pointer;
            transition: transform .2s, background .2s;
        }
        .rg-pick:hover { background: #1e293b; transform: translateY(-1px); }
        .rg-pick:active { transform: scale(.97); }
        .rg-remove {
            height: 46px; padding: 0 16px;
            border: 1.5px solid var(--line); border-radius: 14px;
            font: inherit; font-size: 13.5px; font-weight: 700;
            color: var(--muted); background: #fff;
            cursor: pointer;
        }
        .rg-remove:hover { color: var(--rose); border-color: #fecdd3; }
        .rg-hint { margin-top: 12px; font-size: 12.5px; line-height: 1.5; color: var(--faint); }
        .rg-hint.err { color: var(--rose); font-weight: 600; }
        .rg-file { position: absolute; width: 1px; height: 1px; opacity: 0; pointer-events: none; }

        .rg-preview {
            display: flex; align-items: flex-start; gap: 12px;
            margin-top: 22px;
            padding: 14px 16px;
            border-radius: 16px;
            background: var(--soft);
            border: 1px dashed #d3dbe7;
            font-size: 13px; line-height: 1.55; color: var(--muted);
        }
        .rg-preview i { margin-top: 3px; color: var(--faint); }
        .rg-preview b { display: block; margin-top: 2px; font-weight: 700; color: var(--ink); }

        .rg-chips { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        @media (min-width: 560px) { .rg-chips { grid-template-columns: repeat(3, minmax(0, 1fr)); } }

        .rg-chip {
            position: relative;
            display: flex; align-items: center; gap: 12px;
            min-height: 58px;
            padding: 12px 14px;
            border: 1.5px solid var(--line);
            border-radius: 18px;
            background: #fff;
            font-size: 13.5px; font-weight: 700; color: var(--text);
            cursor: pointer;
            transition: border-color .2s, background .2s, transform .2s;
        }
        .rg-chip:hover { border-color: #cdd6e2; }
        .rg-chip:active { transform: scale(.98); }
        .rg-chip input { position: absolute; opacity: 0; pointer-events: none; }
        .rg-box {
            flex: none;
            width: 24px; height: 24px;
            display: flex; align-items: center; justify-content: center;
            border: 1.5px solid #cbd5e1; border-radius: 8px;
            font-size: 11px; color: transparent;
            background: #fff;
            transition: all .2s;
        }
        .rg-chip input:checked + .rg-box { color: #fff; background: var(--em); border-color: var(--em); }
        .rg-chip input:focus-visible + .rg-box { outline: 3px solid rgba(16, 185, 129, .35); outline-offset: 2px; }
        .rg-chip:has(input:checked) { border-color: var(--em); background: #f3fcf8; color: var(--ink); }

        .rg-note {
            display: flex; align-items: flex-start; gap: 12px;
            margin-top: 18px;
            padding: 14px 16px;
            border-radius: 18px;
            background: #eff6ff; border: 1px solid #dbeafe;
            font-size: 13px; line-height: 1.55; color: #1e40af;
        }
        .rg-note i { margin-top: 2px; }

        .rg-actions { display: flex; flex-direction: column-reverse; gap: 12px; margin-top: 22px; }
        @media (min-width: 640px) { .rg-actions { flex-direction: row; justify-content: flex-end; } }
        .rg-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 10px;
            height: 54px; padding: 0 28px;
            border: 0; border-radius: 17px;
            font: inherit; font-size: 14.5px; font-weight: 800;
            text-decoration: none; cursor: pointer;
            transition: transform .2s, box-shadow .2s, background .2s, filter .2s;
        }
        .rg-btn--ghost { color: var(--muted); background: #fff; border: 1.5px solid var(--line); }
        .rg-btn--ghost:hover { background: var(--soft); }
        .rg-btn--main {
            color: #fff;
            background: linear-gradient(135deg, #10b981, #059669 60%, #0d9488);
            box-shadow: 0 18px 30px -14px rgba(16, 185, 129, .8), inset 0 1px 0 rgba(255, 255, 255, .25);
        }
        .rg-btn--main:hover { filter: brightness(1.05); transform: translateY(-2px); }
        .rg-btn:active { transform: scale(.98); }
        .rg-btn:disabled { opacity: .7; cursor: wait; transform: none; }
        .rg-btn:focus-visible { outline: 3px solid rgba(16, 185, 129, .4); outline-offset: 3px; }

        .rg-foot { margin-top: 28px; text-align: center; font-size: 12px; color: var(--faint); }
    </style>
</head>
<body>

    <div class="rg-wrap">

        <header class="rg-top">
            <img src="{{ asset('images/brgy_logo.png.jpg') }}" alt="Barangay Bactad East logo" class="rg-logo">
            <p class="rg-brand">Barangay Bactad East</p>
            <h1>Create Resident Account</h1>
            <p>Fill out the form. Your account will be reviewed by the Admin before you can log in.</p>
            <p class="rg-login">Already have an account? <a href="{{ route('login') }}">Log in</a></p>
        </header>

        @if(session('success'))
            <div class="rg-alert rg-alert--ok" role="status">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="rg-alert rg-alert--err" role="alert">
                <i class="fas fa-exclamation-circle"></i>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data" id="registerForm" class="rg-form">
            @csrf

            <!-- 1. Profile Photo -->
            <section class="rg-card">
                <div class="rg-head">
                    <span class="rg-num">1</span>
                    <div>
                        <h2>Profile Photo</h2>
                        <p>Optional — para makilala ka ng barangay</p>
                    </div>
                </div>

                <div class="rg-photo">
                    <div class="rg-avatar">
                        <img id="photoPreview" src="" alt="Photo preview" hidden>
                        <div id="photoPlaceholder" class="rg-avatar-ph"><i class="fas fa-user"></i></div>
                    </div>

                    <div>
                        <div class="rg-photo-actions">
                            <label for="profile_photo" class="rg-pick">
                                <i class="fas fa-camera"></i> <span id="pickLabel">Choose Photo</span>
                            </label>
                            <button type="button" id="removePhoto" class="rg-remove" hidden>
                                <i class="fas fa-trash-alt"></i> Remove
                            </button>
                        </div>
                        <input type="file" name="profile_photo" id="profile_photo" accept="image/*" class="rg-file">

                        <p class="rg-hint" id="photoHint">JPG, PNG, WEBP • Max 2MB (Optional)</p>
                    </div>
                </div>
            </section>

            <!-- 2. Personal Information -->
            <section class="rg-card">
                <div class="rg-head">
                    <span class="rg-num">2</span>
                    <div>
                        <h2>Personal Information</h2>
                        <p>Ang iyong pangunahing detalye</p>
                    </div>
                </div>

                <div class="rg-grid">

                    <div>
                        <label class="rg-lbl" for="email">Email <span class="req">*</span></label>
                        <div class="rg-field">
                            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="juan@example.com" required class="rg-in" autocomplete="email">
                            <i class="fas fa-envelope ic"></i>
                        </div>
                    </div>

                    <div>
                        <label class="rg-lbl" for="dob">Date of Birth <span class="req">*</span></label>
                        <div class="rg-field">
                            <input type="date" id="dob" name="date_of_birth" value="{{ old('date_of_birth') }}" required class="rg-in">
                            <i class="fas fa-calendar-alt ic"></i>
                        </div>
                    </div>

                    <div>
                        <label class="rg-lbl" for="first_name">First Name <span class="req">*</span></label>
                        <div class="rg-field">
                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" placeholder="Juan" required class="rg-in" autocomplete="given-name">
                            <i class="fas fa-user ic"></i>
                        </div>
                    </div>

                    <div>
                        <label class="rg-lbl" for="middle_name">Middle Name <span class="opt">(Optional)</span></label>
                        <div class="rg-field">
                            <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name') }}" placeholder="Santos" class="rg-in" autocomplete="additional-name">
                            <i class="fas fa-user ic"></i>
                        </div>
                    </div>

                    <div class="rg-full">
                        <label class="rg-lbl" for="last_name">Last Name <span class="req">*</span></label>
                        <div class="rg-field">
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder="Dela Cruz" required class="rg-in" autocomplete="family-name">
                            <i class="fas fa-user ic"></i>
                        </div>
                    </div>

                    <div>
                        <label class="rg-lbl" for="gender">Gender <span class="req">*</span></label>
                        <div class="rg-field">
                            <select id="gender" name="gender" required class="rg-in">
                                <option value="" disabled selected>Select Gender</option>
                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                            <i class="fas fa-venus-mars ic"></i>
                        </div>
                    </div>

                    <div>
                        <label class="rg-lbl" for="civil_status">Civil Status <span class="req">*</span></label>
                        <div class="rg-field">
                            <select id="civil_status" name="civil_status" required class="rg-in">
                                <option value="" disabled selected>Select Status</option>
                                <option value="Single" {{ old('civil_status') == 'Single' ? 'selected' : '' }}>Single</option>
                                <option value="Married" {{ old('civil_status') == 'Married' ? 'selected' : '' }}>Married</option>
                                <option value="Widowed" {{ old('civil_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                <option value="Separated" {{ old('civil_status') == 'Separated' ? 'selected' : '' }}>Separated</option>
                            </select>
                            <i class="fas fa-ring ic"></i>
                        </div>
                    </div>

                    <div>
                        <label class="rg-lbl" for="contact_number">Contact Number</label>
                        <div class="rg-field">
                            <input type="tel" id="contact_number" name="contact_number" value="{{ old('contact_number') }}" placeholder="09XX XXX XXXX" class="rg-in" autocomplete="tel">
                            <i class="fas fa-phone-alt ic"></i>
                        </div>
                    </div>

                    <div>
                        <label class="rg-lbl" for="occupation">Occupation</label>
                        <div class="rg-field">
                            <input type="text" id="occupation" name="occupation" value="{{ old('occupation') }}" placeholder="e.g., Farmer" class="rg-in" autocomplete="organization-title" data-lpignore="true" data-1p-ignore="true">
                            <i class="fas fa-briefcase ic"></i>
                        </div>
                    </div>

                    <div class="rg-full">
                        <label class="rg-lbl" for="zone_purok">Zone / Purok <span class="req">*</span></label>
                        <div class="rg-field">
                            <select id="zone_purok" name="zone_purok" required class="rg-in">
                                <option value="" disabled selected>Select Zone</option>
                                @for ($i = 1; $i <= 7; $i++)
                                    <option value="Zone {{ $i }}" {{ old('zone_purok') == "Zone $i" ? 'selected' : '' }}>Zone {{ $i }}</option>
                                @endfor
                            </select>
                            <i class="fas fa-map-pin ic"></i>
                        </div>
                    </div>

                </div>
            </section>

            <!-- 3. Address -->
            <section class="rg-card">
                <div class="rg-head">
                    <span class="rg-num">3</span>
                    <div>
                        <h2>Address Details</h2>
                        <p>Isulat ang iyong buong address</p>
                    </div>
                </div>

                <div>
                    <label class="rg-lbl" for="complete_address">Complete Address <span class="req">*</span></label>
                    <div class="rg-field">
                        <textarea id="complete_address" name="complete_address" rows="3" required placeholder="House No., Street, Purok/Zone, Barangay Bactad East, Urdaneta City, Pangasinan" class="rg-in">{{ old('complete_address') }}</textarea>
                        <i class="fas fa-map-marker-alt ic" style="top: 24px; transform: none;"></i>
                    </div>
                    <p class="rg-hint">Pakisulat nang kumpleto para madaling mahanap.</p>
                </div>
            </section>

            <!-- 4. Valid ID Verification -->
            <section class="rg-card">
                <div class="rg-head">
                    <span class="rg-num">4</span>
                    <div>
                        <h2>Identity Verification</h2>
                        <p>Mag-upload ng valid ID para ma-verify</p>
                    </div>
                </div>

                <div class="rg-grid">
                    <div class="rg-full">
                        <label class="rg-lbl" for="id_type">Type of Valid ID <span class="req">*</span></label>
                        <div class="rg-field">
                            <select id="id_type" name="id_type" required class="rg-in">
                                <option value="" disabled selected>Select ID type...</option>
                                <optgroup label="Government-Issued IDs">
                                    <option value="Philippine National ID (PhilSys)" {{ old('id_type') == 'Philippine National ID (PhilSys)' ? 'selected' : '' }}>Philippine National ID (PhilSys)</option>
                                    <option value="Driver's License" {{ old('id_type') == "Driver's License" ? 'selected' : '' }}>Driver's License</option>
                                    <option value="Philippine Passport" {{ old('id_type') == 'Philippine Passport' ? 'selected' : '' }}>Philippine Passport</option>
                                    <option value="UMID" {{ old('id_type') == 'UMID' ? 'selected' : '' }}>UMID</option>
                                    <option value="Voter's ID" {{ old('id_type') == "Voter's ID" ? 'selected' : '' }}>Voter's ID</option>
                                    <option value="PRC ID" {{ old('id_type') == 'PRC ID' ? 'selected' : '' }}>PRC ID</option>
                                    <option value="Postal ID" {{ old('id_type') == 'Postal ID' ? 'selected' : '' }}>Postal ID</option>
                                    <option value="PhilHealth ID" {{ old('id_type') == 'PhilHealth ID' ? 'selected' : '' }}>PhilHealth ID</option>
                                    <option value="Pag-IBIG ID" {{ old('id_type') == 'Pag-IBIG ID' ? 'selected' : '' }}>Pag-IBIG ID</option>
                                    <option value="TIN ID" {{ old('id_type') == 'TIN ID' ? 'selected' : '' }}>TIN ID</option>
                                </optgroup>
                                <optgroup label="Sectoral / Special IDs">
                                    <option value="Senior Citizen ID" {{ old('id_type') == 'Senior Citizen ID' ? 'selected' : '' }}>Senior Citizen ID</option>
                                    <option value="PWD ID" {{ old('id_type') == 'PWD ID' ? 'selected' : '' }}>PWD ID</option>
                                    <option value="Solo Parent ID" {{ old('id_type') == 'Solo Parent ID' ? 'selected' : '' }}>Solo Parent ID</option>
                                    <option value="Barangay ID" {{ old('id_type') == 'Barangay ID' ? 'selected' : '' }}>Barangay ID</option>
                                    <option value="OFW ID" {{ old('id_type') == 'OFW ID' ? 'selected' : '' }}>OFW ID</option>
                                </optgroup>
                                <optgroup label="Other IDs">
                                    <option value="School ID" {{ old('id_type') == 'School ID' ? 'selected' : '' }}>School ID</option>
                                    <option value="Company ID" {{ old('id_type') == 'Company ID' ? 'selected' : '' }}>Company ID</option>
                                    <option value="NBI Clearance" {{ old('id_type') == 'NBI Clearance' ? 'selected' : '' }}>NBI Clearance</option>
                                    <option value="Police Clearance" {{ old('id_type') == 'Police Clearance' ? 'selected' : '' }}>Police Clearance</option>
                                    <option value="Other" {{ old('id_type') == 'Other' ? 'selected' : '' }}>Other</option>
                                </optgroup>
                            </select>
                            <i class="fas fa-id-card ic"></i>
                        </div>
                    </div>

                    <div class="rg-full">
                        <label class="rg-lbl" for="valid_id">Upload Valid ID <span class="req">*</span></label>
                        <div class="rg-field">
                            <input type="file" id="valid_id" name="valid_id" accept="image/*" capture="environment" required class="rg-in" style="padding-top: 14px; height: auto;">
                        </div>
                        <p class="rg-hint">
                            📱 On mobile: Opens camera. <br>
                            💻 On PC: Upload an image file.
                        </p>
                    </div>
                </div>
            </section>

            <!-- 5. Account Security -->
            <section class="rg-card">
                <div class="rg-head">
                    <span class="rg-num">5</span>
                    <div>
                        <h2>Account Security</h2>
                        <p>Gumawa ng matibay na password</p>
                    </div>
                </div>

                <div class="rg-grid">
                    <div>
                        <label class="rg-lbl" for="password">Password <span class="req">*</span></label>
                        <div class="rg-field">
                            <input type="password" id="password" name="password" placeholder="Min 8 chars" required class="rg-in has-eye" autocomplete="new-password">
                            <i class="fas fa-key ic"></i>
                            <button type="button" class="rg-eye" data-target="password" aria-label="Show password"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                    <div>
                        <label class="rg-lbl" for="password_confirmation">Confirm Password <span class="req">*</span></label>
                        <div class="rg-field">
                            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Re-enter" required class="rg-in has-eye" autocomplete="new-password">
                            <i class="fas fa-check-circle ic"></i>
                            <button type="button" class="rg-eye" data-target="password_confirmation" aria-label="Show password"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 6. Classifications -->
            <section class="rg-card">
                <div class="rg-head">
                    <span class="rg-num">6</span>
                    <div>
                        <h2>Resident Classifications</h2>
                        <p>Lagyan ng tsek ang mga naaangkop sa iyo</p>
                    </div>
                </div>

                <div class="rg-chips">
                    <label class="rg-chip">
                        <input type="checkbox" name="is_senior" value="1" {{ old('is_senior') ? 'checked' : '' }}>
                        <span class="rg-box"><i class="fas fa-check"></i></span>
                        <span>Senior</span>
                    </label>
                    <label class="rg-chip">
                        <input type="checkbox" name="is_4ps" value="1" {{ old('is_4ps') ? 'checked' : '' }}>
                        <span class="rg-box"><i class="fas fa-check"></i></span>
                        <span>4Ps</span>
                    </label>
                    <label class="rg-chip">
                        <input type="checkbox" name="is_pwd" value="1" {{ old('is_pwd') ? 'checked' : '' }}>
                        <span class="rg-box"><i class="fas fa-check"></i></span>
                        <span>PWD</span>
                    </label>
                    <label class="rg-chip">
                        <input type="checkbox" name="is_solo_parent" value="1" {{ old('is_solo_parent') ? 'checked' : '' }}>
                        <span class="rg-box"><i class="fas fa-check"></i></span>
                        <span>Solo Parent</span>
                    </label>
                    <label class="rg-chip">
                        <input type="checkbox" name="is_voter" value="1" {{ old('is_voter') ? 'checked' : '' }}>
                        <span class="rg-box"><i class="fas fa-check"></i></span>
                        <span>Voter</span>
                    </label>
                </div>
            </section>

            <div class="rg-note">
                <i class="fas fa-info-circle"></i>
                <span>Ire-review muna ng Admin ang iyong account bago ka makapag-log in.</span>
            </div>

            <div class="rg-actions">
                <a href="{{ route('login') }}" class="rg-btn rg-btn--ghost">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" id="submitBtn" class="rg-btn rg-btn--main">
                    <i class="fas fa-user-plus"></i> Register Account
                </button>
            </div>
        </form>

        <p class="rg-foot">Barangay Information Management System · Bactad East</p>
    </div>

    <script>
        (function () {
            const input       = document.getElementById('profile_photo');
            const preview     = document.getElementById('photoPreview');
            const placeholder = document.getElementById('photoPlaceholder');
            const removeBtn   = document.getElementById('removePhoto');
            const pickLabel   = document.getElementById('pickLabel');
            const hint        = document.getElementById('photoHint');
            const HINT_TEXT   = 'JPG, PNG, WEBP • Max 2MB (Optional)';
            const MAX_BYTES   = 2 * 1024 * 1024;

            function resetPhoto() {
                input.value = '';
                preview.removeAttribute('src');
                preview.hidden = true;
                placeholder.hidden = false;
                removeBtn.hidden = true;
                pickLabel.textContent = 'Choose Photo';
            }

            input.addEventListener('change', function (e) {
                const file = e.target.files[0];
                hint.classList.remove('err');
                hint.textContent = HINT_TEXT;

                if (!file) { resetPhoto(); return; }

                if (file.size > MAX_BYTES) {
                    resetPhoto();
                    hint.classList.add('err');
                    hint.textContent = 'Masyadong malaki ang file. Max 2MB lang po.';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (event) {
                    preview.src = event.target.result;
                    preview.hidden = false;
                    placeholder.hidden = true;
                    removeBtn.hidden = false;
                    pickLabel.textContent = 'Change Photo';
                };
                reader.readAsDataURL(file);
            });

            removeBtn.addEventListener('click', function () {
                resetPhoto();
                hint.classList.remove('err');
                hint.textContent = HINT_TEXT;
            });

            document.querySelectorAll('.rg-eye').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const field = document.getElementById(btn.dataset.target);
                    const show  = field.type === 'password';
                    field.type = show ? 'text' : 'password';
                    btn.innerHTML = show ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
                    btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
                });
            });

            const form = document.getElementById('registerForm');
            const btn  = document.getElementById('submitBtn');
            const btnHTML = btn.innerHTML;
            form.addEventListener('submit', function () {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registering...';
            });
            window.addEventListener('pageshow', function () {
                btn.disabled = false;
                btn.innerHTML = btnHTML;
            });
        })();
    </script>
</body>
</html>
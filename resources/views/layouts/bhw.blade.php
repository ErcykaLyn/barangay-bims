<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BHW Panel — Barangay Information Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        .custom-scroll::-webkit-scrollbar { width: 4px; }
        .custom-scroll::-webkit-scrollbar-track { background: #065f46; border-radius: 10px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #34d399; border-radius: 10px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #6ee7b7; }
        .card-hover { transition: all 0.2s ease; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="bg-slate-50">
    <div class="flex h-screen overflow-hidden">
        @include('layouts.bhw_sidebar')
        <main class="flex-1 overflow-y-auto p-6 custom-scroll">
            @yield('content')
        </main>
    </div>
</body>
</html>
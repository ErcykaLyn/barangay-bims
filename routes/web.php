<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Admin\ResidentController as AdminResident;
use App\Http\Controllers\Admin\RegisteredAccountsController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\ServiceRequestController as AdminRequest;
use App\Http\Controllers\Admin\DocumentPrintController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PasswordResetAdminController;
use App\Http\Controllers\Residents\ServiceRequestController as ResidentRequest;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncement;
use App\Http\Controllers\Residents\AnnouncementController as ResidentAnnouncement;
use App\Http\Controllers\Residents\ChatbotController;
use App\Http\Controllers\Residents\EquipmentController as ResidentEquipment;
use App\Http\Controllers\BHW\DashboardController as BHWDashboard;
use App\Http\Controllers\BHW\ResidentController as BHWResident;
use App\Http\Controllers\BHW\DeceasedController as BHWDeceased;
use App\Http\Controllers\BHW\SeniorController as BHWSenior;
use App\Http\Controllers\BHW\PwdController as BHWPwd;
use App\Http\Controllers\BHW\FourPsController as BHWFourPs;
use App\Http\Controllers\BHW\FamilyProfilingController;
use App\Http\Controllers\PublicAnnouncementController;

// UPDATED: Kunin ang live count ng residents para sa landing page
Route::get('/', function () {
    $totalResidents = \App\Models\Resident::where('role', 'resident')->count();
    return view('welcome', compact('totalResidents'));
})->name('welcome');

// UPDATED: Kunin ang live count ng residents para sa landing page
Route::get('/home', function () {
    $totalResidents = \App\Models\Resident::where('role', 'resident')->count();
    return view('landing', compact('totalResidents'));
})->name('landing');

// Public Chatbot Route (may throttle para iwas spam)
Route::post('/chatbot/ask', [ChatbotController::class, 'ask'])
    ->middleware('throttle:15,1')
    ->name('public.chatbot.ask');

// Public Announcements Routes
Route::get('/announcements', [PublicAnnouncementController::class, 'index'])
    ->name('public.announcements.index');

Route::get('/announcements/{announcement}/{slug?}', [PublicAnnouncementController::class, 'show'])
    ->whereNumber('announcement')
    ->name('public.announcements.show');
// END OF PUBLIC ANNOUNCEMENTS

// BAGONG DAGDAG: Public Ordinances at Hotlines
Route::get('/ordinances', function () {
    return view('public.ordinances');
})->name('public.ordinances');

Route::get('/hotlines', function () {
    return view('public.hotlines');
})->name('public.hotlines');
// END OF PUBLIC ORDINANCES & HOTLINES

Route::get('/login', [LoginController::class, 'showResidentLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

Route::get('/admin/login', [LoginController::class, 'showAdminLoginForm'])->name('admin.login');

Route::get('/bhw/login', [LoginController::class, 'showBhwLoginForm'])->name('bhw.login');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/password/request', [PasswordResetController::class, 'showRequestForm'])
    ->name('password.request');
Route::post('/password/request', [PasswordResetController::class, 'storeRequest'])
    ->name('password.request.store');

Route::get('/password/request/status', [PasswordResetController::class, 'showStatus'])
    ->name('password.request.status');
Route::post('/password/request/check', [PasswordResetController::class, 'checkStatus'])
    ->name('password.request.check');

Route::get('/password/reset/{request_id}', [PasswordResetController::class, 'showResetForm'])
    ->name('password.reset.form');
Route::post('/password/reset/{request_id}', [PasswordResetController::class, 'resetPassword'])
    ->name('password.reset.submit');

Route::middleware(['auth:web'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminResident::class, 'dashboard'])->name('dashboard');

    Route::controller(AdminResident::class)->group(function () {
        Route::get('/residents', 'index')->name('residents.index');

        // BAGONG DAGDAG: Create at Store routes para sa Admin
        Route::get('/residents/create', 'create')->name('residents.create');
        Route::post('/residents', 'store')->name('residents.store');

        // BAGONG DAGDAG: Deceased Residents Route
        Route::get('/residents/deceased', 'deceased')->name('residents.deceased');

        // Pending Accounts Routes
        Route::get('/residents/pending', 'pending')->name('residents.pending');
        Route::post('/residents/bulk-approve', 'bulkApprove')->name('residents.bulk-approve');
        Route::post('/residents/bulk-reject', 'bulkReject')->name('residents.bulk-reject');
        Route::post('/residents/{id}/approve', 'approve')->name('residents.approve');
        Route::post('/residents/{id}/reject', 'reject')->name('residents.reject');

        Route::get('/residents/{id}', 'show')->name('residents.show');
        Route::get('/residents/{id}/edit', 'edit')->name('residents.edit');
        Route::put('/residents/{id}', 'update')->name('residents.update');
        Route::put('/residents/{id}/account', 'updateAccount')->name('residents.account.update');
        Route::delete('/residents/{id}', 'destroy')->name('residents.destroy');
    });

    Route::get('/registered-accounts', [RegisteredAccountsController::class, 'index'])->name('registered-accounts.index');

    Route::resource('users', UserController::class)->except(['show', 'edit', 'update']);

    Route::get('/voters', [AdminResident::class, 'voters'])->name('voters.index');

    Route::controller(EquipmentController::class)->group(function () {
        Route::get('/equipment', 'index')->name('equipment.index');
        Route::post('/equipment', 'store')->name('equipment.store');

        Route::get('/equipment/requests', 'requests')->name('equipment.requests');
        Route::post('/equipment/requests/{id}/approve', 'approveRequest')->name('equipment.requests.approve');
        Route::post('/equipment/requests/{id}/reject', 'rejectRequest')->name('equipment.requests.reject');
        Route::post('/equipment/requests/{id}/picked-up', 'markPickedUp')->name('equipment.requests.picked-up');
        Route::post('/equipment/requests/{id}/dispensed', 'markDispensed')->name('equipment.requests.dispensed');
        Route::post('/equipment/requests/{id}/returned', 'markReturned')->name('equipment.requests.returned');

        Route::get('/equipment/{id}/history', 'history')->name('equipment.history');
        Route::post('/equipment/{id}/borrow', 'borrow')->name('equipment.borrow');
        Route::post('/equipment/{id}/return', 'returnStock')->name('equipment.return');
        Route::post('/equipment/{id}/damage', 'markDamaged')->name('equipment.damage');
        Route::post('/equipment/{id}/restock', 'restock')->name('equipment.restock');

        Route::patch('/equipment/{id}/status', 'updateStatus')->name('equipment.update-status');
        Route::delete('/equipment/{id}', 'destroy')->name('equipment.destroy');
    });

    Route::prefix('requests')->name('requests.')->group(function () {

        Route::get('/', [AdminRequest::class, 'index'])->name('index');
        Route::get('/logs', [AdminRequest::class, 'logs'])->name('logs');
        Route::get('/pending-count', [AdminRequest::class, 'pendingCount'])->name('pendingCount');
        Route::post('/bulk-update', [AdminRequest::class, 'bulkUpdate'])->name('bulkUpdate');

        Route::get('/{id}/print', [DocumentPrintController::class, 'print'])->name('print');

        Route::post('/{id}/accept', [AdminRequest::class, 'accept'])->name('accept');
        Route::post('/{id}/deny', [AdminRequest::class, 'deny'])->name('deny');
        Route::post('/{id}/ready', [AdminRequest::class, 'markReadyForPickup'])->name('ready');
        Route::post('/{id}/complete', [AdminRequest::class, 'markCompleted'])->name('complete');

        Route::get('/{id}', [AdminRequest::class, 'show'])->name('show');
        Route::patch('/{id}', [AdminRequest::class, 'updateStatus'])->name('update');
        Route::delete('/{id}', [AdminRequest::class, 'destroy'])->name('destroy');
    });

    Route::get('/health', function () {
        return view('admin.health_records');
    })->name('health.index');

    Route::resource('announcements', AdminAnnouncement::class)->except(['show']);

    Route::get('/password-resets', [PasswordResetAdminController::class, 'index'])
        ->name('password-resets.index');
    Route::get('/password-resets/{id}', [PasswordResetAdminController::class, 'show'])
        ->name('password-resets.show');
    Route::post('/password-resets/{id}/approve', [PasswordResetAdminController::class, 'approve'])
        ->name('password-resets.approve');
    Route::post('/password-resets/{id}/reject', [PasswordResetAdminController::class, 'reject'])
        ->name('password-resets.reject');
});

Route::middleware(['auth:web'])->prefix('bhw')->name('bhw.')->group(function () {

    Route::get('/dashboard', [BHWDashboard::class, 'index'])->name('dashboard');

    Route::get('/residents/deceased', [BHWDeceased::class, 'index'])->name('residents.deceased');

    Route::resource('residents', BHWResident::class);
    Route::patch('/residents/{id}/mark-deceased', [BHWResident::class, 'markDeceased'])->name('residents.mark-deceased');

    // BAGONG ROUTE PARA SA PAGGawa NG HEAD OF THE FAMILY
    Route::post('/residents/{id}/make-head', [BHWResident::class, 'makeAsHead'])->name('residents.make-head');

    Route::controller(FamilyProfilingController::class)->prefix('family-profiling')->name('family_profiling.')->group(function () {
        Route::get('/export', 'export')->name('export');
        Route::get('/{family}/search-residents', 'searchResidents')->name('searchResidents');
        Route::post('/{family}/attach-resident', 'attachResident')->name('attachResident');
        Route::delete('/{family}/detach-resident/{resident}', 'detachResident')->name('detachResident');

        Route::get('/import/template', 'downloadTemplate')->name('template');
        Route::post('/import', 'import')->name('import');

        // BAGONG ROUTE PARA SA AUTO-GENERATE NG FAMILY CODE
        Route::get('/next-code', 'getNextCode')->name('next-code');
    });

    Route::resource('family-profiling', FamilyProfilingController::class)->names('family_profiling');

    Route::get('/seniors', [BHWSenior::class, 'index'])->name('seniors.index');
    Route::get('/pwds', [BHWPwd::class, 'index'])->name('pwds.index');
    Route::get('/4ps', [BHWFourPs::class, 'index'])->name('4ps.index');
});

// IDINAGDAG NATIN ANG 'resident.alive' DITO
Route::middleware(['auth:resident', 'resident.alive'])->prefix('resident')->name('resident.')->group(function () {

    Route::get('/dashboard', [ResidentRequest::class, 'dashboard'])->name('dashboard');

    Route::controller(ResidentRequest::class)->group(function () {
        Route::get('/request-form', 'create')->name('requests.form');
        Route::post('/request-form', 'store')->name('requests.store');
        Route::get('/status', 'index')->name('requests.status');
        Route::get('/inventory', 'inventory')->name('inventory');
    });

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');

    // FIXED: tumatanggap na ng PUT at POST para hindi na mag-405 (MethodNotAllowed)
    Route::match(['put', 'post'], '/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::controller(ResidentEquipment::class)->prefix('equipment')->name('equipment.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/my-requests', 'myRequests')->name('requests');
        Route::get('/{id}/request', 'showRequestForm')->name('request.form');
        Route::post('/{id}/request', 'submitRequest')->name('request.submit');
    });

    Route::get('/announcements', [ResidentAnnouncement::class, 'index'])->name('announcements');

    // INALIS KO NA ITO DAHIL NASA PUBLIC NA SILA
    // Route::get('/ordinances', function () {
    //     return view('resident.ordinances');
    // })->name('ordinances');

    // Route::get('/hotlines', function () {
    //     return view('resident.hotlines');
    // })->name('hotlines');

    // Resident Chatbot Routes (may throttle din)
    Route::controller(ChatbotController::class)->group(function () {
        Route::get('/chatbot', 'index')->name('chatbot');
        Route::post('/chatbot/send', 'ask')
            ->middleware('throttle:20,1')
            ->name('chatbot.send');
    });
});
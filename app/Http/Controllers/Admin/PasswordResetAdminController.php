<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetRequest;
use App\Mail\PasswordResetApprovedMail;  // <-- IDINAGDAG
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;      // <-- IDINAGDAG

class PasswordResetAdminController extends Controller
{
    /**
     * List all password reset requests.
     */
    public function index()
    {
        $requests = PasswordResetRequest::with('resident')
            ->latest()
            ->paginate(20);

        $pendingCount = PasswordResetRequest::where('status', 'pending')->count();

        return view('admin.password-reset.index', compact('requests', 'pendingCount'));
    }

    /**
     * Show specific request details.
     */
    public function show($id)
    {
        $resetRequest = PasswordResetRequest::with('resident', 'approvedBy')->findOrFail($id);
        return view('admin.password-reset.show', compact('resetRequest'));
    }

    /**
     * Approve a request.
     */
    public function approve(Request $request, $id)
    {
        $resetRequest = PasswordResetRequest::findOrFail($id);

        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $resetRequest->status = 'approved';
        $resetRequest->admin_notes = $validated['admin_notes'] ?? null;
        $resetRequest->approved_by = Auth::id();
        $resetRequest->approved_at = now();
        $resetRequest->save();

        Log::info('Password reset request approved by admin: ' . Auth::user()->name . ' for: ' . $resetRequest->username);

        // MAG-SEND NG EMAIL KUNG MAY RESIDENT AT EMAIL ADDRESS
        $resident = $resetRequest->resident;
        if ($resident && $resident->email) {
            try {
                Mail::to($resident->email)->send(new PasswordResetApprovedMail($resident, $resetRequest));
            } catch (\Exception $e) {
                Log::error('Failed to send password reset approval email to ' . $resident->email . ': ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Na-approve na ang request. Pwede nang mag-reset ng password ang resident. An email notification has been sent.');
    }

    /**
     * Reject a request.
     */
    public function reject(Request $request, $id)
    {
        $resetRequest = PasswordResetRequest::findOrFail($id);

        $validated = $request->validate([
            'admin_notes' => 'required|string|max:500',
        ]);

        $resetRequest->status = 'rejected';
        $resetRequest->admin_notes = $validated['admin_notes'];
        $resetRequest->rejected_at = now();
        $resetRequest->save();

        Log::info('Password reset request rejected by admin: ' . Auth::user()->name . ' for: ' . $resetRequest->username);

        return back()->with('success', 'Na-reject na ang request.');
    }
}
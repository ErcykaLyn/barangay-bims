<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ServiceRequestController extends Controller
{
    /**
     * ============================================================
     * INDEX — Display all service requests with filters & stats.
     * ============================================================
     */
    public function index(Request $request)
    {
        // Update admin's last viewed timestamp
        $admin = Auth::user();
        if ($admin && $admin->role === 'admin') {
            $admin->update(['last_viewed_requests_at' => now()]);
        }

        // Start query with resident eager-loaded
        $query = ServiceRequest::with('resident');

        // ==================== FILTERS ====================

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by document type
        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        // Search by resident name, username, purpose, or request ID
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                  ->orWhere('document_type', 'LIKE', "%{$search}%")
                  ->orWhere('purpose', 'LIKE', "%{$search}%")
                  ->orWhereHas('resident', function ($r) use ($search) {
                      $r->where('first_name', 'LIKE', "%{$search}%")
                        ->orWhere('last_name', 'LIKE', "%{$search}%")
                        ->orWhere('username', 'LIKE', "%{$search}%");
                  });
            });
        }

        // ==================== SORTING ====================
        // ✅ Bagong order: pending → accepted → ready_for_pickup → completed → denied
        $requests = $query->orderByRaw("
            CASE status
                WHEN 'pending' THEN 1
                WHEN 'accepted' THEN 2
                WHEN 'ready_for_pickup' THEN 3
                WHEN 'completed' THEN 4
                WHEN 'denied' THEN 5
                ELSE 6
            END
        ")
        ->orderByDesc('requested_at')
        ->orderByDesc('created_at')
        ->get();

        // ==================== STATS ====================
        // ✅ Idinagdag ang 'accepted' sa stats
        $stats = [
            'total'      => ServiceRequest::count(),
            'pending'    => ServiceRequest::where('status', 'pending')->count(),
            'accepted'   => ServiceRequest::where('status', 'accepted')->count(),
            'ready'      => ServiceRequest::where('status', 'ready_for_pickup')->count(),
            'completed'  => ServiceRequest::where('status', 'completed')->count(),
            'denied'     => ServiceRequest::where('status', 'denied')->count(),
        ];

        return view('admin.requests.index', compact('requests', 'stats'));
    }

    /**
     * ============================================================
     * SHOW — Display a specific service request.
     * ============================================================
     */
    public function show($id)
    {
        $serviceRequest = ServiceRequest::with('resident')->findOrFail($id);

        return view('admin.requests.show', compact('serviceRequest'));
    }

    /**
     * ============================================================
     * ✅ ACCEPT — Accept a pending request.
     * ============================================================
     */
    public function accept($id)
    {
        try {
            $service = ServiceRequest::findOrFail($id);

            if ($service->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending requests can be accepted.');
            }

            $service->status = 'accepted';
            $service->accepted_at = now();
            $service->save();

            return redirect()->back()->with(
                'success',
                "✅ Request {$service->reference_number} has been ACCEPTED. You can now print the document."
            );

        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Request not found.');
        } catch (\Exception $e) {
            Log::error('Accept failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while accepting.');
        }
    }

    /**
     * ============================================================
     * ❌ DENY — Deny a pending request.
     * ============================================================
     */
    public function deny(Request $request, $id)
    {
        try {
            $service = ServiceRequest::findOrFail($id);

            if ($service->status === 'completed') {
                return redirect()->back()->with('error', '🔒 Completed requests cannot be denied.');
            }

            $service->status = 'denied';
            $service->denied_at = now();
            $service->processed_at = now();
            $service->denial_reason = $request->input('denial_reason');
            $service->save();

            return redirect()->back()->with(
                'success',
                "❌ Request {$service->reference_number} has been DENIED."
            );

        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Request not found.');
        } catch (\Exception $e) {
            Log::error('Deny failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while denying.');
        }
    }

    /**
     * ============================================================
     * ✅ MARK READY FOR PICKUP — After printing, notify resident.
     * ============================================================
     */
    public function markReadyForPickup($id)
    {
        try {
            $service = ServiceRequest::findOrFail($id);

            if (!in_array($service->status, ['accepted', 'pending'])) {
                return redirect()->back()->with(
                    'error',
                    'Only accepted requests can be marked as ready for pickup.'
                );
            }

            $service->status = 'ready_for_pickup';
            $service->ready_at = now();
            $service->processed_at = now();
            $service->save();

            // 🔔 NOTIFICATION: Send to resident (kung may notification system)
            // $service->resident->notify(new \App\Notifications\DocumentReadyNotification($service));

            return redirect()->back()->with(
                'success',
                "📦 Request {$service->reference_number} is now READY FOR PICKUP! Resident has been notified."
            );

        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Request not found.');
        } catch (\Exception $e) {
            Log::error('Mark ready failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while marking ready.');
        }
    }

    /**
     * ============================================================
     * ✅ MARK COMPLETED — After resident claims the document.
     * ============================================================
     */
    public function markCompleted($id)
    {
        try {
            $service = ServiceRequest::findOrFail($id);

            if ($service->status !== 'ready_for_pickup') {
                return redirect()->back()->with(
                    'error',
                    'Only requests that are ready for pickup can be completed.'
                );
            }

            $service->status = 'completed';
            $service->completed_at = now();
            $service->processed_at = now();
            $service->save();

            return redirect()->back()->with(
                'success',
                "✅ Request {$service->reference_number} has been COMPLETED!"
            );

        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Request not found.');
        } catch (\Exception $e) {
            Log::error('Complete failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while completing.');
        }
    }

    /**
     * ============================================================
     * UPDATE STATUS — Manual update (legacy, ginagamit pa rin sa ibang parts).
     * ✅ LOCKED: Hindi na mae-edit kapag COMPLETED na.
     * ============================================================
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,ready_for_pickup,completed,denied',
        ]);

        try {
            $service = ServiceRequest::findOrFail($id);

            // ✅ LOCK CHECK
            if ($service->status === 'completed') {
                return redirect()->back()->with(
                    'error',
                    '🔒 This request is already COMPLETED and cannot be modified.'
                );
            }

            // Skip if status hasn't changed
            if ($service->status === $validated['status']) {
                return redirect()->back()->with(
                    'info',
                    'Status is already set to ' . str_replace('_', ' ', $validated['status']) . '.'
                );
            }

            // Update status
            $service->status = $validated['status'];

            // Auto-manage processed_at timestamp
            if (in_array($validated['status'], ['ready_for_pickup', 'completed', 'denied'])) {
                $service->processed_at = now();
            } elseif (in_array($validated['status'], ['pending', 'accepted'])) {
                $service->processed_at = null;
            }

            // Auto-set completed_at
            if ($validated['status'] === 'completed') {
                $service->completed_at = now();
            }

            $service->save();

            $statusText = str_replace('_', ' ', $validated['status']);

            return redirect()->back()->with(
                'success',
                "✅ Request {$service->reference_number} status updated to {$statusText}."
            );

        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Request not found.');
        } catch (\Exception $e) {
            Log::error('Status update failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while updating the status.');
        }
    }

    /**
     * ============================================================
     * BULK UPDATE — Update multiple requests at once.
     * ============================================================
     */
    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'request_ids'   => 'required|array',
            'request_ids.*' => 'exists:service_requests,id',
            'status'        => 'required|in:pending,accepted,ready_for_pickup,completed,denied',
        ]);

        try {
            $lockedCount = ServiceRequest::whereIn('id', $validated['request_ids'])
                ->where('status', 'completed')
                ->count();

            $processedAt = in_array($validated['status'], ['ready_for_pickup', 'completed', 'denied'])
                ? now()
                : null;

            $completedAt = $validated['status'] === 'completed' ? now() : null;

            $count = ServiceRequest::whereIn('id', $validated['request_ids'])
                ->where('status', '!=', 'completed')
                ->update([
                    'status'       => $validated['status'],
                    'processed_at' => $processedAt,
                    'completed_at' => $completedAt,
                    'updated_at'   => now(),
                ]);

            $statusText = str_replace('_', ' ', $validated['status']);

            $message = "✅ Updated {$count} request(s) to {$statusText}.";
            if ($lockedCount > 0) {
                $message .= " ({$lockedCount} locked request(s) were skipped.)";
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Bulk update failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong during bulk update.');
        }
    }

    /**
     * ============================================================
     * DESTROY — Delete a request from history.
     * ✅ Pwedeng i-delete: completed, denied, accepted.
     * ❌ Hindi pwedeng i-delete: pending (kailangan muna i-process).
     * ============================================================
     */
    public function destroy($id)
    {
        try {
            $service = ServiceRequest::findOrFail($id);

            // ✅ Bagong rule: pwedeng i-delete ang completed, denied, at accepted
            if (!in_array($service->status, ['completed', 'denied', 'accepted'])) {
                return redirect()->back()->with(
                    'error',
                    'Only accepted, completed, or denied requests can be deleted. Pending requests must be processed first.'
                );
            }

            $ref = $service->reference_number ?? $service->id;
            $service->delete();

            return redirect()->route('admin.requests.index')
                ->with('success', "🗑️ Request {$ref} has been deleted from history.");

        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Request not found.');
        } catch (\Exception $e) {
            Log::error('Delete failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while deleting.');
        }
    }

    /**
     * ============================================================
     * PENDING COUNT — JSON endpoint for notification badge.
     * ============================================================
     */
    public function pendingCount()
    {
        $count = ServiceRequest::where('status', 'pending')->count();

        return response()->json(['count' => $count]);
    }

    /**
     * ============================================================
     * LOGS — Display completed & denied requests (read-only).
     * ============================================================
     */
    public function logs(Request $request)
    {
        $query = ServiceRequest::with('resident')
            ->whereIn('status', ['completed', 'denied']);

        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('document_type', 'LIKE', "%{$search}%")
                  ->orWhereHas('resident', function ($r) use ($search) {
                      $r->where('first_name', 'LIKE', "%{$search}%")
                        ->orWhere('last_name', 'LIKE', "%{$search}%")
                        ->orWhere('username', 'LIKE', "%{$search}%");
                  });
            });
        }

        $logs = $query->orderByDesc('processed_at')
            ->orderByDesc('updated_at')
            ->get();

        $logStats = [
            'total'     => $logs->count(),
            'completed' => $logs->where('status', 'completed')->count(),
            'denied'    => $logs->where('status', 'denied')->count(),
        ];

        return view('admin.requests.logs', compact('logs', 'logStats'));
    }
}
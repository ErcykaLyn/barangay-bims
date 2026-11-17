<?php

namespace App\Http\Controllers\Residents;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceRequest;
use App\Models\Equipment; 
use Illuminate\Support\Facades\Auth;

class ServiceRequestController extends Controller
{
    /**
     * ============================================================
     * DASHBOARD — Display the Resident Dashboard.
     * ============================================================
     */
    public function dashboard()
    {
        $requests = ServiceRequest::where('resident_id', Auth::id())
                                    ->latest()
                                    ->get();

        return view('resident.dashboard', compact('requests'));
    }

    /**
     * ============================================================
     * INVENTORY — Display the Barangay Equipment/Inventory Page.
     * ============================================================
     */
    public function inventory()
    {
        $equipments = Equipment::all(); 
        return view('resident.inventory', compact('equipments'));
    }

    /**
     * ============================================================
     * CREATE — Show the form for creating a new document request.
     * ============================================================
     */
    public function create()
    {
        return view('resident.request_form');
    }

    /**
     * ============================================================
     * STORE — Save the new document request.
     * ✅ PURPOSE IS AUTO-GENERATED base sa document type.
     * ✅ Extra fields (business, residency) are OPTIONAL (nullable).
     * ✅ Auto-fills resident info from authenticated resident's profile.
     * ============================================================
     */
    public function store(Request $request)
    {
        // ✅ Validation — WALANG PURPOSE (auto-generated)
        $validated = $request->validate([
            // Required field
            'document_type' => 'required|string|max:255',
            'notes'         => 'nullable|string|max:500',
            
            // Business Clearance fields — OPTIONAL
            'business_name'    => 'nullable|string|max:255',
            'business_owner'   => 'nullable|string|max:255',
            'business_address' => 'nullable|string|max:500',
            'application_type' => 'nullable|string|max:255',
            'ctc_no'           => 'nullable|string|max:50',
            
            // Residency fields — OPTIONAL
            'valid_id'   => 'nullable|string|max:100',
            'id_ref_no'  => 'nullable|string|max:100',
        ]);

        // ✅ Auto-generate PURPOSE base sa document type
        $purposeMap = [
            'Barangay Residency Certificate'    => 'Proof of Residency',
            'Barangay Indigency Certificate'    => 'Financial / Medical Assistance',
            'Barangay Clearance Certification'  => 'Employment / Business Requirement',
            'Good Moral Certificate'            => 'Scholarship / Employment Requirement',
            'Barangay Business Clearance'       => 'Renewal of Barangay Business Permit',
        ];

        $purpose = $purposeMap[$validated['document_type']] ?? 'General Purpose';

        // ✅ Get authenticated resident
        $resident = Auth::guard('resident')->user();

        // ✅ Create the request with auto-filled data
        ServiceRequest::create([
            'resident_id'      => $resident->id,
            'document_type'    => $validated['document_type'],
            'purpose'          => $purpose, // ✅ Auto-generated
            'notes'            => $validated['notes'] ?? null,
            'status'           => 'pending',
            'requested_at'     => now(),
            
            // Business Clearance fields (nullable)
            'business_name'    => $validated['business_name'] ?? null,
            'business_owner'   => $validated['business_owner'] ?? null,
            'business_address' => $validated['business_address'] ?? null,
            'application_type' => $validated['application_type'] ?? null,
            'ctc_no'           => $validated['ctc_no'] ?? null,
            
            // Residency fields (nullable)
            'valid_id'         => $validated['valid_id'] ?? null,
            'id_ref_no'        => $validated['id_ref_no'] ?? null,
        ]);

        return redirect()->route('resident.requests.status')
            ->with('success', '✅ Request submitted successfully! Please wait for admin approval.');
    }

    /**
     * ============================================================
     * INDEX — Display the Request Status/History page.
     * ✅ Marks the last viewed timestamp so badge disappears.
     * ============================================================
     */
    public function index()
    {
        $user = Auth::guard('resident')->user();
        
        // ✅ Mark when the resident last viewed the status page
        if ($user) {
            $user->update(['last_viewed_status_at' => now()]);
        }
        
        $requests = ServiceRequest::where('resident_id', Auth::id())
                                    ->latest()
                                    ->get();

        return view('resident.status', compact('requests'));
    }
}
<?php

namespace App\Http\Controllers\Residents;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipments = Equipment::orderBy('item_name')->get();

        $resident = Auth::guard('resident')->user();
        $myRequests = $resident->equipmentRequests()->with('equipment')->take(5)->get();

        $pendingCount = $resident->equipmentRequests()->where('status', 'pending')->count();
        $approvedCount = $resident->equipmentRequests()->where('status', 'approved')->count();

        return view('resident.equipment.index', compact(
            'equipments', 'myRequests', 'pendingCount', 'approvedCount'
        ));
    }

    public function showRequestForm($id)
    {
        $equipment = Equipment::findOrFail($id);

        if ($equipment->available_stock <= 0) {
            return redirect()->route('resident.equipment.index')
                ->with('error', 'Sorry, this item is currently out of stock.');
        }

        return view('resident.equipment.request', compact('equipment'));
    }

    public function submitRequest(Request $request, $id)
    {
        $equipment = Equipment::findOrFail($id);
        $resident = Auth::guard('resident')->user();

        if ($equipment->available_stock <= 0) {
            return back()->with('error', 'Sorry, this item is currently out of stock.');
        }

        $existing = EquipmentRequest::where('resident_id', $resident->id)
            ->where('equipment_id', $equipment->id)
            ->whereIn('status', ['pending', 'approved', 'picked_up'])
            ->exists();

        if ($existing) {
            return back()->with('error', 'You already have an active request for this item.');
        }

        $validated = $request->validate([
            'quantity'    => 'required|integer|min:1|max:' . $equipment->available_stock,
            'reason'      => 'required|string|max:500',
            'needed_date' => 'nullable|date|after_or_equal:today',
        ]);

        EquipmentRequest::create([
            'equipment_id' => $equipment->id,
            'resident_id'  => $resident->id,
            'quantity'     => $validated['quantity'],
            'reason'       => $validated['reason'],
            'needed_date'  => $validated['needed_date'] ?? null,
            'status'       => 'pending',
        ]);

        return redirect()->route('resident.equipment.requests')
            ->with('success', 'Request submitted! Waiting for admin approval.');
    }

    public function myRequests()
    {
        $resident = Auth::guard('resident')->user();

        $requests = $resident->equipmentRequests()
            ->with('equipment')
            ->latest()
            ->get();

        return view('resident.equipment.requests', compact('requests'));
    }
}
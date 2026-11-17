<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentRequest;
use App\Models\EquipmentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipments = Equipment::latest()->get();

        $totalItems = $equipments->count();
        $totalAvailable = $equipments->sum('available_stock');
        $totalBorrowed = $equipments->sum('borrowed_stock');
        $totalDamaged = $equipments->sum('damaged_stock');

        return view('admin.equipment.index', compact(
            'equipments', 'totalItems', 'totalAvailable', 'totalBorrowed', 'totalDamaged'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_name'    => 'required|string|max:255',
            'category'     => 'required|string',
            'quantity'     => 'required|integer|min:1',
            'unit_price'   => 'nullable|numeric|min:0',
            'description'  => 'nullable|string|max:1000',
            'photo'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('equipment-photos', 'public');
        }

        Equipment::create([
            'item_name'       => $validated['item_name'],
            'photo_path'      => $photoPath,
            'description'     => $validated['description'] ?? null,
            'category'        => $validated['category'],
            'quantity'        => $validated['quantity'],
            'total_stock'     => $validated['quantity'],
            'available_stock' => $validated['quantity'],
            'borrowed_stock'  => 0,
            'damaged_stock'   => 0,
            'unit_price'      => $validated['unit_price'] ?? null,
            'status'          => 'available',
        ]);

        return redirect()->route('admin.equipment.index')
            ->with('success', 'Equipment item added successfully!');
    }

    public function requests(Request $request)
    {
        $query = EquipmentRequest::with(['equipment', 'resident', 'approvedBy'])->latest();

        if ($request->filled('equipment')) {
            $query->where('equipment_id', $request->equipment);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('resident', function ($r) use ($search) {
                    $r->where('first_name', 'LIKE', "%{$search}%")
                      ->orWhere('middle_name', 'LIKE', "%{$search}%")
                      ->orWhere('last_name', 'LIKE', "%{$search}%");
                })->orWhereHas('equipment', function ($e) use ($search) {
                    $e->where('item_name', 'LIKE', "%{$search}%");
                });
            });
        }

        $requests = $query->get();

        $pendingCount    = EquipmentRequest::where('status', 'pending')->count();
        $approvedCount   = EquipmentRequest::where('status', 'approved')->count();
        $pickedUpCount   = EquipmentRequest::where('status', 'picked_up')->count();
        $dispensedCount  = EquipmentRequest::where('status', 'dispensed')->count();
        $returnedCount   = EquipmentRequest::where('status', 'returned')->count();

        return view('admin.equipment.requests', compact(
            'requests', 'pendingCount', 'approvedCount', 'pickedUpCount', 'dispensedCount', 'returnedCount'
        ));
    }

    public function approveRequest(Request $request, $id)
    {
        $req = EquipmentRequest::with('equipment')->findOrFail($id);

        if (!$req->isPending()) {
            return back()->with('error', 'This request is no longer pending.');
        }

        if ($req->equipment->available_stock < $req->quantity) {
            return back()->with('error', 'Not enough stock available to approve.');
        }

        if ($req->is_medicine) {
            $validated = $request->validate([
                'notes' => 'nullable|string|max:500',
            ]);

            $req->update([
                'status'             => 'approved',
                'approved_by'        => Auth::id(),
                'approved_at'        => now(),
                'due_date'           => null,
                'notes'              => $validated['notes'] ?? null,
                'quantity_borrowed'  => $req->quantity,
            ]);

            return back()->with('success', 'Medicine request approved! Resident will be notified to claim it.');
        }

        $validated = $request->validate([
            'due_date' => 'required|date|after:today',
            'notes'    => 'nullable|string|max:500',
        ]);

        $req->update([
            'status'             => 'approved',
            'approved_by'        => Auth::id(),
            'approved_at'        => now(),
            'due_date'           => $validated['due_date'],
            'notes'              => $validated['notes'] ?? null,
            'quantity_borrowed'  => $req->quantity,
        ]);

        return back()->with('success', 'Request approved! Resident will be notified to pick up the item.');
    }

    public function rejectRequest(Request $request, $id)
    {
        $req = EquipmentRequest::findOrFail($id);

        if (!$req->isPending()) {
            return back()->with('error', 'This request is no longer pending.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $req->update([
            'status'           => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'approved_by'      => Auth::id(),
            'approved_at'      => now(),
        ]);

        return back()->with('success', 'Request rejected. Reason recorded.');
    }

    public function markPickedUp($id)
    {
        $req = EquipmentRequest::with('equipment')->findOrFail($id);

        if ($req->is_medicine) {
            return back()->with('error', 'Medicine items must be marked as Dispensed, not Picked Up.');
        }

        if (!$req->isApproved()) {
            return back()->with('error', 'Only approved requests can be marked as picked up.');
        }

        if ($req->equipment->available_stock < $req->quantity) {
            return back()->with('error', 'Not enough stock. Cannot mark as picked up.');
        }

        $equipment = $req->equipment;
        $equipment->decrement('available_stock', $req->quantity);
        $equipment->increment('borrowed_stock', $req->quantity);

        if ($equipment->available_stock <= 0) {
            $equipment->status = 'borrowed';
        }
        $equipment->save();

        $req->update([
            'status'       => 'picked_up',
            'picked_up_at' => now(),
        ]);

        return back()->with('success', 'Item marked as picked up. Stock deducted.');
    }

    public function markReturned(Request $request, $id)
    {
        $request->validate([
            'returned_quantity' => 'required|integer|min:1',
        ]);

        $req = EquipmentRequest::with('equipment')->findOrFail($id);

        if (!$req->isPickedUp() && !$req->isPartialReturn()) {
            return back()->with('error', 'Only picked up or partially returned items can be returned.');
        }

        $borrowed = $req->quantity_borrowed ?? $req->quantity;
        $alreadyReturned = $req->quantity_returned ?? 0;
        $remainingToReturn = $borrowed - $alreadyReturned;
        $returned = (int) $request->returned_quantity;

        if ($returned > $remainingToReturn) {
            return back()->with('error', 'Returned quantity cannot exceed the remaining ' . $remainingToReturn . ' pcs.');
        }

        $totalReturned = $alreadyReturned + $returned;
        $shortage = max(0, $borrowed - $totalReturned);
        $status = $shortage > 0 ? 'partial_return' : 'returned';

        $equipment = $req->equipment;
        $equipment->increment('available_stock', $returned);
        $equipment->decrement('borrowed_stock', $returned);

        if ($equipment->available_stock > 0) {
            $equipment->status = 'available';
        }
        $equipment->save();

        $req->update([
            'status'             => $status,
            'quantity_borrowed'  => $borrowed,
            'quantity_returned'  => $totalReturned,
            'shortage'           => $shortage,
            'returned_at'        => now(),
        ]);

        if ($shortage > 0) {
            return back()->with('warning', "Naibalik ang {$returned} piraso. Kulang pa ng {$shortage} piraso para makumpleto ang {$borrowed} na hiniram.");
        }

        return back()->with('success', "Kumpleto ang naibalik! {$returned} piraso ang naisauli.");
    }

    public function markDispensed($id)
    {
        $req = EquipmentRequest::with('equipment')->findOrFail($id);

        if (!$req->is_medicine) {
            return back()->with('error', 'Only medicine items can be dispensed.');
        }

        if (!$req->isApproved()) {
            return back()->with('error', 'Only approved requests can be dispensed.');
        }

        if ($req->equipment->available_stock < $req->quantity) {
            return back()->with('error', 'Not enough stock. Cannot dispense.');
        }

        $equipment = $req->equipment;
        $equipment->decrement('available_stock', $req->quantity);
        $equipment->decrement('total_stock', $req->quantity);
        $equipment->decrement('quantity', $req->quantity);

        if ($equipment->available_stock <= 0) {
            $equipment->status = 'out_of_stock';
        }
        $equipment->save();

        $req->update([
            'status'       => 'dispensed',
            'dispensed_at' => now(),
            'dispensed_by' => Auth::id(),
        ]);

        return back()->with('success', 'Medicine dispensed. Stock deducted permanently.');
    }

    public function borrow(Request $request, $id)
    {
        $request->validate([
            'quantity'    => 'required|integer|min:1',
            'resident_id' => 'required|exists:residents,id',
            'due_date'    => 'required|date|after:today',
            'notes'       => 'nullable|string',
        ]);

        try {
            $equipment = Equipment::findOrFail($id);
            $equipment->borrow(
                $request->quantity,
                $request->resident_id,
                $request->due_date,
                $request->notes
            );

            return back()->with('success', 'Stock borrowed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function returnStock(Request $request, $id)
    {
        $request->validate([
            'quantity'    => 'required|integer|min:1',
            'resident_id' => 'nullable|exists:residents,id',
            'notes'       => 'nullable|string',
        ]);

        $equipment = Equipment::findOrFail($id);
        $equipment->returnStock(
            $request->quantity,
            $request->resident_id,
            $request->notes
        );

        return back()->with('success', 'Stock returned successfully.');
    }

    public function markDamaged(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'notes'    => 'nullable|string',
        ]);

        $equipment = Equipment::findOrFail($id);
        $equipment->markDamaged($request->quantity, $request->notes);

        return back()->with('success', 'Item marked as damaged.');
    }

    public function restock(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'notes'    => 'nullable|string',
        ]);

        $equipment = Equipment::findOrFail($id);
        $equipment->restock($request->quantity, $request->notes);

        return back()->with('success', 'Item restocked successfully.');
    }

    public function history($id)
    {
        $equipment = Equipment::with('transactions.resident')->findOrFail($id);
        return view('admin.equipment.history', compact('equipment'));
    }

    public function destroy($id)
    {
        $equipment = Equipment::findOrFail($id);

        if ($equipment->photo_path && Storage::disk('public')->exists($equipment->photo_path)) {
            Storage::disk('public')->delete($equipment->photo_path);
        }

        $equipment->delete();

        return back()->with('success', 'Equipment deleted successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $equipment = Equipment::findOrFail($id);
        $equipment->status = $request->status;
        $equipment->save();

        return back()->with('success', 'Status updated.');
    }
}
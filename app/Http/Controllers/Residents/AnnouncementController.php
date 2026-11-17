<?php

namespace App\Http\Controllers\Residents;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index()
    {
        // Get the logged‑in resident
        $resident = Auth::guard('resident')->user();

        // Fetch all active announcements (latest first)
        $announcements = Announcement::active()->latest()->get();

        // Mark all announcements as read for this resident
        if ($resident && $announcements->isNotEmpty()) {
            $announcementIds = $announcements->pluck('id')->toArray();
            $syncData = [];
            foreach ($announcementIds as $id) {
                $syncData[$id] = ['read_at' => now()];
            }
            $resident->readAnnouncements()->syncWithoutDetaching($syncData);
        }

        return view('resident.announcements', compact('announcements'));
    }
}
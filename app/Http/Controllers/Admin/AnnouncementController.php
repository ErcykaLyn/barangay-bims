<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::latest()->get();
        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'published_date' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['published_date'] = $validated['published_date'] ?? Carbon::today();

        Announcement::create($validated);

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement created successfully.');
    }

    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'published_date' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $announcement->update($validated);

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement updated.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return redirect()->route('admin.announcements.index')->with('success', 'Announcement deleted.');
    }
}
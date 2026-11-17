<?php

namespace App\View\Composers;

use App\Models\Announcement;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ResidentSidebarComposer
{
    public function compose(View $view)
    {
        $user = Auth::guard('resident')->user();

        // ==================== UNREAD ANNOUNCEMENTS ====================
        $announcementCount = 0;
        if ($user) {
            $announcementCount = Announcement::whereNotIn('id', function ($query) use ($user) {
                $query->select('announcement_id')
                      ->from('announcement_user')
                      ->where('user_id', $user->id);
            })->count();
        }

        // ==================== REQUEST UPDATES SINCE LAST VIEW ====================
        $requestCount = 0;
        if ($user) {
            $lastViewed = $user->last_viewed_status_at;
            $query = ServiceRequest::where('resident_id', $user->id)
                        ->whereIn('status', ['completed', 'ready_for_pickup']);
            
            // If the resident has never viewed the status page, show badge for all completed/ready requests
            if ($lastViewed) {
                $query->where('updated_at', '>', $lastViewed);
            }
            // else: no filter, so counts all qualifying requests (first-time badge)
            
            $requestCount = $query->count();
        }

        $view->with([
            'announcementCount' => $announcementCount,
            'requestCount'      => $requestCount,
        ]);
    }
}
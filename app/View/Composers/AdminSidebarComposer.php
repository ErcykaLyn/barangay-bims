<?php

namespace App\View\Composers;

use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminSidebarComposer
{
    public function compose(View $view)
    {
        $admin = Auth::user(); // admin uses the 'web' guard
        $newRequestsCount = 0;

        if ($admin && $admin->role === 'admin') {
            $lastViewed = $admin->last_viewed_requests_at;

            // Start query for all service requests
            $query = ServiceRequest::query();

            // If admin has viewed the requests page before, only count requests created after that time
            if ($lastViewed) {
                $query->where('created_at', '>', $lastViewed);
            }
            // else: count all existing requests (first time viewing)

            $newRequestsCount = $query->count();
        }

        $view->with('newRequestsCount', $newRequestsCount);
    }
}
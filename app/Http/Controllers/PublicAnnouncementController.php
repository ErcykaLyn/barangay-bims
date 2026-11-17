<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PublicAnnouncementController extends Controller
{
    private const MONTH_EXPR = "DATE_FORMAT(COALESCE(published_date, DATE(created_at)), '%Y-%m')";

    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $month  = (string) $request->query('month', '');
        $month  = preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $month) ? $month : '';

        $query = Announcement::published()->newestFirst();

        if ($search !== '') {
            $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")
                                       ->orWhere('content', 'like', "%{$search}%"));
        }

        if ($month !== '') {
            $query->whereRaw(self::MONTH_EXPR . ' = ?', [$month]);
        }

        $filtering     = $search !== '' || $month !== '';
        $announcements = $query->paginate(9)->withQueryString();

        // Featured: pinakabago, sa page 1 at kapag walang search/filter
        $featured = (!$filtering && $announcements->currentPage() === 1)
            ? $announcements->first()
            : null;

        $archive = Announcement::published()
            ->selectRaw(self::MONTH_EXPR . ' as ym, COUNT(*) as total')
            ->groupBy('ym')
            ->orderByDesc('ym')
            ->get()
            ->map(fn ($r) => [
                'value' => $r->ym,
                'total' => $r->total,
                'label' => Carbon::createFromFormat('!Y-m', $r->ym)->format('F Y'),
            ]);

        $total     = Announcement::published()->count();
        $thisMonth = Announcement::published()
            ->whereRaw(self::MONTH_EXPR . ' = ?', [now()->format('Y-m')])
            ->count();

        $monthLabel = $month !== ''
            ? Carbon::createFromFormat('!Y-m', $month)->format('F Y')
            : null;

        return view('public.announcements.index', compact(
            'announcements', 'featured', 'archive', 'total', 'thisMonth',
            'search', 'month', 'monthLabel', 'filtering'
        ));
    }

    public function show(Announcement $announcement, ?string $slug = null)
    {
        abort_unless($announcement->isPublished(), 404);

        $older = Announcement::published()
            ->where('id', '<', $announcement->id)->orderByDesc('id')->first();

        $newer = Announcement::published()
            ->where('id', '>', $announcement->id)->orderBy('id')->first();

        $others = Announcement::published()
            ->where('id', '!=', $announcement->id)
            ->newestFirst()->take(4)->get();

        return view('public.announcements.show', compact('announcement', 'older', 'newer', 'others'));
    }
}
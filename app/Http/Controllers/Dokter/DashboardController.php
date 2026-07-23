<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();

        $todayVisits = Visit::where('visit_date', $today)
            ->where('created_by', auth()->id())
            ->with(['customer', 'pet'])
            ->get();

        $draftVisits = Visit::where('status', 'draft')
            ->where('created_by', auth()->id())
            ->with(['customer', 'pet'])
            ->latest('visit_date')
            ->get();

        $recentPatients = Visit::where('created_by', auth()->id())
            ->with(['customer', 'pet'])
            ->latest('visit_date')
            ->limit(10)
            ->get();

        return view('dokter.dashboard', compact(
            'todayVisits',
            'draftVisits',
            'recentPatients',
        ));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Visit;
use App\Services\ReportService;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected ReportService $reportService,
    ) {}

    public function index(): View
    {
        $today = Carbon::today();

        $todayVisitsCount = Visit::whereDate('visit_date', $today)->count();

        $todayRevenue = Payment::where('status', 'paid')
            ->whereDate('created_at', $today)
            ->sum('amount');

        $pendingPaymentsCount = Invoice::where('status', 'unpaid')
            ->orWhere('status', 'partial')
            ->count();

        $lowStockProductsCount = Product::whereColumn('current_stock', '<', 'reorder_point')
            ->count();

        $recentVisits = Visit::with(['customer', 'pet', 'creator'])
            ->latest('visit_date')
            ->limit(10)
            ->get();

        $recentPayments = Payment::with(['payable', 'receiver'])
            ->latest()
            ->limit(10)
            ->get();

        $dailyReport = $this->reportService->getDailyReport($today->toDateString());

        return view('admin.dashboard', compact(
            'todayVisitsCount',
            'todayRevenue',
            'pendingPaymentsCount',
            'lowStockProductsCount',
            'recentVisits',
            'recentPayments',
            'dailyReport',
        ));
    }
}

<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\PosOrder;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();

        $todayPosTransactions = PosOrder::whereDate('created_at', $today)
            ->with(['customer', 'creator'])
            ->latest()
            ->get();

        $pendingPayments = Invoice::whereIn('status', ['unpaid', 'partial'])
            ->with(['customer', 'pet'])
            ->latest('due_date')
            ->get();

        $recentTransactions = PosOrder::with(['customer', 'creator'])
            ->latest()
            ->limit(10)
            ->get();

        $todayTransactionsTotal = $todayPosTransactions->where('status', 'completed')->sum('total');

        return view('kasir.dashboard', compact(
            'todayPosTransactions',
            'pendingPayments',
            'recentTransactions',
            'todayTransactionsTotal',
        ));
    }
}

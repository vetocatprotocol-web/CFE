<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\Visit;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $customer = $user->customers()->first();

        $pets = $customer?->pets()->where('status', 'active')->get() ?? collect();

        $recentVisits = Visit::where('customer_id', $customer?->id)
            ->with(['pet'])
            ->latest('visit_date')
            ->limit(10)
            ->get();

        $unpaidInvoices = Invoice::where('customer_id', $customer?->id)
            ->whereIn('status', ['unpaid', 'partial'])
            ->with(['pet'])
            ->latest('due_date')
            ->get();

        $notifications = Notification::where('user_id', $user->id)
            ->latest()
            ->limit(20)
            ->get();

        $unreadNotificationsCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return view('portal.dashboard', compact(
            'pets',
            'recentVisits',
            'unpaidInvoices',
            'notifications',
            'unreadNotificationsCount',
        ));
    }
}

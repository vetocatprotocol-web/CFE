<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Visit;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Get daily report for a given date.
     */
    public function getDailyReport(string $date): array
    {
        $date = Carbon::parse($date);

        $visitsCount = Visit::whereDate('visit_date', $date)->count();

        $revenue = Payment::where('status', 'paid')
            ->whereDate('created_at', $date)
            ->sum('amount');

        $revenueByMethod = Payment::where('status', 'paid')
            ->whereDate('created_at', $date)
            ->select('payment_method', DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method')
            ->toArray();

        $visitsByStatus = Visit::whereDate('visit_date', $date)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'date' => $date->toDateString(),
            'visits_count' => $visitsCount,
            'revenue' => round($revenue, 2),
            'revenue_by_method' => $revenueByMethod,
            'visits_by_status' => $visitsByStatus,
        ];
    }

    /**
     * Get revenue report for a date range.
     */
    public function getRevenueReport(string $dateFrom, string $dateTo): array
    {
        $dateFrom = Carbon::parse($dateFrom);
        $dateTo = Carbon::parse($dateTo);

        $payments = Payment::where('status', 'paid')
            ->whereBetween('created_at', [$dateFrom->startOfDay(), $dateTo->endOfDay()]);

        $totalRevenue = (clone $payments)->sum('amount');

        $revenueByMethod = (clone $payments)
            ->select('payment_method', DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method')
            ->toArray();

        $revenueByService = Payment::where('status', 'paid')
            ->whereBetween('created_at', [$dateFrom->startOfDay(), $dateTo->endOfDay()])
            ->where('payable_type', 'visit')
            ->join('invoice_items', 'invoice_items.invoice_id', '=', 'payments.payable_id')
            ->select('invoice_items.category', DB::raw('SUM(invoice_items.subtotal) as total'))
            ->groupBy('invoice_items.category')
            ->pluck('total', 'category')
            ->toArray();

        $dailyRevenue = Payment::where('status', 'paid')
            ->whereBetween('created_at', [$dateFrom->startOfDay(), $dateTo->endOfDay()])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as total'))
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        return [
            'date_from' => $dateFrom->toDateString(),
            'date_to' => $dateTo->toDateString(),
            'total_revenue' => round($totalRevenue, 2),
            'revenue_by_method' => $revenueByMethod,
            'revenue_by_service' => $revenueByService,
            'daily_revenue' => $dailyRevenue,
        ];
    }

    /**
     * Get inventory report.
     */
    public function getInventoryReport(): array
    {
        $products = Product::with('category')->get();

        $totalProducts = $products->count();
        $totalStockValue = $products->sum(fn ($p) => $p->current_stock * $p->price);
        $lowStockCount = $products->filter->isLowStock()->count();
        $outOfStockCount = $products->where('current_stock', 0)->count();

        $lowStockProducts = $products->filter->isLowStock()->map(fn ($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'category' => $p->category?->name,
            'current_stock' => $p->current_stock,
            'reorder_point' => $p->reorder_point,
        ])->values()->toArray();

        $stockByCategory = $products->groupBy(fn ($p) => $p->category?->name ?? 'Uncategorized')
            ->map(fn ($items) => [
                'count' => $items->count(),
                'total_stock' => $items->sum('current_stock'),
                'total_value' => round($items->sum(fn ($p) => $p->current_stock * $p->price), 2),
            ])
            ->toArray();

        return [
            'total_products' => $totalProducts,
            'total_stock_value' => round($totalStockValue, 2),
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount,
            'low_stock_products' => $lowStockProducts,
            'stock_by_category' => $stockByCategory,
        ];
    }

    /**
     * Get customer activity report.
     */
    public function getCustomerReport(): array
    {
        $totalCustomers = Customer::count();

        $activeCustomers = Customer::has('visits')->count();

        $newCustomersThisMonth = Customer::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $topCustomers = Customer::withCount('visits')
            ->withCount('invoices')
            ->orderByDesc('visits_count')
            ->limit(10)
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'phone' => $c->phone,
                'visits_count' => $c->visits_count,
                'invoices_count' => $c->invoices_count,
            ])
            ->toArray();

        $customersByCity = Customer::whereNotNull('city')
            ->select('city', DB::raw('COUNT(*) as count'))
            ->groupBy('city')
            ->orderByDesc('count')
            ->pluck('count', 'city')
            ->toArray();

        return [
            'total_customers' => $totalCustomers,
            'active_customers' => $activeCustomers,
            'new_customers_this_month' => $newCustomersThisMonth,
            'top_customers' => $topCustomers,
            'customers_by_city' => $customersByCity,
        ];
    }

    /**
     * Get payment report for a date range.
     */
    public function getPaymentReport(string $dateFrom, string $dateTo): array
    {
        $dateFrom = Carbon::parse($dateFrom);
        $dateTo = Carbon::parse($dateTo);

        $payments = Payment::whereBetween('created_at', [$dateFrom->startOfDay(), $dateTo->endOfDay()]);

        $totalPayments = (clone $payments)->where('status', 'paid')->sum('amount');
        $totalTransactions = (clone $payments)->where('status', 'paid')->count();
        $averagePayment = $totalTransactions > 0 ? $totalPayments / $totalTransactions : 0;

        $paymentsByMethod = (clone $payments)
            ->where('status', 'paid')
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->get()
            ->toArray();

        $paymentsByStatus = (clone $payments)
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('status')
            ->get()
            ->toArray();

        return [
            'date_from' => $dateFrom->toDateString(),
            'date_to' => $dateTo->toDateString(),
            'total_payments' => round($totalPayments, 2),
            'total_transactions' => $totalTransactions,
            'average_payment' => round($averagePayment, 2),
            'payments_by_method' => $paymentsByMethod,
            'payments_by_status' => $paymentsByStatus,
        ];
    }
}

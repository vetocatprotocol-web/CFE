<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\ProductCategory;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        private ReportService $reportService
    ) {}

    public function index(): View
    {
        return view('admin.reports.index');
    }

    public function daily(Request $request): View
    {
        $date = $request->get('date', now()->toDateString());
        $report = $this->reportService->getDailyReport($date);

        return view('admin.reports.daily', compact('report', 'date'));
    }

    public function revenue(Request $request): View
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        $report = $this->reportService->getRevenueReport($dateFrom, $dateTo);

        return view('admin.reports.revenue', compact('report', 'dateFrom', 'dateTo'));
    }

    public function inventory(Request $request): View
    {
        $report = $this->reportService->getInventoryReport();
        $categories = ProductCategory::pluck('name')->toArray();

        return view('admin.reports.inventory', compact('report', 'categories'));
    }

    public function customers(): View
    {
        $report = $this->reportService->getCustomerReport();

        return view('admin.reports.customers', compact('report'));
    }

    public function payments(Request $request): View
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        $report = $this->reportService->getPaymentReport($dateFrom, $dateTo);

        $unpaidInvoices = Invoice::with(['customer'])
            ->whereIn('status', ['unpaid', 'partial'])
            ->latest('invoice_date')
            ->get();

        return view('admin.reports.payments', compact('report', 'dateFrom', 'dateTo', 'unpaidInvoices'));
    }

    public function export(Request $request, string $type)
    {
        $reportType = $request->get('report', 'daily');

        if ($type === 'pdf') {
            return $this->exportPdf($reportType, $request);
        }

        if ($type === 'csv') {
            return $this->exportCsv($reportType, $request);
        }

        return back()->with('error', 'Invalid export type.');
    }

    private function exportPdf(string $reportType, Request $request)
    {
        $data = $this->getReportData($reportType, $request);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView("admin.reports.exports.{$reportType}", $data);

        return $pdf->download("{$reportType}-report.pdf");
    }

    private function exportCsv(string $reportType, Request $request)
    {
        $data = $this->getReportData($reportType, $request);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$reportType}-report.csv\"",
        ];

        $callback = function () use ($data, $reportType) {
            $file = fopen('php://output', 'w');

            match ($reportType) {
                'daily' => $this->writeDailyCsv($file, $data),
                'revenue' => $this->writeRevenueCsv($file, $data),
                'inventory' => $this->writeInventoryCsv($file, $data),
                'payments' => $this->writePaymentsCsv($file, $data),
                default => null,
            };

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getReportData(string $reportType, Request $request): array
    {
        return match ($reportType) {
            'daily' => [
                'report' => $this->reportService->getDailyReport($request->get('date', now()->toDateString())),
                'date' => $request->get('date', now()->toDateString()),
            ],
            'revenue' => [
                'report' => $this->reportService->getRevenueReport(
                    $request->get('date_from', now()->startOfMonth()->toDateString()),
                    $request->get('date_to', now()->toDateString())
                ),
                'dateFrom' => $request->get('date_from', now()->startOfMonth()->toDateString()),
                'dateTo' => $request->get('date_to', now()->toDateString()),
            ],
            'inventory' => [
                'report' => $this->reportService->getInventoryReport(),
            ],
            'payments' => [
                'report' => $this->reportService->getPaymentReport(
                    $request->get('date_from', now()->startOfMonth()->toDateString()),
                    $request->get('date_to', now()->toDateString())
                ),
            ],
            default => [],
        };
    }

    private function writeDailyCsv($file, array $data): void
    {
        fputcsv($file, ['Daily Report', $data['date'] ?? '']);
        fputcsv($file, []);
        fputcsv($file, ['Metric', 'Value']);
        fputcsv($file, ['Total Visits', $data['report']['visits_count'] ?? 0]);
        fputcsv($file, ['Total Revenue', $data['report']['revenue'] ?? 0]);
    }

    private function writeRevenueCsv($file, array $data): void
    {
        fputcsv($file, ['Revenue Report', ($data['dateFrom'] ?? '') . ' to ' . ($data['dateTo'] ?? '')]);
        fputcsv($file, []);
        fputcsv($file, ['Total Revenue', $data['report']['total_revenue'] ?? 0]);
        fputcsv($file, []);
        fputcsv($file, ['Payment Method', 'Total']);
        foreach ($data['report']['revenue_by_method'] ?? [] as $method => $total) {
            fputcsv($file, [$method, $total]);
        }
    }

    private function writeInventoryCsv($file, array $data): void
    {
        fputcsv($file, ['Inventory Report']);
        fputcsv($file, []);
        fputcsv($file, ['Category', 'Products', 'Total Stock', 'Stock Value']);
        foreach ($data['report']['stock_by_category'] ?? [] as $category => $info) {
            fputcsv($file, [$category, $info['count'], $info['total_stock'], $info['total_value']]);
        }
    }

    private function writePaymentsCsv($file, array $data): void
    {
        fputcsv($file, ['Payment Report']);
        fputcsv($file, []);
        fputcsv($file, ['Total Payments', $data['report']['total_payments'] ?? 0]);
        fputcsv($file, ['Total Transactions', $data['report']['total_transactions'] ?? 0]);
        fputcsv($file, []);
        fputcsv($file, ['Method', 'Count', 'Total']);
        foreach ($data['report']['payments_by_method'] ?? [] as $method) {
            fputcsv($file, [$method['payment_method'] ?? '', $method['count'] ?? 0, $method['total'] ?? 0]);
        }
    }
}

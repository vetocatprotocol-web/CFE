<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Pet;
use App\Models\Prescription;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PortalController extends Controller
{
    public function dashboard(): View
    {
        $customer = $this->getCustomer();

        $pets = $customer->pets()->where('status', 'active')->get();

        $recentVisits = $customer->visits()
            ->with(['pet', 'items.service', 'items.drug'])
            ->latest('visit_date')
            ->limit(5)
            ->get();

        $unpaidInvoices = $customer->invoices()
            ->with('pet')
            ->whereIn('status', ['unpaid', 'partial'])
            ->latest('invoice_date')
            ->get();

        return view('portal.dashboard', compact('customer', 'pets', 'recentVisits', 'unpaidInvoices'));
    }

    public function pets(): View
    {
        $customer = $this->getCustomer();
        $pets = $customer->pets()->with('visits')->orderBy('name')->get();

        return view('portal.pets.index', compact('pets'));
    }

    public function petShow(Pet $pet): View
    {
        $customer = $this->getCustomer();

        if ($pet->customer_id !== $customer->id) {
            abort(403, 'Unauthorized access to this pet.');
        }

        $pet->load(['visits.items.service', 'visits.items.drug', 'prescriptions']);

        return view('portal.pets.show', compact('pet'));
    }

    public function visits(Request $request): View
    {
        $customer = $this->getCustomer();
        $query = $customer->visits()->with(['pet', 'items.service', 'items.drug']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('pet_id')) {
            $query->where('pet_id', $request->pet_id);
        }

        $visits = $query->latest('visit_date')->paginate(10);
        $pets = $customer->pets()->where('status', 'active')->orderBy('name')->get();

        return view('portal.visits.index', compact('visits', 'pets'));
    }

    public function visitShow(Visit $visit): View
    {
        $customer = $this->getCustomer();

        if ($visit->customer_id !== $customer->id) {
            abort(403, 'Unauthorized access to this visit.');
        }

        $visit->load(['pet', 'items.service', 'items.drug', 'invoice', 'prescriptions']);

        return view('portal.visits.show', compact('visit'));
    }

    public function invoices(): View
    {
        $customer = $this->getCustomer();
        $invoices = $customer->invoices()
            ->with(['pet', 'payments'])
            ->latest('invoice_date')
            ->paginate(10);

        return view('portal.invoices.index', compact('invoices'));
    }

    public function invoiceShow(Invoice $invoice): View
    {
        $customer = $this->getCustomer();

        if ($invoice->customer_id !== $customer->id) {
            abort(403, 'Unauthorized access to this invoice.');
        }

        $invoice->load(['pet', 'items', 'payments.receiver']);

        return view('portal.invoices.show', compact('invoice'));
    }

    public function invoiceDownload(Invoice $invoice)
    {
        $customer = $this->getCustomer();

        if ($invoice->customer_id !== $customer->id) {
            abort(403, 'Unauthorized access to this invoice.');
        }

        $invoice->load(['customer', 'pet', 'items']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('shared.invoices.pdf', compact('invoice'));

        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }

    public function prescriptions(): View
    {
        $customer = $this->getCustomer();
        $prescriptions = Prescription::with(['pet', 'visit', 'items.drug'])
            ->where('customer_id', $customer->id)
            ->latest('prescription_date')
            ->paginate(10);

        return view('portal.prescriptions.index', compact('prescriptions'));
    }

    public function prescriptionShow(Prescription $prescription): View
    {
        $customer = $this->getCustomer();

        if ($prescription->customer_id !== $customer->id) {
            abort(403, 'Unauthorized access to this prescription.');
        }

        $prescription->load(['pet', 'visit', 'items.drug']);

        return view('portal.prescriptions.show', compact('prescription'));
    }

    public function profile(): View
    {
        $customer = $this->getCustomer();
        $user = Auth::user();

        return view('portal.profile.edit', compact('customer', 'user'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
        ]);

        $customer = $this->getCustomer();
        $user = Auth::user();

        $user->update(['name' => $validated['name'], 'email' => $validated['email']]);

        $customer->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? $customer->phone,
            'address' => $validated['address'] ?? $customer->address,
            'city' => $validated['city'] ?? $customer->city,
            'postal_code' => $validated['postal_code'] ?? $customer->postal_code,
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function changePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        $user->update(['password' => Hash::make($validated['password'])]);

        return back()->with('success', 'Password changed successfully.');
    }

    protected function getCustomer(): Customer
    {
        $user = Auth::user();

        return Customer::where('user_id', $user->id)->firstOrFail();
    }
}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; line-height: 1.5; }

        .page { width: 100%; padding: 30px 40px; }

        /* Header */
        .header { display: table; width: 100%; margin-bottom: 25px; border-bottom: 3px solid #2563eb; padding-bottom: 15px; }
        .header-left { display: table-cell; vertical-align: top; width: 60%; }
        .header-right { display: table-cell; vertical-align: top; width: 40%; text-align: right; }
        .company-logo { width: 50px; height: 50px; background: #2563eb; color: #fff; text-align: center; line-height: 50px; font-size: 20px; font-weight: bold; border-radius: 8px; margin-bottom: 8px; }
        .company-name { font-size: 18px; font-weight: bold; color: #1e40af; margin-bottom: 3px; }
        .company-info { font-size: 9px; color: #666; line-height: 1.4; }
        .invoice-title { font-size: 28px; font-weight: bold; color: #2563eb; text-transform: uppercase; letter-spacing: 2px; }
        .invoice-title-sub { font-size: 10px; color: #666; margin-top: 2px; }

        /* Details Section */
        .details-section { display: table; width: 100%; margin-bottom: 25px; }
        .details-box { display: table-cell; vertical-align: top; width: 50%; }
        .details-box-label { font-size: 9px; font-weight: bold; text-transform: uppercase; color: #666; margin-bottom: 5px; letter-spacing: 0.5px; }
        .details-box p { font-size: 11px; margin-bottom: 2px; color: #444; }
        .details-box strong { color: #222; }

        /* Items Table */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .items-table thead th { background: #2563eb; color: #fff; padding: 8px 10px; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .items-table thead th:first-child { border-radius: 4px 0 0 0; }
        .items-table thead th:last-child { border-radius: 0 4px 0 0; }
        .items-table tbody td { padding: 8px 10px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
        .items-table tbody tr:nth-child(even) { background: #f9fafb; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* Totals */
        .totals-section { display: table; width: 100%; margin-bottom: 25px; }
        .totals-spacer { display: table-cell; width: 55%; }
        .totals-box { display: table-cell; width: 45%; }
        .totals-table { width: 100%; }
        .totals-table tr td { padding: 4px 0; font-size: 11px; }
        .totals-table tr td:last-child { text-align: right; font-weight: 500; }
        .totals-table tr.total-row td { font-size: 14px; font-weight: bold; color: #1e40af; border-top: 2px solid #2563eb; padding-top: 8px; margin-top: 4px; }

        /* Payment Section */
        .payment-section { background: #f0f9ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 15px; margin-bottom: 25px; }
        .payment-title { font-size: 12px; font-weight: bold; color: #1e40af; margin-bottom: 8px; text-transform: uppercase; }
        .payment-grid { display: table; width: 100%; }
        .payment-item { display: table-cell; width: 33.33%; }
        .payment-item p { font-size: 10px; color: #666; margin-bottom: 2px; }
        .payment-item strong { font-size: 13px; color: #222; }

        .status-badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .status-paid { background: #dcfce7; color: #166534; }
        .status-partial { background: #fef9c3; color: #854d0e; }
        .status-unpaid { background: #fee2e2; color: #991b1b; }

        /* Footer */
        .footer { border-top: 1px solid #e5e7eb; padding-top: 15px; margin-top: 20px; }
        .footer-notes { font-size: 10px; color: #666; margin-bottom: 10px; }
        .footer-thanks { font-size: 12px; font-weight: bold; color: #2563eb; text-align: center; margin-top: 15px; }
        .footer-info { font-size: 9px; color: #999; text-align: center; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="page">
        {{-- Header --}}
        <div class="header">
            <div class="header-left">
                <div class="company-logo">HP</div>
                <div class="company-name">{{ $settings['company_name'] ?? 'Haland PetCare' }}</div>
                <div class="company-info">
                    {{ $settings['company_address'] ?? 'Jl. Contoh No. 123, Kota' }}<br>
                    Telp: {{ $settings['company_phone'] ?? '(021) 1234-5678' }}<br>
                    Email: {{ $settings['company_email'] ?? 'info@halandpetcare.com' }}
                </div>
            </div>
            <div class="header-right">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-title-sub">{{ $settings['invoice_prefix'] ?? 'INV' }}-{{ $invoice->invoice_number }}</div>
            </div>
        </div>

        {{-- Invoice Details & Customer Info --}}
        <div class="details-section">
            <div class="details-box">
                <div class="details-box-label">Invoice Details</div>
                <p><strong>Invoice Number:</strong> {{ $invoice->invoice_number }}</p>
                <p><strong>Invoice Date:</strong> {{ $invoice->invoice_date ? $invoice->invoice_date->format('d/m/Y') : '-' }}</p>
                <p><strong>Due Date:</strong> {{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : '-' }}</p>
            </div>
            <div class="details-box">
                <div class="details-box-label">Bill To</div>
                <p><strong>{{ $invoice->customer->name ?? '-' }}</strong></p>
                @if($invoice->customer?->phone)
                    <p>Phone: {{ $invoice->customer->phone }}</p>
                @endif
                @if($invoice->customer?->email)
                    <p>Email: {{ $invoice->customer->email }}</p>
                @endif
                @if($invoice->customer?->address)
                    <p>Address: {{ $invoice->customer->address }}</p>
                @endif

                @if($invoice->pet)
                    <div class="details-box-label" style="margin-top: 10px;">Pet Information</div>
                    <p><strong>{{ $invoice->pet->name }}</strong></p>
                    @if($invoice->pet->species)
                        <p>Species: {{ $invoice->pet->species }}@if($invoice->pet->breed) &middot; {{ $invoice->pet->breed }}@endif</p>
                    @endif
                @endif
            </div>
        </div>

        {{-- Items Table --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 45%;">Item Name</th>
                    <th class="text-center" style="width: 10%;">Qty</th>
                    <th class="text-right" style="width: 20%;">Unit Price</th>
                    <th class="text-right" style="width: 20%;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->item_name ?? '-' }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                @if($invoice->items->isEmpty())
                    <tr>
                        <td colspan="5" class="text-center" style="padding: 20px; color: #999;">No items</td>
                    </tr>
                @endif
            </tbody>
        </table>

        {{-- Totals --}}
        <div class="totals-section">
            <div class="totals-spacer"></div>
            <div class="totals-box">
                <table class="totals-table">
                    <tr>
                        <td>Subtotal</td>
                        <td>Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @if(($invoice->tax_amount ?? 0) > 0)
                        <tr>
                            <td>Tax</td>
                            <td>Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    @if(($invoice->discount_amount ?? 0) > 0)
                        <tr>
                            <td>Discount</td>
                            <td style="color: #dc2626;">- Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    <tr class="total-row">
                        <td>TOTAL</td>
                        <td>Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Payment Section --}}
        <div class="payment-section">
            <div class="payment-title">Payment Information</div>
            <div class="payment-grid">
                <div class="payment-item">
                    <p>Total Paid</p>
                    <strong>Rp {{ number_format($invoice->paid_amount ?? 0, 0, ',', '.') }}</strong>
                </div>
                <div class="payment-item">
                    <p>Remaining Amount</p>
                    <strong style="color: {{ ($invoice->total - ($invoice->paid_amount ?? 0)) > 0 ? '#dc2626' : '#16a34a' }};">
                        Rp {{ number_format($invoice->total - ($invoice->paid_amount ?? 0), 0, ',', '.') }}
                    </strong>
                </div>
                <div class="payment-item">
                    <p>Status</p>
                    <span class="status-badge
                        @if($invoice->status === 'PAID') status-paid
                        @elseif($invoice->status === 'PARTIAL') status-partial
                        @else status-unpaid @endif">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            @if($invoice->notes || ($settings['invoice_notes'] ?? null))
                <div class="footer-notes">
                    <strong>Notes:</strong> {{ $invoice->notes ?? $settings['invoice_notes'] ?? '' }}
                </div>
            @endif
            <div class="footer-thanks">Thank you for your visit!</div>
            <div class="footer-info">
                {{ $settings['company_name'] ?? 'Haland PetCare' }} &mdash; {{ $settings['company_address'] ?? '' }}
            </div>
        </div>
    </div>
</body>
</html>

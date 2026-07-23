<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Struk {{ $order->order_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', Courier, monospace; font-size: 11px; color: #333; line-height: 1.4; }

        .receipt { width: 280px; padding: 15px; margin: 0 auto; }

        .header { text-align: center; border-bottom: 1px dashed #333; padding-bottom: 10px; margin-bottom: 10px; }
        .company-name { font-size: 14px; font-weight: bold; margin-bottom: 3px; }
        .company-info { font-size: 9px; color: #555; line-height: 1.3; }

        .info { font-size: 10px; margin-bottom: 10px; border-bottom: 1px dashed #333; padding-bottom: 10px; }
        .info p { margin-bottom: 2px; }
        .info strong { font-weight: bold; }

        .items { margin-bottom: 10px; }
        .items table { width: 100%; border-collapse: collapse; font-size: 10px; }
        .items th, .items td { padding: 3px 0; text-align: left; }
        .items th { border-bottom: 1px solid #333; font-weight: bold; }
        .items td:last-child, .items th:last-child { text-align: right; }
        .items td:nth-child(2), .items th:nth-child(2) { text-align: center; }

        .totals { border-top: 1px dashed #333; padding-top: 5px; font-size: 10px; }
        .totals .row { display: flex; justify-content: space-between; margin-bottom: 2px; }
        .totals .row.total { font-weight: bold; font-size: 12px; border-top: 1px solid #333; padding-top: 3px; margin-top: 3px; }

        .payment { border-top: 1px dashed #333; padding-top: 5px; font-size: 10px; margin-top: 5px; }
        .payment .row { display: flex; justify-content: space-between; margin-bottom: 2px; }

        .footer { text-align: center; border-top: 1px dashed #333; padding-top: 10px; margin-top: 10px; font-size: 10px; color: #555; }
        .footer-thanks { font-weight: bold; color: #333; margin-bottom: 3px; }
    </style>
</head>
<body>
    <div class="receipt">
        {{-- Header --}}
        <div class="header">
            <div class="company-name">{{ $settings['company_name'] ?? 'Haland PetCare' }}</div>
            <div class="company-info">
                {{ $settings['company_address'] ?? 'Jl. Contoh No. 123, Kota' }}<br>
                Telp: {{ $settings['company_phone'] ?? '(021) 1234-5678' }}
            </div>
        </div>

        {{-- Info --}}
        <div class="info">
            <p><strong>No. Struk:</strong> {{ $order->order_number }}</p>
            <p><strong>Tanggal:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>Kasir:</strong> {{ $order->creator->name ?? '-' }}</p>
            @if($order->customer)
                <p><strong>Pelanggan:</strong> {{ $order->customer->name }}</p>
            @endif
        </div>

        {{-- Items --}}
        <div class="items">
            <table>
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th style="text-align:center;">Qty</th>
                        <th style="text-align:right;">Harga</th>
                        <th style="text-align:right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product->name ?? '-' }}</td>
                            <td style="text-align:center;">{{ $item->quantity }}</td>
                            <td style="text-align:right;">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td style="text-align:right;">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Totals --}}
        <div class="totals">
            <div class="row">
                <span>Subtotal</span>
                <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            @if(($order->discount_amount ?? 0) > 0)
                <div class="row">
                    <span>Diskon</span>
                    <span>- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                </div>
            @endif
            @if(($order->tax_amount ?? 0) > 0)
                <div class="row">
                    <span>Pajak</span>
                    <span>Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="row total">
                <span>TOTAL</span>
                <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Payment --}}
        <div class="payment">
            <div class="row">
                <span>Metode Bayar</span>
                <span>{{ strtoupper($order->payment_method ?? '-') }}</span>
            </div>
            <div class="row">
                <span>Dibayar</span>
                <span>Rp {{ number_format($order->payment_amount ?? 0, 0, ',', '.') }}</span>
            </div>
            @if(($order->change_amount ?? 0) > 0)
                <div class="row">
                    <span>Kembalian</span>
                    <span>Rp {{ number_format($order->change_amount, 0, ',', '.') }}</span>
                </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="footer">
            <div class="footer-thanks">Terima kasih atas kunjungan Anda!</div>
            <p>Barang yang sudah dibeli tidak dapat</p>
            <p>ditukar atau dikembalikan.</p>
        </div>
    </div>
</body>
</html>

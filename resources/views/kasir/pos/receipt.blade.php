<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk - {{ $order->order_number }}</title>
    <style>
        @media print {
            body { margin: 0; padding: 0; }
            .no-print { display: none !important; }
            .receipt { box-shadow: none !important; border: none !important; margin: 0 !important; }
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Courier New', Courier, monospace; background: #f3f4f6; }
        .receipt {
            max-width: 320px;
            margin: 2rem auto;
            background: white;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .header { text-align: center; border-bottom: 1px dashed #000; padding-bottom: 0.75rem; margin-bottom: 0.75rem; }
        .header h1 { font-size: 1.1rem; font-weight: bold; margin-bottom: 0.25rem; }
        .header p { font-size: 0.75rem; color: #555; line-height: 1.4; }
        .info { font-size: 0.75rem; margin-bottom: 0.75rem; border-bottom: 1px dashed #000; padding-bottom: 0.75rem; }
        .info p { margin-bottom: 0.15rem; }
        .items { margin-bottom: 0.75rem; }
        .items table { width: 100%; border-collapse: collapse; font-size: 0.75rem; }
        .items th, .items td { padding: 0.25rem 0; text-align: left; }
        .items th { border-bottom: 1px solid #000; font-weight: bold; }
        .items td:last-child, .items th:last-child { text-align: right; }
        .totals { border-top: 1px dashed #000; padding-top: 0.5rem; font-size: 0.75rem; }
        .totals .row { display: flex; justify-content: space-between; margin-bottom: 0.2rem; }
        .totals .row.total { font-weight: bold; font-size: 0.9rem; border-top: 1px solid #000; padding-top: 0.3rem; margin-top: 0.3rem; }
        .payment { border-top: 1px dashed #000; padding-top: 0.5rem; font-size: 0.75rem; margin-top: 0.5rem; }
        .payment .row { display: flex; justify-content: space-between; margin-bottom: 0.2rem; }
        .footer { text-align: center; border-top: 1px dashed #000; padding-top: 0.75rem; margin-top: 0.75rem; font-size: 0.75rem; color: #555; }
    </style>
</head>
<body>
    <button onclick="window.print()" class="no-print" style="position:fixed;top:1rem;right:1rem;background:#2563eb;color:white;padding:0.5rem 1.5rem;border-radius:0.5rem;border:none;cursor:pointer;font-size:0.875rem;font-weight:600;z-index:100;">
        Cetak Struk
    </button>
    <button onclick="window.close()" class="no-print" style="position:fixed;top:1rem;right:8rem;background:#6b7280;color:white;padding:0.5rem 1.5rem;border-radius:0.5rem;border:none;cursor:pointer;font-size:0.875rem;z-index:100;">
        Tutup
    </button>

    <div class="receipt">
        <div class="header">
            <h1>{{ config('app.name', 'Haland PetCare') }}</h1>
            <p>{{ config('clinic.address', 'Jl. Contoh No. 123, Kota') }}</p>
            <p>Telp: {{ config('clinic.phone', '(021) 1234-5678') }}</p>
        </div>

        <div class="info">
            <p><strong>No. Struk:</strong> {{ $order->order_number }}</p>
            <p><strong>Tanggal:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>Kasir:</strong> {{ $order->creator->name ?? '-' }}</p>
            @if($order->customer)
                <p><strong>Pelanggan:</strong> {{ $order->customer->name }}</p>
            @endif
        </div>

        <div class="items">
            <table>
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th style="text-align:center">Qty</th>
                        <th style="text-align:right">Harga</th>
                        <th style="text-align:right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td style="text-align:center">{{ $item->quantity }}</td>
                            <td style="text-align:right">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td style="text-align:right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="totals">
            <div class="row">
                <span>Subtotal</span>
                <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($order->discount_amount > 0)
                <div class="row">
                    <span>Diskon</span>
                    <span>- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                </div>
            @endif
            @if($order->tax_amount > 0)
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

        <div class="footer">
            <p>Terima kasih atas kunjungan Anda!</p>
            <p>Barang yang sudah dibeli tidak dapat</p>
            <p>ditukar atau dikembalikan.</p>
        </div>
    </div>
</body>
</html>

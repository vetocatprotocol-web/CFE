<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Invoice - Haland PetCare</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f3f4f6; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; }
        .header { background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); padding: 30px 40px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 24px; margin: 0; font-weight: 700; }
        .header p { color: #bfdbfe; font-size: 14px; margin: 5px 0 0; }
        .content { padding: 30px 40px; }
        .greeting { font-size: 16px; color: #374151; margin-bottom: 20px; }
        .section-title { font-size: 13px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; }
        .detail-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
        .detail-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 14px; }
        .detail-label { color: #6b7280; }
        .detail-value { color: #111827; font-weight: 500; }
        .total-box { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 15px 20px; margin-bottom: 25px; text-align: center; }
        .total-label { font-size: 13px; color: #2563eb; font-weight: 600; }
        .total-amount { font-size: 24px; color: #1e40af; font-weight: 700; margin-top: 5px; }
        .cta-button { display: block; background: #16a34a; color: #ffffff; text-align: center; padding: 14px 30px; border-radius: 8px; text-decoration: none; font-size: 16px; font-weight: 600; margin: 0 auto 25px; max-width: 250px; }
        .cta-button:hover { background: #15803d; }
        .footer { background: #f9fafb; border-top: 1px solid #e5e7eb; padding: 20px 40px; text-align: center; }
        .footer p { font-size: 12px; color: #9ca3af; margin: 3px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Haland PetCare</h1>
            <p>Klinik Hewan Terpercaya</p>
        </div>

        <div class="content">
            <p class="greeting">Halo {{ $invoice->customer->name ?? 'Pelanggan' }},</p>

            <p style="font-size: 14px; color: #4b5563; margin-bottom: 20px;">
                Invoice baru telah diterbitkan untuk kunjungan terbaru Anda. Berikut adalah rinciannya:
            </p>

            <div class="section-title">Detail Invoice</div>
            <div class="detail-box">
                <div class="detail-row">
                    <span class="detail-label">Nomor Invoice</span>
                    <span class="detail-value">{{ $invoice->invoice_number }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tanggal</span>
                    <span class="detail-value">{{ $invoice->invoice_date?->format('d/m/Y') ?? '-' }}</span>
                </div>
                @if($invoice->due_date)
                    <div class="detail-row">
                        <span class="detail-label">Jatuh Tempo</span>
                        <span class="detail-value">{{ $invoice->due_date->format('d/m/Y') }}</span>
                    </div>
                @endif
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="detail-value" style="color: {{ $invoice->status === 'PAID' ? '#16a34a' : '#dc2626' }};">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </div>
            </div>

            <div class="section-title">Total Tagihan</div>
            <div class="total-box">
                <div class="total-label">TOTAL</div>
                <div class="total-amount">Rp {{ number_format($invoice->total, 0, ',', '.') }}</div>
            </div>

            <a href="{{ route('portal.invoices.show', $invoice) }}" class="cta-button">Bayar Sekarang</a>
        </div>

        <div class="footer">
            <p><strong>Haland PetCare</strong></p>
            <p>Jl. Contoh No. 123, Kota</p>
            <p>Telp: (021) 1234-5678 | Email: info@halandpetcare.com</p>
            <p style="margin-top: 10px;">&copy; {{ date('Y') }} Haland PetCare. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

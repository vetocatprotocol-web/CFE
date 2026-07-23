<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Confirmed - Haland PetCare</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f3f4f6; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; }
        .header { background: linear-gradient(135deg, #16a34a 0%, #166534 100%); padding: 30px 40px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 24px; margin: 0; font-weight: 700; }
        .header p { color: #bbf7d0; font-size: 14px; margin: 5px 0 0; }
        .content { padding: 30px 40px; }
        .greeting { font-size: 16px; color: #374151; margin-bottom: 20px; }
        .section-title { font-size: 13px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; }
        .detail-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
        .detail-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 14px; }
        .detail-label { color: #6b7280; }
        .detail-value { color: #111827; font-weight: 500; }
        .total-box { background: #f0fdf4; border: 1px solid #86efac; border-radius: 8px; padding: 15px 20px; margin-bottom: 25px; text-align: center; }
        .total-label { font-size: 13px; color: #16a34a; font-weight: 600; }
        .total-amount { font-size: 24px; color: #166534; font-weight: 700; margin-top: 5px; }
        .cta-button { display: block; background: #2563eb; color: #ffffff; text-align: center; padding: 14px 30px; border-radius: 8px; text-decoration: none; font-size: 16px; font-weight: 600; margin: 0 auto 25px; max-width: 250px; }
        .cta-button:hover { background: #1d4ed8; }
        .footer { background: #f9fafb; border-top: 1px solid #e5e7eb; padding: 20px 40px; text-align: center; }
        .footer p { font-size: 12px; color: #9ca3af; margin: 3px 0; }
        .success-icon { width: 60px; height: 60px; background: #dcfce7; border-radius: 50%; margin: 0 auto 15px; line-height: 60px; font-size: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Haland PetCare</h1>
            <p>Klinik Hewan Terpercaya</p>
        </div>

        <div class="content">
            <div class="success-icon">&#10003;</div>

            <p class="greeting" style="text-align: center;">Pembayaran Berhasil!</p>

            <p style="font-size: 14px; color: #4b5563; margin-bottom: 20px; text-align: center;">
                Pembayaran Anda telah dikonfirmasi. Berikut adalah rinciannya:
            </p>

            <div class="section-title">Detail Pembayaran</div>
            <div class="detail-box">
                <div class="detail-row">
                    <span class="detail-label">Nomor Pembayaran</span>
                    <span class="detail-value">{{ $payment->payment_number }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tanggal</span>
                    <span class="detail-value">{{ $payment->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Metode Pembayaran</span>
                    <span class="detail-value">{{ ucfirst($payment->payment_method) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="detail-value" style="color: #16a34a; font-weight: 600;">Berhasil</span>
                </div>
            </div>

            <div class="total-box">
                <div class="total-label">JUMLAH DIBAYAR</div>
                <div class="total-amount">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
            </div>

            @if($payment->payable_type === 'App\Models\Invoice')
                <div class="section-title">Referensi</div>
                <div class="detail-box">
                    <div class="detail-row">
                        <span class="detail-label">Invoice</span>
                        <span class="detail-value">#{{ $payment->payable->invoice_number ?? '-' }}</span>
                    </div>
                </div>

                <a href="{{ route('portal.invoices.show', $payment->payable_id) }}" class="cta-button">Lihat Struk</a>
            @endif
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

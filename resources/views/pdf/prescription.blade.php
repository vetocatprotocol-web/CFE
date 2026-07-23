<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Resep {{ $prescription->prescription_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; line-height: 1.5; }

        .page { width: 100%; padding: 30px 40px; }

        .header { display: table; width: 100%; margin-bottom: 25px; border-bottom: 3px solid #16a34a; padding-bottom: 15px; }
        .header-left { display: table-cell; vertical-align: top; width: 60%; }
        .header-right { display: table-cell; vertical-align: top; width: 40%; text-align: right; }
        .company-logo { width: 50px; height: 50px; background: #16a34a; color: #fff; text-align: center; line-height: 50px; font-size: 20px; font-weight: bold; border-radius: 8px; margin-bottom: 8px; }
        .company-name { font-size: 18px; font-weight: bold; color: #166534; margin-bottom: 3px; }
        .company-info { font-size: 9px; color: #666; line-height: 1.4; }
        .prescription-title { font-size: 26px; font-weight: bold; color: #16a34a; text-transform: uppercase; letter-spacing: 2px; }
        .prescription-number { font-size: 10px; color: #666; margin-top: 2px; }

        .rx-symbol { font-size: 36px; font-weight: bold; color: #16a34a; float: left; margin-right: 15px; margin-top: -5px; }

        .info-section { display: table; width: 100%; margin-bottom: 25px; }
        .info-box { display: table-cell; vertical-align: top; width: 50%; }
        .info-box-label { font-size: 9px; font-weight: bold; text-transform: uppercase; color: #666; margin-bottom: 5px; letter-spacing: 0.5px; }
        .info-box p { font-size: 11px; margin-bottom: 2px; color: #444; }
        .info-box strong { color: #222; }

        .drug-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .drug-table thead th { background: #16a34a; color: #fff; padding: 8px 10px; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .drug-table thead th:first-child { border-radius: 4px 0 0 0; }
        .drug-table thead th:last-child { border-radius: 0 4px 0 0; }
        .drug-table tbody td { padding: 8px 10px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
        .drug-table tbody tr:nth-child(even) { background: #f0fdf4; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .signature-section { display: table; width: 100%; margin-top: 40px; }
        .signature-box { display: table-cell; vertical-align: top; width: 50%; }
        .signature-line { width: 200px; border-bottom: 1px solid #333; margin-top: 60px; margin-bottom: 5px; }
        .signature-label { font-size: 10px; color: #666; }
        .signature-name { font-size: 11px; font-weight: bold; color: #333; }

        .footer { border-top: 1px solid #e5e7eb; padding-top: 15px; margin-top: 25px; }
        .footer-notes { font-size: 10px; color: #666; margin-bottom: 5px; }
        .footer-info { font-size: 9px; color: #999; text-align: center; margin-top: 10px; }
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
                <div class="prescription-title">PRESCRIPTION</div>
                <div class="prescription-number">No: {{ $prescription->prescription_number }}</div>
            </div>
        </div>

        {{-- Prescription & Patient Info --}}
        <div class="info-section">
            <div class="info-box">
                <div class="info-box-label">Prescription Details</div>
                <p><strong>Prescription Number:</strong> {{ $prescription->prescription_number }}</p>
                <p><strong>Date:</strong> {{ $prescription->prescription_date ? $prescription->prescription_date->format('d/m/Y') : '-' }}</p>
                <p><strong>Visit Number:</strong> {{ $prescription->visit?->visit_number ?? '-' }}</p>
            </div>
            <div class="info-box">
                <div class="info-box-label">Patient Information</div>
                @if($prescription->pet)
                    <p><strong>Pet:</strong> {{ $prescription->pet->name }}</p>
                    @if($prescription->pet->species)
                        <p><strong>Species:</strong> {{ $prescription->pet->species }}@if($prescription->pet->breed) &middot; {{ $prescription->pet->breed }}@endif</p>
                    @endif
                @endif
                @if($prescription->customer)
                    <p><strong>Owner:</strong> {{ $prescription->customer->name }}</p>
                @endif
                <p><strong>Doctor:</strong> {{ $prescription->visit?->creator?->name ?? '-' }}</p>
            </div>
        </div>

        {{-- Drug Table --}}
        <table class="drug-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 25%;">Drug Name</th>
                    <th class="text-center" style="width: 10%;">Quantity</th>
                    <th style="width: 15%;">Dosage</th>
                    <th class="text-center" style="width: 10%;">Duration</th>
                    <th style="width: 35%;">Instructions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prescription->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $item->drug?->name ?? '-' }}</strong></td>
                        <td class="text-center">{{ $item->quantity }} {{ $item->drug?->unit ?? '' }}</td>
                        <td>{{ $item->dosage ?: '-' }}</td>
                        <td class="text-center">{{ $item->duration_days }} hari</td>
                        <td>{{ $item->instructions ?: '-' }}</td>
                    </tr>
                @endforeach
                @if($prescription->items->isEmpty())
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 20px; color: #999;">No drugs prescribed</td>
                    </tr>
                @endif
            </tbody>
        </table>

        {{-- Signature --}}
        <div class="signature-section">
            <div class="signature-box" style="text-align: right;">
                <div class="signature-label" style="margin-top: 60px;">Dokter,</div>
                <div class="signature-line" style="margin-left: auto;"></div>
                <div class="signature-name">{{ $prescription->visit?->creator?->name ?? '-' }}</div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            @if($prescription->notes ?? null)
                <div class="footer-notes">
                    <strong>Catatan:</strong> {{ $prescription->notes }}
                </div>
            @endif
            <div class="footer-info">
                {{ $settings['company_name'] ?? 'Haland PetCare' }} &mdash; {{ $settings['company_address'] ?? '' }}<br>
                Telp: {{ $settings['company_phone'] ?? '' }}
            </div>
        </div>
    </div>
</body>
</html>

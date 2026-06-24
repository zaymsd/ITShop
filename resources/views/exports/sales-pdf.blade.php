<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 24px; color: #1e293b; }
        .header p { margin: 5px 0; color: #64748b; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #e2e8f0; padding: 8px 10px; text-align: left; }
        th { background-color: #f8fafc; color: #475569; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge { display: inline-block; padding: 3px 6px; font-size: 10px; border-radius: 4px; color: white; background-color: #4f46e5; }
        .summary { float: right; width: 300px; }
        .summary-table { width: 100%; }
        .summary-table th, .summary-table td { border: none; padding: 5px; }
        .summary-table th { text-align: left; }
        .summary-table .total { font-weight: bold; font-size: 14px; border-top: 1px solid #000; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <p>Laporan Transaksi Penjualan</p>
        @if($startDate && $endDate)
            <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
        @else
            <p>Periode: Semua Waktu</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Tanggal</th>
                <th>No. Invoice</th>
                <th>Pelanggan</th>
                <th>Kasir</th>
                <th class="text-center">Pembayaran</th>
                <th class="text-right">Grand Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $index => $sale)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $sale->invoice_no }}</td>
                    <td>{{ $sale->customer_name }}</td>
                    <td>{{ $sale->user->name ?? '-' }}</td>
                    <td class="text-center">
                        <span class="badge" style="background-color: {{ $sale->payment_method === 'cash' ? '#10b981' : '#f59e0b' }}">
                            {{ strtoupper($sale->payment_method) }}
                        </span>
                    </td>
                    <td class="text-right">{{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px;">Tidak ada data transaksi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="clearfix">
        <div class="summary">
            <table class="summary-table">
                <tr>
                    <th>Total Transaksi:</th>
                    <td class="text-right">{{ $sales->count() }}</td>
                </tr>
                <tr>
                    <th>Total Subtotal:</th>
                    <td class="text-right">Rp {{ number_format($sales->sum('total'), 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Total PPN (11%):</th>
                    <td class="text-right">Rp {{ number_format($sales->sum('tax'), 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th class="total">Total Pendapatan:</th>
                    <td class="text-right total">Rp {{ number_format($sales->sum('grand_total'), 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    </div>
    
    <div style="margin-top: 50px; font-size: 10px; color: #94a3b8; text-align: center;">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} oleh {{ auth()->user()->name ?? 'System' }}
    </div>
</body>
</html>

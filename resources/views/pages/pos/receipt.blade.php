<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $sale->invoice_no }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
            width: 100%;
            max-width: 300px; /* Thermal printer typical width 58mm or 80mm */
        }
        .receipt-container {
            padding: 10px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .border-top { border-top: 1px dashed #000; }
        .border-bottom { border-bottom: 1px dashed #000; }
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        .mt-1 { margin-top: 5px; }
        .mt-2 { margin-top: 10px; }
        .w-100 { width: 100%; }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 3px 0; vertical-align: top; }
        
        .item-name {
            display: block;
            margin-bottom: 2px;
        }
        .item-details {
            display: flex;
            justify-content: space-between;
        }
        
        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        
        @media print {
            body { max-width: 100%; }
            .no-print { display: none; }
        }
        
        .btn-print {
            display: block;
            width: 100%;
            padding: 10px;
            background: #000;
            color: #fff;
            text-align: center;
            text-decoration: none;
            font-family: sans-serif;
            font-size: 14px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <button class="no-print btn-print" onclick="window.print()">Cetak Sekarang</button>
        
        <div class="text-center mb-2">
            <h2 style="margin: 0; font-size: 16px;">IT SHOP</h2>
            <p style="margin: 3px 0; font-size: 11px;">Jl. Teknologi No. 123, Kota Tech</p>
            <p style="margin: 3px 0; font-size: 11px;">Telp: 08123456789</p>
        </div>
        
        <div class="divider"></div>
        
        <table class="w-100 mb-1" style="font-size: 11px;">
            <tr>
                <td>Tgl</td>
                <td>: {{ $sale->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td>No.</td>
                <td>: {{ $sale->invoice_no }}</td>
            </tr>
            <tr>
                <td>Kasir</td>
                <td>: {{ $sale->user->name ?? 'Admin' }}</td>
            </tr>
            <tr>
                <td>Pelanggan</td>
                <td>: {{ $sale->customer_name }}</td>
            </tr>
        </table>
        
        <div class="divider"></div>
        
        <div class="items" style="font-size: 11px;">
            @foreach($sale->saleItems as $item)
            <div class="mb-1">
                <span class="item-name">{{ $item->product->name ?? 'Produk Dihapus' }}</span>
                <div class="item-details">
                    <span>{{ $item->qty }} x {{ number_format($item->price - $item->discount, 0, ',', '.') }}</span>
                    <span class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="divider"></div>
        
        <table class="w-100 font-bold" style="font-size: 11px;">
            @if($sale->discount > 0)
            <tr>
                <td>Subtotal</td>
                <td class="text-right">{{ number_format($sale->total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Diskon</td>
                <td class="text-right">-{{ number_format($sale->discount, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($sale->tax > 0)
            <tr>
                <td>Pajak (11%)</td>
                <td class="text-right">{{ number_format($sale->tax, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr>
                <td style="font-size: 13px; padding-top: 5px;">TOTAL</td>
                <td class="text-right" style="font-size: 13px; padding-top: 5px;">{{ number_format($sale->grand_total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="padding-top: 5px;">Bayar ({{ ucfirst($sale->payment_method) }})</td>
                <td class="text-right" style="padding-top: 5px;">{{ number_format($sale->paid_amount, 0, ',', '.') }}</td>
            </tr>
            @if($sale->change_amount > 0)
            <tr>
                <td>Kembali</td>
                <td class="text-right">{{ number_format($sale->change_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
        </table>
        
        <div class="divider"></div>
        
        <div class="text-center mt-2" style="font-size: 11px;">
            <p style="margin: 3px 0;">Terima Kasih</p>
            <p style="margin: 3px 0;">Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.</p>
        </div>
        
    </div>

    <script>
        // Auto print on load
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>

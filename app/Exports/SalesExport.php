<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return Sale::with('user')
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'No. Invoice',
            'Pelanggan',
            'Kasir',
            'Metode Pembayaran',
            'Subtotal',
            'Diskon',
            'PPN',
            'Grand Total',
            'Status'
        ];
    }

    public function map($sale): array
    {
        return [
            $sale->created_at->format('d-m-Y H:i'),
            $sale->invoice_no,
            $sale->customer_name,
            $sale->user->name ?? '-',
            strtoupper($sale->payment_method),
            $sale->total,
            $sale->discount,
            $sale->tax,
            $sale->grand_total,
            $sale->status,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

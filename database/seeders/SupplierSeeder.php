<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name'    => 'PT. Teknologi Nusantara',
                'address' => 'Jl. Sudirman No. 45, Jakarta Pusat',
                'phone'   => '021-5551234',
                'email'   => 'info@teknus.co.id',
            ],
            [
                'name'    => 'CV. Mitra Komputer',
                'address' => 'Jl. Mangga Dua Raya No. 12, Jakarta Barat',
                'phone'   => '021-6227890',
                'email'   => 'order@mitrakomputer.com',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::firstOrCreate(['email' => $supplier['email']], $supplier);
        }
    }
}

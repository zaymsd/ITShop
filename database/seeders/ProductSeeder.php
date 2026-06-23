<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $laptop    = Category::where('slug', 'laptop')->first();
        $aksesoris = Category::where('slug', 'aksesoris')->first();
        $komponen  = Category::where('slug', 'komponen')->first();

        $asus    = Brand::where('name', 'Asus')->first();
        $lenovo  = Brand::where('name', 'Lenovo')->first();
        $logitech = Brand::where('name', 'Logitech')->first();

        $supplier1 = Supplier::first();

        $products = [
            [
                'name'        => 'ASUS VivoBook 14 A1404',
                'sku'         => 'ASU-VB14-A1404',
                'barcode'     => '8992399123456',
                'buy_price'   => 7500000,
                'sell_price'  => 8999000,
                'stock'       => 15,
                'min_stock'   => 3,
                'specs'       => 'Intel Core i5-1235U, 8GB RAM, 512GB SSD, 14" FHD, Windows 11',
                'category_id' => $laptop->id,
                'brand_id'    => $asus->id,
                'supplier_id' => $supplier1->id,
            ],
            [
                'name'        => 'Lenovo IdeaPad Slim 3',
                'sku'         => 'LNV-IPS3-15',
                'barcode'     => '8992399234567',
                'buy_price'   => 6200000,
                'sell_price'  => 7499000,
                'stock'       => 10,
                'min_stock'   => 3,
                'specs'       => 'AMD Ryzen 5 7520U, 8GB RAM, 256GB SSD, 15.6" FHD, Windows 11',
                'category_id' => $laptop->id,
                'brand_id'    => $lenovo->id,
                'supplier_id' => $supplier1->id,
            ],
            [
                'name'        => 'Logitech MX Master 3',
                'sku'         => 'LOG-MXM3-BLK',
                'barcode'     => '8992399345678',
                'buy_price'   => 750000,
                'sell_price'  => 999000,
                'stock'       => 25,
                'min_stock'   => 5,
                'specs'       => 'Wireless Mouse, 4000 DPI, Ergonomic, USB-C Charging',
                'category_id' => $aksesoris->id,
                'brand_id'    => $logitech->id,
                'supplier_id' => $supplier1->id,
            ],
            [
                'name'        => 'Logitech K380 Bluetooth Keyboard',
                'sku'         => 'LOG-K380-BLU',
                'barcode'     => '8992399456789',
                'buy_price'   => 350000,
                'sell_price'  => 499000,
                'stock'       => 20,
                'min_stock'   => 5,
                'specs'       => 'Bluetooth Multi-Device Keyboard, Compact, Cross Platform',
                'category_id' => $aksesoris->id,
                'brand_id'    => $logitech->id,
                'supplier_id' => $supplier1->id,
            ],
            [
                'name'        => 'SSD Kingston NV2 500GB M.2 NVMe',
                'sku'         => 'KNG-NV2-500',
                'barcode'     => '8992399567890',
                'buy_price'   => 420000,
                'sell_price'  => 599000,
                'stock'       => 30,
                'min_stock'   => 8,
                'specs'       => '500GB M.2 2280 NVMe, Read 3500MB/s, Write 2100MB/s',
                'category_id' => $komponen->id,
                'brand_id'    => $asus->id,  // using asus as placeholder brand
                'supplier_id' => $supplier1->id,
            ],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(['sku' => $product['sku']], $product);
        }
    }
}

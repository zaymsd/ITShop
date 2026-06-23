<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $product = App\Models\Product::create([
        'name' => 'Test Product',
        'sku' => 'TEST-002',
        'barcode' => null,
        'buy_price' => 1000,
        'sell_price' => 2000,
        'stock' => 10,
        'min_stock' => 5,
        'category_id' => 1,
        'brand_id' => 1,
        'supplier_id' => null,
    ]);
    echo "Success: " . $product->id;
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pivot table with extra attributes for the Product <-> Sale many-to-many.
     * Using a dedicated SaleItem model (not just a pivot) to support Observers for stock management.
     */
    public function up(): void
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('restrict');
            $table->integer('qty');
            $table->decimal('price', 12, 2);       // unit sell price at time of sale
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2);    // (price * qty) - discount
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->decimal('cost_price', 10, 2)->change();
            $table->decimal('stock_quantity', 10, 2)->default(0)->change();
            $table->decimal('min_stock_alert', 10, 2)->default(0)->change();
        });

        Schema::table('material_product', function (Blueprint $table) {
            $table->decimal('quantity', 10, 2)->change();
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->decimal('cost_price', 10, 4)->change();
            $table->decimal('stock_quantity', 10, 4)->default(0)->change();
            $table->decimal('min_stock_alert', 10, 4)->default(0)->change();
        });

        Schema::table('material_product', function (Blueprint $table) {
            $table->decimal('quantity', 10, 4)->change();
        });
    }
};

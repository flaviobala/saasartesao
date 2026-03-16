<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->integer('stock_quantity')->default(0)->change();
            $table->integer('min_stock_alert')->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->decimal('stock_quantity', 10, 2)->default(0)->change();
            $table->decimal('min_stock_alert', 10, 2)->default(0)->change();
        });
    }
};

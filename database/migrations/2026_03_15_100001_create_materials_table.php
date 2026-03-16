<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('unit'); // g, kg, cm, m, un, ml, l
            $table->decimal('cost_price', 10, 4); // preço de custo por unidade
            $table->decimal('stock_quantity', 10, 4)->default(0); // estoque atual
            $table->decimal('min_stock_alert', 10, 4)->default(0); // alerta de estoque mínimo
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index(['user_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};

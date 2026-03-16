<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabela pivot: receita de cada produto (quais materiais e quantidades)
        Schema::create('material_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 10, 4); // quantidade de material usada na receita
            $table->timestamps();

            $table->unique(['product_id', 'material_id']);
            $table->index('product_id');
            $table->index('material_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_product');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->string('customer_name')->nullable();
            $table->string('customer_contact')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2); // valor unitário na venda
            $table->decimal('total_price', 10, 2); // total = unit_price * quantity
            $table->decimal('cost_price_snapshot', 10, 4)->default(0); // custo no momento da venda
            $table->string('status')->default('pending'); // pending | completed | cancelled
            $table->text('notes')->nullable();
            $table->date('sold_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'sold_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};

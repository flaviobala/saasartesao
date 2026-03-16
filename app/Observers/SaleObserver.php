<?php

namespace App\Observers;

use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SaleObserver
{
    public function updating(Sale $sale): void
    {
        if (! $sale->isDirty('status')) {
            return;
        }

        $oldStatus = $sale->getOriginal('status');
        $newStatus = $sale->status;

        if ($newStatus === Sale::STATUS_COMPLETED && $oldStatus !== Sale::STATUS_COMPLETED) {
            $this->deductStock($sale);
        }

        if ($oldStatus === Sale::STATUS_COMPLETED && $newStatus !== Sale::STATUS_COMPLETED) {
            $this->restoreStock($sale);
        }
    }

    private function deductStock(Sale $sale): void
    {
        $product = $sale->product()->with('materials', 'user')->first();

        DB::transaction(function () use ($sale, $product) {
            foreach ($product->materials as $material) {
                $consumo = (int) round($material->pivot->quantity * $sale->quantity);
                $material->decrement('stock_quantity', $consumo);
                Log::info("Estoque abatido: {$material->name} -{$consumo} (venda #{$sale->id})");
            }

            // Atualiza snapshot do custo sem disparar o observer novamente
            Sale::withoutEvents(function () use ($sale, $product) {
                $sale->cost_price_snapshot = $product->calculateCost();
                $sale->saveQuietly();
            });
        });
    }

    private function restoreStock(Sale $sale): void
    {
        $product = $sale->product()->with('materials')->first();

        DB::transaction(function () use ($sale, $product) {
            foreach ($product->materials as $material) {
                $consumo = (int) round($material->pivot->quantity * $sale->quantity);
                $material->increment('stock_quantity', $consumo);
                Log::info("Estoque devolvido: {$material->name} +{$consumo} (venda #{$sale->id} revertida)");
            }
        });
    }
}

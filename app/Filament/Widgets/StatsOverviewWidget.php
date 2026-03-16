<?php

namespace App\Filament\Widgets;

use App\Models\Material;
use App\Models\Product;
use App\Models\Sale;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $userId = auth()->id();

        $totalVendasMes = Sale::where('user_id', $userId)
            ->where('status', Sale::STATUS_COMPLETED)
            ->whereMonth('sold_at', now()->month)
            ->whereYear('sold_at', now()->year)
            ->sum('total_price');

        $vendasPendentes = Sale::where('user_id', $userId)
            ->where('status', Sale::STATUS_PENDING)
            ->count();

        $faturamentoTotal = Sale::where('user_id', $userId)
            ->where('status', Sale::STATUS_COMPLETED)
            ->sum('total_price');

        $lucroTotal = Sale::where('user_id', $userId)
            ->where('status', Sale::STATUS_COMPLETED)
            ->selectRaw('SUM((unit_price - cost_price_snapshot) * quantity) as lucro')
            ->value('lucro') ?? 0;

        $materiaisEstoqueBaixo = Material::where('user_id', $userId)
            ->whereRaw('stock_quantity <= min_stock_alert')
            ->where('min_stock_alert', '>', 0)
            ->count();

        $totalProdutos = Product::where('user_id', $userId)
            ->where('is_active', true)
            ->count();

        return [
            Stat::make('Vendas este mês', 'R$ ' . number_format($totalVendasMes, 2, ',', '.'))
                ->description('Vendas concluídas no mês atual')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('success'),

            Stat::make('Faturamento Total', 'R$ ' . number_format($faturamentoTotal, 2, ',', '.'))
                ->description('Lucro: R$ ' . number_format($lucroTotal, 2, ',', '.'))
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('primary'),

            Stat::make('Vendas Pendentes', $vendasPendentes)
                ->description('Aguardando conclusão')
                ->descriptionIcon('heroicon-o-clock')
                ->color($vendasPendentes > 0 ? 'warning' : 'success'),

            Stat::make('Produtos Ativos', $totalProdutos)
                ->description('Cadastrados no sistema')
                ->descriptionIcon('heroicon-o-sparkles')
                ->color('info'),

            Stat::make('Estoque Baixo', $materiaisEstoqueBaixo)
                ->description($materiaisEstoqueBaixo > 0 ? 'Materiais abaixo do mínimo!' : 'Estoque OK')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($materiaisEstoqueBaixo > 0 ? 'danger' : 'success'),
        ];
    }
}

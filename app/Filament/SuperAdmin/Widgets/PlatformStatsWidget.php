<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Sale;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlatformStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalClientes = User::where('is_super_admin', false)->count();

        $novosMes = User::where('is_super_admin', false)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $faturamentoPlataforma = Sale::where('status', Sale::STATUS_COMPLETED)
            ->sum('total_price');

        $vendasMes = Sale::where('status', Sale::STATUS_COMPLETED)
            ->whereMonth('sold_at', now()->month)
            ->whereYear('sold_at', now()->year)
            ->sum('total_price');

        $totalVendas = Sale::where('status', Sale::STATUS_COMPLETED)->count();

        $vendasPendentes = Sale::where('status', Sale::STATUS_PENDING)->count();

        return [
            Stat::make('Total de Clientes', $totalClientes)
                ->description("{$novosMes} novos este mês")
                ->descriptionIcon('heroicon-o-user-plus')
                ->color('primary'),

            Stat::make('Faturamento da Plataforma', 'R$ ' . number_format($faturamentoPlataforma, 2, ',', '.'))
                ->description('R$ ' . number_format($vendasMes, 2, ',', '.') . ' este mês')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Total de Vendas Concluídas', $totalVendas)
                ->description("{$vendasPendentes} pendentes")
                ->descriptionIcon('heroicon-o-shopping-cart')
                ->color('warning'),
        ];
    }
}

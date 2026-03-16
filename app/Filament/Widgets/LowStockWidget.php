<?php

namespace App\Filament\Widgets;

use App\Models\Material;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Materiais com Estoque Baixo';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Material::query()
                    ->where('user_id', auth()->id())
                    ->whereRaw('stock_quantity <= min_stock_alert')
                    ->where('min_stock_alert', '>', 0)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Material'),
                Tables\Columns\TextColumn::make('unit')
                    ->label('Unidade'),
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Estoque Atual')
                    ->color('danger'),
                Tables\Columns\TextColumn::make('min_stock_alert')
                    ->label('Estoque Mínimo'),
            ])
            ->emptyStateHeading('Estoque OK!')
            ->emptyStateDescription('Nenhum material abaixo do mínimo.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}

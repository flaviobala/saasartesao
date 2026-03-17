<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestSalesWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected $columnSpan = 'full';
    protected static ?string $heading = 'Últimas Vendas';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Sale::query()
                    ->where('user_id', auth()->id())
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Produto'),
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Cliente')
                    ->default('—'),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Qtd'),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total')
                    ->money('BRL'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => Sale::STATUS_PENDING,
                        'success' => Sale::STATUS_COMPLETED,
                        'danger'  => Sale::STATUS_CANCELLED,
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        Sale::STATUS_PENDING   => 'Pendente',
                        Sale::STATUS_COMPLETED => 'Concluída',
                        Sale::STATUS_CANCELLED => 'Cancelada',
                        default                => $state,
                    }),
                Tables\Columns\TextColumn::make('sold_at')
                    ->label('Data')
                    ->date('d/m/Y')
                    ->default('—'),
            ]);
    }
}

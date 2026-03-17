<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\User;
use App\Models\Sale;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestUsersWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected $columnSpan = 'full';
    protected static ?string $heading = 'Clientes Recentes';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->where('is_super_admin', false)
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail'),
                Tables\Columns\TextColumn::make('craft_specialty')
                    ->label('Especialidade')
                    ->default('—'),
                Tables\Columns\TextColumn::make('products_count')
                    ->label('Produtos')
                    ->counts('products'),
                Tables\Columns\TextColumn::make('sales_count')
                    ->label('Vendas')
                    ->counts('sales'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Cadastrado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ]);
    }
}

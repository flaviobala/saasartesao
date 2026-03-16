<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaterialResource\Pages;
use App\Models\Material;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MaterialResource extends Resource
{
    protected static ?string $model = Material::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Materiais';
    protected static ?string $modelLabel = 'Material';
    protected static ?string $pluralModelLabel = 'Materiais';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informações do Material')->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2),
                Forms\Components\Select::make('category_id')
                    ->label('Categoria')
                    ->relationship('category', 'name', fn (Builder $query) => $query->where('user_id', auth()->id())->where('type', 'material'))
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Forms\Components\Select::make('unit')
                    ->label('Unidade')
                    ->options([
                        'un' => 'Unidade (un)',
                        'g'  => 'Grama (g)',
                        'kg' => 'Quilograma (kg)',
                        'ml' => 'Mililitro (ml)',
                        'l'  => 'Litro (l)',
                        'cm' => 'Centímetro (cm)',
                        'm'  => 'Metro (m)',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('cost_price')
                    ->label('Preço de Custo (por unidade)')
                    ->numeric()
                    ->prefix('R$')
                    ->required(),
                Forms\Components\TextInput::make('stock_quantity')
                    ->label('Estoque Atual')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('min_stock_alert')
                    ->label('Alerta de Estoque Mínimo')
                    ->numeric()
                    ->default(0),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nome')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category.name')->label('Categoria')->sortable(),
                Tables\Columns\TextColumn::make('unit')->label('Unidade'),
                Tables\Columns\TextColumn::make('cost_price')->label('Custo/Un')->money('BRL')->sortable(),
                Tables\Columns\TextColumn::make('stock_quantity')->label('Estoque')->sortable(),
                Tables\Columns\IconColumn::make('low_stock')
                    ->label('Estoque Baixo')
                    ->getStateUsing(fn (Material $record) => $record->isLowStock())
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Categoria')
                    ->relationship('category', 'name'),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMaterials::route('/'),
            'create' => Pages\CreateMaterial::route('/create'),
            'edit'   => Pages\EditMaterial::route('/{record}/edit'),
        ];
    }
}

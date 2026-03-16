<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Material;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationLabel = 'Produtos';
    protected static ?string $modelLabel = 'Produto';
    protected static ?string $pluralModelLabel = 'Produtos';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informações do Produto')->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome do Produto')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2),
                Forms\Components\Textarea::make('description')
                    ->label('Descrição')
                    ->columnSpan(2),
                Forms\Components\Select::make('category_id')
                    ->label('Categoria')
                    ->relationship('category', 'name', fn (Builder $query) => $query->where('user_id', auth()->id())->where('type', 'product'))
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Forms\Components\TextInput::make('labor_hours')
                    ->label('Horas de Mão de Obra')
                    ->numeric()
                    ->suffix('h')
                    ->default(0),
                Forms\Components\TextInput::make('profit_margin')
                    ->label('Margem de Lucro')
                    ->numeric()
                    ->suffix('%')
                    ->default(30),
                Forms\Components\Toggle::make('is_active')
                    ->label('Produto Ativo')
                    ->default(true),
                Forms\Components\FileUpload::make('image_url')
                    ->label('Foto do Produto')
                    ->image()
                    ->imageEditor()
                    ->directory('products')
                    ->visibility('public')
                    ->maxSize(2048)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->columnSpan(2),
            ])->columns(2),

            Forms\Components\Section::make('Receita (Materiais Usados)')->schema([
                Forms\Components\Repeater::make('recipe')
                    ->label('')
                    ->schema([
                        Forms\Components\Select::make('material_id')
                            ->label('Material')
                            ->options(fn () => Material::where('user_id', auth()->id())->pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('quantity')
                            ->label('Quantidade Usada')
                            ->numeric()
                            ->minValue(0.0001)
                            ->required(),
                    ])
                    ->columns(2)
                    ->addActionLabel('Adicionar Material')
                    ->default([]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')->label('Foto')->circular(),
                Tables\Columns\TextColumn::make('name')->label('Produto')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category.name')->label('Categoria'),
                Tables\Columns\TextColumn::make('labor_hours')->label('Horas M.O.')->suffix('h'),
                Tables\Columns\TextColumn::make('profit_margin')->label('Margem')->suffix('%'),
                Tables\Columns\IconColumn::make('is_active')->label('Ativo')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->label('Atualizado')->dateTime('d/m/Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Apenas Ativos'),
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
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleResource\Pages;
use App\Models\Sale;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Vendas';
    protected static ?string $modelLabel = 'Venda';
    protected static ?string $pluralModelLabel = 'Vendas';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Dados da Venda')->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Produto')
                    ->relationship('product', 'name', fn (Builder $query) => $query->where('user_id', auth()->id())->where('is_active', true))
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('customer_name')
                    ->label('Cliente')
                    ->maxLength(255),
                Forms\Components\TextInput::make('customer_contact')
                    ->label('Contato do Cliente')
                    ->maxLength(255),
                Forms\Components\TextInput::make('quantity')
                    ->label('Quantidade')
                    ->numeric()
                    ->default(1)
                    ->required(),
                Forms\Components\TextInput::make('unit_price')
                    ->label('Preço Unitário')
                    ->numeric()
                    ->prefix('R$')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        Sale::STATUS_PENDING   => 'Pendente',
                        Sale::STATUS_COMPLETED => 'Concluída',
                        Sale::STATUS_CANCELLED => 'Cancelada',
                    ])
                    ->default(Sale::STATUS_PENDING)
                    ->required(),
                Forms\Components\DatePicker::make('sold_at')
                    ->label('Data da Venda')
                    ->displayFormat('d/m/Y'),
                Forms\Components\Textarea::make('notes')
                    ->label('Observações')
                    ->columnSpan(2),
                Forms\Components\FileUpload::make('image_url')
                    ->label('Comprovante / Foto da Venda')
                    ->image()
                    ->imageEditor()
                    ->directory('sales')
                    ->visibility('public')
                    ->maxSize(2048)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->columnSpan(2),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')->label('Foto'),
                Tables\Columns\TextColumn::make('product.name')->label('Produto')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('customer_name')->label('Cliente')->searchable(),
                Tables\Columns\TextColumn::make('quantity')->label('Qtd'),
                Tables\Columns\TextColumn::make('unit_price')->label('Preço Unit.')->money('BRL')->sortable(),
                Tables\Columns\TextColumn::make('total_price')->label('Total')->money('BRL')->sortable(),
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
                Tables\Columns\TextColumn::make('sold_at')->label('Data')->date('d/m/Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        Sale::STATUS_PENDING   => 'Pendente',
                        Sale::STATUS_COMPLETED => 'Concluída',
                        Sale::STATUS_CANCELLED => 'Cancelada',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('completar')
                    ->label('Concluir')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Sale $record) => $record->status === Sale::STATUS_PENDING)
                    ->action(fn (Sale $record) => $record->update(['status' => Sale::STATUS_COMPLETED])),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSales::route('/'),
            'create' => Pages\CreateSale::route('/create'),
            'edit'   => Pages\EditSale::route('/{record}/edit'),
        ];
    }
}

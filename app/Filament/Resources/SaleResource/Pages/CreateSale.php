<?php
namespace App\Filament\Resources\SaleResource\Pages;

use App\Filament\Resources\SaleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSale extends CreateRecord
{
    protected static string $resource = SaleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id']    = auth()->id();
        $data['total_price'] = ($data['unit_price'] ?? 0) * ($data['quantity'] ?? 1);
        return $data;
    }
}

<?php
namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }

    protected function afterCreate(): void
    {
        $this->syncRecipe();
    }

    private function syncRecipe(): void
    {
        $recipe = $this->data['recipe'] ?? [];
        $sync = [];
        foreach ($recipe as $item) {
            if (!empty($item['material_id'])) {
                $sync[$item['material_id']] = ['quantity' => $item['quantity']];
            }
        }
        $this->record->materials()->sync($sync);
    }
}

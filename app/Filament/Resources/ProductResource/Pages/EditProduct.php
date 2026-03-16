<?php
namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['recipe'] = $this->record->materials->map(fn ($m) => [
            'material_id' => $m->id,
            'quantity'    => $m->pivot->quantity,
        ])->toArray();

        return $data;
    }

    protected function afterSave(): void
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

<?php
namespace App\Filament\Resources\MaterialResource\Pages;
use App\Filament\Resources\MaterialResource;
use Filament\Resources\Pages\CreateRecord;
class CreateMaterial extends CreateRecord {
    protected static string $resource = MaterialResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array {
        $data['user_id'] = auth()->id();
        return $data;
    }
}

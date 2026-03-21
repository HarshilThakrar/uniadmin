<?php

namespace App\Filament\Resources\ClientImageResource\Pages;

use App\Filament\Resources\ClientImageResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateClientImage extends CreateRecord
{
    protected static string $resource = ClientImageResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $images = $data['images'] ?? [];
        $firstModel = null;

        foreach ($images as $imagePath) {
            $model = static::getModel()::create([
                'image' => $imagePath,
                'is_active' => $data['is_active'] ?? true,
                'sort_order' => $data['sort_order'] ?? 999,
            ]);
            
            if (!$firstModel) {
                $firstModel = $model;
            }
        }

        return $firstModel ?: new (static::getModel())();
    }
}

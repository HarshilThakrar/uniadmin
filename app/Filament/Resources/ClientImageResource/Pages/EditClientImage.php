<?php

namespace App\Filament\Resources\ClientImageResource\Pages;

use App\Filament\Resources\ClientImageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClientImage extends EditRecord
{
    protected static string $resource = ClientImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

<?php

namespace App\Filament\Resources\BrochureRequestResource\Pages;

use App\Filament\Resources\BrochureRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBrochureRequest extends EditRecord
{
    protected static string $resource = BrochureRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

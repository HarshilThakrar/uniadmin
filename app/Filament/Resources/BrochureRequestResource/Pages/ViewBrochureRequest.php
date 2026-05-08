<?php

namespace App\Filament\Resources\BrochureRequestResource\Pages;

use App\Filament\Resources\BrochureRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBrochureRequest extends ViewRecord
{
    protected static string $resource = BrochureRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

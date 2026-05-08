<?php

namespace App\Filament\Resources\BrochureRequestResource\Pages;

use App\Filament\Resources\BrochureRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBrochureRequests extends ListRecords
{
    protected static string $resource = BrochureRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

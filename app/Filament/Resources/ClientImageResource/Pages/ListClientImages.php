<?php

namespace App\Filament\Resources\ClientImageResource\Pages;

use App\Filament\Resources\ClientImageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClientImages extends ListRecords
{
    protected static string $resource = ClientImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

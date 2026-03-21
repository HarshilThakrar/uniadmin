<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\PageVisit;
use Illuminate\Support\Facades\DB;

class PopularPagesTable extends BaseWidget
{
    protected static ?int $sort = 3;
    protected static ?string $pollingInterval = '3s';
    protected int | string | array $columnSpan = '1';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PageVisit::query()
                    ->fromSub(
                        PageVisit::query()
                            ->select('url', DB::raw('count(*) as views'), DB::raw('MIN(id) as id'))
                            ->groupBy('url'),
                        'sub'
                    )
            )
            ->defaultSort('views', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('url')
                    ->label('Page Path')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('views')
                    ->label('Total Views')
                    ->sortable(),
            ]);
    }
}

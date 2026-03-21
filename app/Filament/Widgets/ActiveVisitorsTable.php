<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\PageVisit;
use Illuminate\Support\Facades\DB;

class ActiveVisitorsTable extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = '3s';
    protected int | string | array $columnSpan = '1';
    protected static ?string $heading = 'Live Active Pages';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PageVisit::query()
                    ->fromSub(
                        PageVisit::query()
                            ->where('is_active', true)
                            ->where('created_at', '>=', now()->subHours(2)) // safety fallback
                            ->select('url', DB::raw('count(*) as active_users'), DB::raw('MIN(id) as id'))
                            ->groupBy('url'),
                        'sub'
                    )
            )
            ->defaultSort('active_users', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('url')
                    ->label('Current Page')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('active_users')
                    ->label('Active Users Online')
                    ->badge()
                    ->color('success')
                    ->sortable(),
            ]);
    }
}

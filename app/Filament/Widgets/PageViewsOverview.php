<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\PageVisit;
use Illuminate\Support\Facades\Cache;

class PageViewsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $stats = Cache::remember('dashboard_page_visit_stats', 30, function () {
            $totalViews = PageVisit::count();
            $uniqueVisitors = PageVisit::distinct('ip_address')->count('ip_address');
            $popularPage = PageVisit::select('url')
                ->selectRaw('count(*) as count')
                ->groupBy('url')
                ->orderByDesc('count')
                ->first();
            return compact('totalViews', 'uniqueVisitors', 'popularPage');
        });

        $totalViews = $stats['totalViews'];
        $uniqueVisitors = $stats['uniqueVisitors'];
        $popularPage = $stats['popularPage'];

        return [
            Stat::make('Total Page Views', number_format($totalViews))
                ->description('All time global hits')
                ->descriptionIcon('heroicon-m-eye')
                ->color('success'),
            Stat::make('Unique Visitors', number_format($uniqueVisitors))
                ->description('Distinct IP addresses')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            Stat::make('Most Popular Page', $popularPage ? $popularPage->url : 'N/A')
                ->description(($popularPage ? $popularPage->count : 0) . ' views')
                ->descriptionIcon('heroicon-m-fire')
                ->color('danger'),
        ];
    }
}

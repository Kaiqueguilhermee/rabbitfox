<?php

namespace App\Filament\Widgets;

use App\Models\GGRGamesFiver;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GGROverview extends BaseWidget
{
    protected function getStats(): array
    {
        $creditoGastos = GGRGamesFiver::sum('balance_bet');
        $totalPartidas = GGRGamesFiver::count();

        return [
            Stat::make('Total de Créditos Gastos', 'R$ ' . number_format($creditoGastos, 2, ',', '.'))
                ->description('Total de créditos gastos em jogos Fivers')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7,3,4,5,6,3,5,3]),
            Stat::make('Total de Partidas', $totalPartidas)
                ->description('Total de Partidas Fivers')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7,3,4,5,6,3,5,3]),
        ];
    }

    /**
     * @return bool
     */
    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin');
    }
}

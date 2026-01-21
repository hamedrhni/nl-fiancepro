<?php

namespace App\Filament\Widgets;

use App\Models\Accounting\Account;
use App\Utilities\Currency\CurrencyAccessor;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class DutchFinancialKpiWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        return [
            $this->getLiquidityStat(),
            $this->getSolvabilityStat(),
            $this->getProfitabilityStat(),
        ];
    }

    protected function getLiquidityStat(): Stat
    {
        // Current Ratio = Current Assets / Current Liabilities
        $currentAssets = Account::where('type', 'current_asset')->get()->sum(fn ($a) => (float) $a->ending_balance);
        $currentLiabilities = Account::where('type', 'current_liability')->get()->sum(fn ($a) => (float) $a->ending_balance);

        $ratio = $currentLiabilities > 0 ? round($currentAssets / $currentLiabilities, 2) : 0;
        $color = $ratio >= 1.5 ? 'success' : ($ratio >= 1.0 ? 'warning' : 'danger');

        return Stat::make('Current Ratio (Liquiditeit)', (string) $ratio)
            ->description('Target: > 1.5')
            ->descriptionIcon($ratio >= 1.5 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            ->color($color);
    }

    protected function getSolvabilityStat(): Stat
    {
        // Debt to Equity = Total Liabilities / Total Equity
        $totalLiabilities = Account::whereIn('category', ['liability'])->get()->sum(fn ($a) => (float) $a->ending_balance);
        $totalEquity = Account::where('category', 'equity')->get()->sum(fn ($a) => (float) $a->ending_balance);

        $ratio = $totalEquity > 0 ? round($totalLiabilities / $totalEquity, 2) : 0;
        $isHealthy = $ratio <= 2.0; // Standard healthy threshold for many industries

        return Stat::make('Solvabiliteit (Debt-to-Equity)', (string) $ratio)
            ->description($isHealthy ? 'Gezonde verhouding' : 'Aandacht vereist')
            ->descriptionIcon($isHealthy ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-circle')
            ->color($isHealthy ? 'info' : 'warning');
    }

    protected function getProfitabilityStat(): Stat
    {
        // Net Profit Margin = Net Income / Total Revenue
        $netIncome = Account::whereIn('category', ['revenue', 'expense'])->get()->sum(fn ($a) => (float) $a->ending_balance);
        $totalRevenue = Account::where('category', 'revenue')->get()->sum(fn ($a) => (float) $a->ending_balance);

        $margin = $totalRevenue > 0 ? round(($netIncome / $totalRevenue) * 100, 1) : 0;

        return Stat::make('Netto Winstmarge', $margin . '%')
            ->description('Winstgevendheid analyse')
            ->descriptionIcon('heroicon-m-chart-bar')
            ->color($margin > 0 ? 'success' : 'danger');
    }function () {}
}

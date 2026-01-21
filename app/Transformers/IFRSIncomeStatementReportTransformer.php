<?php

namespace App\Transformers;

use App\DTO\AccountDTO;
use App\DTO\ReportCategoryDTO;
use App\DTO\ReportDTO;
use App\Utilities\Currency\CurrencyAccessor;

class IFRSIncomeStatementReportTransformer extends IncomeStatementReportTransformer
{
    public function getTitle(): string
    {
        return 'IFRS Statement of Profit or Loss (Multi-step)';
    }

    public function getSummary(): array
    {
        $summary = parent::getSummary();
        
        // Add Operating Profit to summary
        $revenue = money($this->totalRevenue, CurrencyAccessor::getDefaultCurrency())->getAmount();
        $cogs = money($this->totalCogs, CurrencyAccessor::getDefaultCurrency())->getAmount();
        $grossProfit = $revenue - $cogs;
        
        $operatingExpenses = money($this->totalExpenses, CurrencyAccessor::getDefaultCurrency())->getAmount();
        $operatingProfit = $grossProfit - $operatingExpenses;
        
        $summary[] = [
            'label' => 'Operating Profit (EBIT)',
            'value' => money($operatingProfit, CurrencyAccessor::getDefaultCurrency(), true)->format(),
        ];
        
        return $summary;
    }

    public function getCategories(): array
    {
        // IFRS often requires specific classification:
        // Revenue
        // Cost of Sales
        // ----------------
        // Gross Profit
        // Other Income
        // Distribution Costs
        // Administrative Expenses
        // Other Expenses
        // ----------------
        // Results from Operating Activities
        // Finance Costs
        // Profit Before Tax
        // Income Tax Expense
        // ----------------
        // Profit for the Period
        
        return parent::getCategories(); 
    }
}

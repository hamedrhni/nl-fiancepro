<?php

namespace App\Transformers;

use App\DTO\AccountDTO;
use App\DTO\ReportCategoryDTO;
use App\DTO\ReportDTO;
use App\DTO\ReportTypeDTO;

class IFRSBalanceSheetReportTransformer extends BalanceSheetReportTransformer
{
    public function getTitle(): string
    {
        return 'IFRS Balance Sheet (Statement of Financial Position)';
    }

    public function getCategories(): array
    {
        $originalCategories = parent::getCategories();
        
        // Re-group for IFRS structure:
        // 1. Assets (Non-current, Current)
        // 2. Equity
        // 3. Liabilities (Non-current, Current)
        
        $ifrsCategories = [];
        
        // Assets section
        if (isset($originalCategories['Assets'])) {
            $ifrsCategories['Non-Current Assets'] = $this->extractGroup($originalCategories['Assets'], [
                'Long-Term Investments', 
                'Fixed Assets', 
                'Intangible Assets',
                'Other Non-Current Assets'
            ]);
            
            $ifrsCategories['Current Assets'] = $this->extractGroup($originalCategories['Assets'], [
                'Cash and Cash Equivalents', 
                'Receivables', 
                'Inventory', 
                'Prepaid and Deferred Charges',
                'Input Tax Recoverable',
                'Other Current Assets'
            ]);
        }
        
        // Equity section
        if (isset($originalCategories['Equity'])) {
            $ifrsCategories['Equity'] = $originalCategories['Equity'];
        }
        
        // Liabilities section
        if (isset($originalCategories['Liabilities'])) {
            $ifrsCategories['Non-Current Liabilities'] = $this->extractGroup($originalCategories['Liabilities'], [
                'Long-Term Borrowings', 
                'Deferred Tax Liabilities',
                'Other Long-Term Liabilities'
            ]);
            
            $ifrsCategories['Current Liabilities'] = $this->extractGroup($originalCategories['Liabilities'], [
                'Supplier Obligations', 
                'Accrued Expenses and Liabilities', 
                'Sales Taxes', 
                'Short-Term Borrowings',
                'Customer Deposits and Advances',
                'Other Current Liabilities'
            ]);
        }
        
        return $ifrsCategories;
    }

    protected function extractGroup(ReportCategoryDTO $category, array $typeNames): ReportCategoryDTO
    {
        $filteredTypes = [];
        $totalEndingBalance = 0;
        
        foreach ($typeNames as $name) {
            if (isset($category->types[$name])) {
                $filteredTypes[$name] = $category->types[$name];
                // Note: Simplified balance calculation for demonstration
            }
        }
        
        return new ReportCategoryDTO(
            header: $category->header,
            data: [], 
            summary: $category->summary,
            types: $filteredTypes
        );
    }
}

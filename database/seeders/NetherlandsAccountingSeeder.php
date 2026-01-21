<?php

namespace Database\Seeders;

use App\Models\Accounting\Adjustment;
use App\Models\Company;
use App\Models\Setting\Currency;
use Illuminate\Database\Seeder;

class NetherlandsAccountingSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            $this->setupDutchBtw($company);
            $this->setupDutchLocalization($company);
        }
    }

    protected function setupDutchBtw(Company $company): void
    {
        $btwRates = [
            ['name' => 'BTW 21% (Verkoop)', 'rate' => '21.00', 'type' => 'sales', 'category' => 'tax'],
            ['name' => 'BTW 9% (Verkoop)', 'rate' => '9.00', 'type' => 'sales', 'category' => 'tax'],
            ['name' => 'BTW 0% (Verkoop)', 'rate' => '0.00', 'type' => 'sales', 'category' => 'tax'],
            ['name' => 'BTW 21% (Inkoop)', 'rate' => '21.00', 'type' => 'purchase', 'category' => 'tax', 'recoverable' => true],
            ['name' => 'BTW 9% (Inkoop)', 'rate' => '9.00', 'type' => 'purchase', 'category' => 'tax', 'recoverable' => true],
        ];

        foreach ($btwRates as $rate) {
            Adjustment::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'name' => $rate['name']
                ],
                [
                    'rate' => $rate['rate'],
                    'type' => $rate['type'],
                    'category' => $rate['category'],
                    'recoverable' => $rate['recoverable'] ?? false,
                    'computation' => 'percentage',
                    'scope' => 'product',
                    'created_by' => $company->owner_id ?? 1,
                    'updated_by' => $company->owner_id ?? 1,
                ]
            );
        }
    }

    protected function setupDutchLocalization(Company $company): void
    {
        if ($company->locale) {
            $company->locale->update([
                'language' => 'nl',
                'timezone' => 'Europe/Amsterdam',
                'date_format' => 'd/m/Y', // d/m/Y backing value
                'number_format' => 'dot_comma', // dot_comma backing value
            ]);
        }
        
        // Ensure EUR is available and default
        Currency::updateOrCreate(
            ['code' => 'EUR'],
            [
                'name' => 'Euro',
                'symbol' => '€',
                'precision' => 2,
                'symbol_first' => true,
                'enabled' => true
            ]
        );
    }
}

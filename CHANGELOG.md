# Changelog

All notable changes to this fork of ERPSaaS will be documented in this file.

## [Portfolio Version] - 2026-01-20

### Added
- **IFRS Reporting Engine**: Created `IFRSBalanceSheetReportTransformer` to group assets and liabilities into Current/Non-current categories.
- **Netherlands Fiscal Seeder**: Added `NetherlandsAccountingSeeder` to automate the setup of Dutch BTW (VAT) rates (21%, 9%, 0%) and localization settings.
- **Professional Dutch Localization**: Overhauled `resources/data/lang/nl.json` with professional accounting terminology.
- **European Formatting**: Default currency set to EUR with `1.234,56` number formatting.

### Changed
- **README.md**: Completely redesigned to highlight accounting and technical expertise for job application purposes in the Netherlands.
- **Localization Settings**: Default timezone updated to `Europe/Amsterdam`.

### Infrastructure
- Forked from original [ERPSaaS](https://github.com/andrewdwallo/erpsaas) v1.x.
- Maintained MIT license compliance and attribution.

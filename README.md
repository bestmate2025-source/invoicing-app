# InvoiceDesk

A Laravel 11 + MySQL invoicing application with client CRUD, invoice line items, automatic invoice numbers, tax/discount totals, payment tracking, PDF downloads, and a Bootstrap dashboard.

## Setup

1. Install a fresh Laravel 11 application in this repository (or copy the standard Laravel skeleton files):
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   ```
2. Set `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in `.env` for MySQL.
3. Install the PDF dependency if needed: `composer require barryvdh/laravel-dompdf`.
4. Run `php artisan migrate` and start with `php artisan serve`.

The included application files are intentionally free of seed data. Invoice status is calculated from the amount paid when an invoice is saved; the displayed status therefore remains consistent with totals.

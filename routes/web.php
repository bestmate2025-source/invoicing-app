<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\DashboardController;

Route::get('/', DashboardController::class)->name('dashboard');
Route::resource('clients', ClientController::class);
Route::resource('invoices', InvoiceController::class)->except(['show'])->parameters(['invoices'=>'invoice']);
Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');

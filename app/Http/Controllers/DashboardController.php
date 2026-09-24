<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;

class DashboardController extends Controller
{
    public function __invoke() {
        $revenue = Invoice::where('status','paid')->sum('total');
        $outstanding = Invoice::whereIn('status',['unpaid','partial'])->get()->sum('balance_due');
        $invoiceCount = Invoice::count(); $clientCount = Client::count();
        $recentInvoices = Invoice::with('client')->latest()->limit(8)->get();
        return view('dashboard', compact('revenue','outstanding','invoiceCount','clientCount','recentInvoices'));
    }
}

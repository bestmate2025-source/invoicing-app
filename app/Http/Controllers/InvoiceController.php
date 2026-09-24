<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index() { $invoices = Invoice::with('client')->latest()->paginate(15); return view('invoices.index', compact('invoices')); }
    public function create() { return view('invoices.create', ['invoice'=>new Invoice(['issue_date'=>today(),'tax_rate'=>0,'discount'=>0]), 'clients'=>Client::orderBy('name')->get()]); }
    public function store(Request $request) { return $this->save($request, new Invoice); }
    public function show(Invoice $invoice) { $invoice->load(['client','items']); return view('invoices.show', compact('invoice')); }
    public function edit(Invoice $invoice) { $invoice->load('items'); return view('invoices.edit', ['invoice'=>$invoice,'clients'=>Client::orderBy('name')->get()]); }
    public function update(Request $request, Invoice $invoice) { return $this->save($request, $invoice); }
    public function destroy(Invoice $invoice) { $invoice->delete(); return redirect()->route('invoices.index')->with('success','Invoice deleted.'); }
    public function pdf(Invoice $invoice) { $invoice->load(['client','items']); return Pdf::loadView('invoices.pdf', compact('invoice'))->download($invoice->invoice_number.'.pdf'); }

    private function save(Request $request, Invoice $invoice) {
        $data = $request->validate(['client_id'=>'required|exists:clients,id','issue_date'=>'required|date','due_date'=>'nullable|date|after_or_equal:issue_date','tax_rate'=>'required|numeric|min:0|max:100','discount'=>'required|numeric|min:0','amount_paid'=>'required|numeric|min:0','status'=>'required|in:unpaid,partial,paid','notes'=>'nullable|string','items'=>'required|array|min:1','items.*.description'=>'required|string|max:255','items.*.quantity'=>'required|numeric|min:.01','items.*.unit_price'=>'required|numeric|min:0']);
        DB::transaction(function () use ($invoice, $data) {
            $invoice->fill(collect($data)->except('items')->all());
            $invoice->save();
            $invoice->items()->delete();
            foreach ($data['items'] as $item) $invoice->items()->create($item);
            $invoice->load('items'); $invoice->recalculateTotals();
            $invoice->status = (float)$invoice->amount_paid <= 0 ? 'unpaid' : ((float)$invoice->amount_paid >= (float)$invoice->total ? 'paid' : 'partial');
            $invoice->save();
        });
        return redirect()->route('invoices.show', $invoice)->with('success','Invoice saved.');
    }
}

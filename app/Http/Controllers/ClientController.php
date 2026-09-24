<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index() { $clients = Client::withCount('invoices')->latest()->paginate(15); return view('clients.index', compact('clients')); }
    public function create() { return view('clients.create', ['client' => new Client]); }
    public function store(Request $request) { $client = Client::create($this->validated($request)); return redirect()->route('clients.show', $client)->with('success', 'Client created.'); }
    public function show(Client $client) { $client->load('invoices'); return view('clients.show', compact('client')); }
    public function edit(Client $client) { return view('clients.edit', compact('client')); }
    public function update(Request $request, Client $client) { $client->update($this->validated($request)); return redirect()->route('clients.show', $client)->with('success', 'Client updated.'); }
    public function destroy(Client $client) { $client->delete(); return redirect()->route('clients.index')->with('success', 'Client deleted.'); }
    private function validated(Request $request): array { return $request->validate(['name'=>'required|string|max:255','email'=>'nullable|email|max:255','phone'=>'nullable|string|max:50','company'=>'nullable|string|max:255','address'=>'nullable|string','tax_number'=>'nullable|string|max:100']); }
}

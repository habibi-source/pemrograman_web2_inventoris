<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['item', 'user']);

        if ($request->filled('type') && $request->type != 'all') {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('item', function ($item) use ($search) {
                    $item->where('name', 'like', "%{$search}%")
                        ->orWhere('item_code', 'like', "%{$search}%");
                })->orWhere('reference', 'like', "%{$search}%");
            });
        }

        $transactions = $query->latest()->paginate(10)->withQueryString();
        $items = Item::where('status', '!=', 'out_of_stock')->get();

        $incomingToday = Transaction::where('type', 'incoming')
            ->whereDate('created_at', today())->count();
        $outgoingToday = Transaction::where('type', 'outgoing')
            ->whereDate('created_at', today())->count();
        $pendingApprovals = Transaction::where('status', 'pending')->count();

        return view('transactions.index', compact(
            'transactions', 'items',
            'incomingToday', 'outgoingToday', 'pendingApprovals'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:incoming,outgoing',
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'location' => 'nullable|string|max:100',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';

        $transaction = Transaction::create($validated);

        $item = Item::find($validated['item_id']);
        if ($validated['type'] == 'incoming') {
            $item->increment('stock_level', $validated['quantity']);
        } else {
            $item->decrement('stock_level', $validated['quantity']);
        }

        $item->status = match (true) {
            $item->stock_level <= 0 => 'out_of_stock',
            $item->stock_level <= 10 => 'low_stock',
            default => 'in_stock',
        };
        $item->save();

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction recorded successfully.');
    }

    public function export()
    {
        $transactions = Transaction::with(['item', 'user'])->latest()->get();
        
        $filename = "transactions_export_" . date('Y-m-d_H-i-s') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Type', 'Item Code', 'Item Name', 'Quantity', 'Location', 'Reference', 'Status', 'Operator', 'Date'];

        $callback = function() use($transactions, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($transactions as $trx) {
                fputcsv($file, [
                    $trx->id,
                    $trx->type,
                    $trx->item->item_code ?? 'N/A',
                    $trx->item->name ?? 'N/A',
                    $trx->quantity,
                    $trx->location,
                    $trx->reference,
                    $trx->status,
                    $trx->user->name ?? 'System',
                    $trx->created_at->format('Y-m-d H:i:s')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function destroy(Transaction $transaction)
    {
        $item = $transaction->item;
        
        if ($item) {
            if ($transaction->type == 'incoming') {
                $item->decrement('stock_level', $transaction->quantity);
            } else {
                $item->increment('stock_level', $transaction->quantity);
            }

            $item->status = match (true) {
                $item->stock_level <= 0 => 'out_of_stock',
                $item->stock_level <= 10 => 'low_stock',
                default => 'in_stock',
            };
            $item->save();
        }

        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction deleted and stock reversed successfully.');
    }
}

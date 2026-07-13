<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalItems = Item::count();
        $lowStock = Item::where('status', 'low_stock')->count();
        $outOfStock = Item::where('status', 'out_of_stock')->count();
        $totalCategories = Category::count();

        $trxQuery = Transaction::with(['item', 'user']);

        if ($request->filled('from_date')) {
            $trxQuery->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $trxQuery->whereDate('created_at', '<=', $request->to_date);
        }

        $incomingToday = (clone $trxQuery)->where('type', 'incoming')
            ->whereDate('created_at', today())->count();
        $outgoingToday = (clone $trxQuery)->where('type', 'outgoing')
            ->whereDate('created_at', today())->count();

        $recentTransactions = (clone $trxQuery)->latest()->take(5)->get();

        $lowStockItems = Item::with('category')
            ->whereIn('status', ['low_stock', 'out_of_stock'])
            ->take(5)->get();

        $chartData = $this->getChartData($request);

        return view('dashboard.index', compact(
            'totalItems', 'lowStock', 'outOfStock', 'totalCategories',
            'incomingToday', 'outgoingToday',
            'recentTransactions', 'lowStockItems', 'chartData'
        ));
    }

    private function getChartData($request)
    {
        $days = [];
        $incoming = [];
        $outgoing = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $days[] = $date->format('D');

            $inq = Transaction::where('type', 'incoming')
                ->whereDate('created_at', $date);
            $out = Transaction::where('type', 'outgoing')
                ->whereDate('created_at', $date);

            if ($request->filled('from_date')) {
                $inq->whereDate('created_at', '>=', $request->from_date);
                $out->whereDate('created_at', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $inq->whereDate('created_at', '<=', $request->to_date);
                $out->whereDate('created_at', '<=', $request->to_date);
            }

            $incoming[] = $inq->count();
            $outgoing[] = $out->count();
        }

        return compact('days', 'incoming', 'outgoing');
    }

    public function export(Request $request)
    {
        $query = Item::with('category');

        if ($request->filled('from_date')) {
            $query->whereHas('transactions', function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->from_date);
            });
        }
        if ($request->filled('to_date')) {
            $query->whereHas('transactions', function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->to_date);
            });
        }

        $items = $query->get();
        
        $filename = "inventory_report_" . date('Y-m-d_H-i-s') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Item Code', 'Name', 'Category', 'Unit Price', 'Stock Level', 'Status'];

        $callback = function() use($items, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($items as $item) {
                fputcsv($file, [
                    $item->item_code,
                    $item->name,
                    $item->category->name ?? 'N/A',
                    'Rp ' . number_format($item->unit_price, 0, ',', '.'),
                    $item->stock_level,
                    $item->status
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

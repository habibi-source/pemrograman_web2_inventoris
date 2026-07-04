<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index()
    {
        $totalItems = Item::count();
        $lowStock = Item::where('status', 'low_stock')->count();
        $outOfStock = Item::where('status', 'out_of_stock')->count();
        $totalCategories = Category::count();

        $incomingToday = Transaction::where('type', 'incoming')
            ->whereDate('created_at', today())->count();
        $outgoingToday = Transaction::where('type', 'outgoing')
            ->whereDate('created_at', today())->count();

        $recentTransactions = Transaction::with(['item', 'user'])
            ->latest()->take(5)->get();

        $lowStockItems = Item::with('category')
            ->whereIn('status', ['low_stock', 'out_of_stock'])
            ->take(5)->get();

        $chartData = $this->getChartData();

        return view('dashboard.index', compact(
            'totalItems', 'lowStock', 'outOfStock', 'totalCategories',
            'incomingToday', 'outgoingToday',
            'recentTransactions', 'lowStockItems', 'chartData'
        ));
    }

    private function getChartData()
    {
        $days = [];
        $incoming = [];
        $outgoing = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $days[] = $date->format('D');
            $incoming[] = Transaction::where('type', 'incoming')
                ->whereDate('created_at', $date)->count();
            $outgoing[] = Transaction::where('type', 'outgoing')
                ->whereDate('created_at', $date)->count();
        }

        return compact('days', 'incoming', 'outgoing');
    }

    public function export()
    {
        $items = Item::with('category')->get();
        
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
                    $item->unit_price,
                    $item->stock_level,
                    $item->status
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

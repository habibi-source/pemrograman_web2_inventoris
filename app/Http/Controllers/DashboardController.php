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
}

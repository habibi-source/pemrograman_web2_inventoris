<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['item', 'user']);

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        if ($request->filled('type') && $request->type != 'all') {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transactions = $query->latest()->paginate(10)->withQueryString();

        $totalSku = Item::count();
        $activeShipments = Transaction::whereIn('status', ['in_transit', 'pending'])->count();
        $inbound = Transaction::where('status', 'in_transit')
            ->where('type', 'incoming')->count();
        $outbound = Transaction::where('status', 'in_transit')
            ->where('type', 'outgoing')->count();

        $lowStockAlerts = Item::with('category')
            ->whereIn('status', ['low_stock', 'out_of_stock'])
            ->get();

        $chartData = $this->getMonthlyChartData();

        return view('reports.index', compact(
            'transactions', 'totalSku', 'activeShipments',
            'inbound', 'outbound', 'lowStockAlerts', 'chartData'
        ));
    }

    private function getMonthlyChartData()
    {
        $months = [];
        $incoming = [];
        $outgoing = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->startOfMonth()->subMonths($i);
            $months[] = $date->format('M');

            $incoming[] = Transaction::where('type', 'incoming')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $outgoing[] = Transaction::where('type', 'outgoing')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        return compact('months', 'incoming', 'outgoing');
    }
}

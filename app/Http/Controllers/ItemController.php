<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with('category');

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('item_code', 'like', "%{$search}%");
            });
        }

        $items = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::all();

        return view('items.index', compact('items', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_code' => 'required|string|max:50|unique:items',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_price' => 'required|numeric|min:0',
            'stock_level' => 'required|integer|min:0',
            'status' => 'required|in:in_stock,low_stock,out_of_stock',
        ]);

        Item::create($validated);

        return redirect()->route('items.index')->with('success', 'Item created successfully.');
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'item_code' => 'required|string|max:50|unique:items,item_code,'.$item->id,
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_price' => 'required|numeric|min:0',
            'stock_level' => 'required|integer|min:0',
            'status' => 'required|in:in_stock,low_stock,out_of_stock',
        ]);

        $item->update($validated);

        return redirect()->route('items.index')->with('success', 'Item updated successfully.');
    }

    public function destroy(Item $item)
    {
        $item->delete();

        return redirect()->route('items.index')->with('success', 'Item deleted successfully.');
    }

    public function export()
    {
        $items = Item::with('category')->latest()->get();
        
        $filename = "inventory_items_" . date('Y-m-d_H-i-s') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Item Code', 'Name', 'Category', 'Unit Price', 'Stock Level', 'Status', 'Last Updated'];

        $callback = function() use($items, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($items as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->item_code,
                    $item->name,
                    $item->category->name ?? 'N/A',
                    'Rp ' . number_format($item->unit_price, 0, ',', '.'),
                    $item->stock_level,
                    $item->status,
                    $item->updated_at->format('Y-m-d H:i:s')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

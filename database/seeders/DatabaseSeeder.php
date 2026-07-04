<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@logisticspro.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Staff User',
            'email' => 'staff@logisticspro.com',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'status' => 'active',
        ]);

        $categories = [
            ['name' => 'Electronics', 'slug' => 'electronics'],
            ['name' => 'Furniture', 'slug' => 'furniture'],
            ['name' => 'Apparel', 'slug' => 'apparel'],
            ['name' => 'Industrial', 'slug' => 'industrial'],
            ['name' => 'Office Supplies', 'slug' => 'office-supplies'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        $items = [
            ['item_code' => 'ITM-4820-X', 'name' => 'Ergonomic Steel Office Desk', 'category_id' => 2, 'unit_price' => 429.00, 'stock_level' => 142, 'status' => 'in_stock'],
            ['item_code' => 'ITM-9211-A', 'name' => 'Wireless Mesh Node v2', 'category_id' => 1, 'unit_price' => 89.50, 'stock_level' => 8, 'status' => 'low_stock'],
            ['item_code' => 'ITM-3304-Y', 'name' => 'Heavy Duty Pallet Jack', 'category_id' => 4, 'unit_price' => 1240.00, 'stock_level' => 34, 'status' => 'in_stock'],
            ['item_code' => 'ITM-1025-Q', 'name' => 'Laser Scanning Hub 500', 'category_id' => 1, 'unit_price' => 2199.99, 'stock_level' => 12, 'status' => 'in_stock'],
            ['item_code' => 'ITM-6623-W', 'name' => 'Industrial Grade Shelving', 'category_id' => 4, 'unit_price' => 185.00, 'stock_level' => 0, 'status' => 'out_of_stock'],
            ['item_code' => 'ITM-7731-Z', 'name' => 'Standing Desk Converter', 'category_id' => 2, 'unit_price' => 349.00, 'stock_level' => 67, 'status' => 'in_stock'],
            ['item_code' => 'ITM-8842-B', 'name' => 'Mechanical Keyboard K95', 'category_id' => 1, 'unit_price' => 199.99, 'stock_level' => 3, 'status' => 'low_stock'],
            ['item_code' => 'ITM-5513-M', 'name' => 'Safety Goggles (Pack of 10)', 'category_id' => 4, 'unit_price' => 24.99, 'stock_level' => 500, 'status' => 'in_stock'],
            ['item_code' => 'ITM-2289-P', 'name' => 'Executive Office Chair', 'category_id' => 2, 'unit_price' => 849.00, 'stock_level' => 21, 'status' => 'in_stock'],
            ['item_code' => 'ITM-4476-K', 'name' => 'USB-C Hub Multiport', 'category_id' => 1, 'unit_price' => 45.00, 'stock_level' => 0, 'status' => 'out_of_stock'],
            ['item_code' => 'APP-M3-SILVER', 'name' => 'MacBook Pro 14" M3', 'category_id' => 1, 'unit_price' => 1999.00, 'stock_level' => 55, 'status' => 'in_stock'],
            ['item_code' => 'LOGI-MXM3S', 'name' => 'Logitech MX Master 3S', 'category_id' => 1, 'unit_price' => 99.99, 'stock_level' => 120, 'status' => 'in_stock'],
            ['item_code' => 'FUR-DESK-ERGO', 'name' => 'Ergonomic Workstation Desk', 'category_id' => 2, 'unit_price' => 599.00, 'stock_level' => 18, 'status' => 'in_stock'],
            ['item_code' => 'DRI-P-90-22', 'name' => 'Industrial Power Drill Gen 2', 'category_id' => 4, 'unit_price' => 299.00, 'stock_level' => 45, 'status' => 'in_stock'],
            ['item_code' => 'FAS-B-08-SS', 'name' => 'High-Tensile Steel Bolts (M8)', 'category_id' => 4, 'unit_price' => 0.50, 'stock_level' => 5000, 'status' => 'in_stock'],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }

        $users = User::all();
        $itemIds = Item::pluck('id')->toArray();

        Transaction::create(['type' => 'incoming', 'item_id' => 11, 'quantity' => 250, 'location' => 'Bay-A24-Shelf-2', 'reference' => 'PO-2024-001', 'user_id' => 1, 'status' => 'verified']);
        Transaction::create(['type' => 'outgoing', 'item_id' => 12, 'quantity' => 1200, 'location' => 'Shipment Dock 4', 'reference' => 'SO-2024-001', 'user_id' => 2, 'status' => 'shipped']);
        Transaction::create(['type' => 'incoming', 'item_id' => 14, 'quantity' => 15, 'location' => 'Inbound Queue', 'reference' => 'PO-2024-002', 'user_id' => 1, 'status' => 'pending']);
        Transaction::create(['type' => 'outgoing', 'item_id' => 1, 'quantity' => 45, 'location' => 'Warehouse B - Dock 1', 'reference' => 'SO-2024-002', 'user_id' => 2, 'status' => 'completed']);
        Transaction::create(['type' => 'incoming', 'item_id' => 8, 'quantity' => 1200, 'location' => 'Whse-4, Bin C-01', 'reference' => 'PO-2024-003', 'user_id' => 1, 'status' => 'completed']);
        Transaction::create(['type' => 'outgoing', 'item_id' => 5, 'quantity' => 500, 'location' => 'Whse-1, Loading Dock B', 'reference' => 'SO-2024-003', 'user_id' => 2, 'status' => 'completed']);
        Transaction::create(['type' => 'incoming', 'item_id' => 4, 'quantity' => 25, 'location' => 'Whse-2', 'reference' => 'TRF-2024-001', 'user_id' => 1, 'status' => 'in_transit']);
        Transaction::create(['type' => 'outgoing', 'item_id' => 3, 'quantity' => 12, 'location' => 'Warehouse Transfer', 'reference' => 'SO-2024-004', 'user_id' => 2, 'status' => 'completed']);
        Transaction::create(['type' => 'outgoing', 'item_id' => 14, 'quantity' => 2, 'location' => 'Return to Vendor', 'reference' => 'RET-2024-001', 'user_id' => 2, 'status' => 'damaged']);
        Transaction::create(['type' => 'incoming', 'item_id' => 13, 'quantity' => 30, 'location' => 'Whse-3, Bin B-12', 'reference' => 'PO-2024-004', 'user_id' => 1, 'status' => 'verified']);
    }
}

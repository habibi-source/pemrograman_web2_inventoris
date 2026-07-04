<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Item;
use App\Models\Transaction;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        View::composer('components.top-nav', function ($view) {
            $notifications = [];
            
            $lowStock = Item::whereIn('status', ['low_stock', 'out_of_stock'])->count();
            if ($lowStock > 0) {
                $notifications[] = [
                    'icon' => 'fa-exclamation-triangle text-danger',
                    'text' => "$lowStock items are currently running low on stock or out of stock.",
                    'time' => 'Action required',
                    'link' => route('reports.index')
                ];
            }

            $pendingTrx = Transaction::whereIn('status', ['pending', 'in_transit'])->count();
            if ($pendingTrx > 0) {
                $notifications[] = [
                    'icon' => 'fa-clipboard-list text-warning',
                    'text' => "$pendingTrx transactions are currently pending or in-transit.",
                    'time' => 'Action required',
                    'link' => route('transactions.index')
                ];
            }

            $view->with('notifications', collect($notifications));
        });
    }
}

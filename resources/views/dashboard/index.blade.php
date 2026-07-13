@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                <li class="breadcrumb-item active">Overview</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-0">Operations Dashboard</h2>
        <p class="text-muted">Real-time inventory overview.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('dashboard.export') }}" class="btn btn-primary btn-sm px-3">
            <i class="fas fa-download me-1"></i>Export Report
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="bg-primary bg-opacity-10 p-2 rounded">
                        <i class="fas fa-boxes text-primary"></i>
                    </div>
                    <span class="badge bg-success-subtle text-success">
                        <i class="fas fa-arrow-up me-1"></i>+2.4%
                    </span>
                </div>
                <p class="text-muted small mb-1">Total Items</p>
                <h3 class="fw-bold mb-0">{{ number_format($totalItems) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="bg-danger bg-opacity-10 p-2 rounded">
                        <i class="fas fa-exclamation-triangle text-danger"></i>
                    </div>
                </div>
                <p class="text-muted small mb-1">Low Stock Alerts</p>
                <h3 class="fw-bold text-danger mb-0">{{ $lowStock + $outOfStock }}</h3>
                <small class="text-muted">Immediate action needed</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="bg-success bg-opacity-10 p-2 rounded">
                        <i class="fas fa-arrow-down text-success"></i>
                    </div>
                    <span class="badge bg-primary-subtle text-primary">ERP active</span>
                </div>
                <p class="text-muted small mb-1">Incoming Today</p>
                <h3 class="fw-bold mb-0">{{ $incomingToday }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="bg-warning bg-opacity-10 p-2 rounded">
                        <i class="fas fa-tags text-warning"></i>
                    </div>
                </div>
                <p class="text-muted small mb-1">Categories</p>
                <h3 class="fw-bold mb-0">{{ $totalCategories }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-0">Stock Movement</h5>
                        <small class="text-muted">Inventory flow over the last 7 days</small>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="d-flex align-items-center gap-1">
                            <span class="badge bg-primary p-2"></span>
                            <small class="text-muted">Incoming</small>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="badge bg-success p-2"></span>
                            <small class="text-muted">Outgoing</small>
                        </div>
                    </div>
                </div>
                <div style="position: relative; height: 250px; width: 100%;">
                    <canvas id="stockChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-danger-subtle mb-3">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="fas fa-exclamation-circle text-danger fs-4"></i>
                    <h5 class="fw-bold mb-0 text-danger">Low Stock Alerts</h5>
                </div>
                <p class="small text-muted mb-3">
                    {{ $lowStock + $outOfStock }} SKUs have fallen below minimum threshold levels.
                </p>
                <a href="{{ route('items.index', ['status' => 'low_stock']) }}" class="btn btn-danger btn-sm w-100">
                    Review All Alerts
                </a>
            </div>
        </div>

    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0">Recent Transactions</h5>
            <small class="text-muted">Latest inventory activities and stock adjustments</small>
        </div>
        <a href="{{ route('transactions.index') }}" class="btn btn-link btn-sm text-primary p-0">
            View All Records
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Transaction ID</th>
                    <th>Item / SKU</th>
                    <th class="text-end">Quantity</th>
                    <th>Type</th>
                    <th>Operator</th>
                    <th>Status</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTransactions as $trx)
                <tr>
                    <td><span class="text-muted">#TRX-{{ $trx->id }}</span></td>
                    <td>
                        <span class="fw-semibold">{{ $trx->item->name ?? 'N/A' }}</span>
                        <br><small class="text-muted">{{ $trx->item->item_code ?? '' }}</small>
                    </td>
                    <td class="text-end fw-bold {{ $trx->type == 'incoming' ? 'text-success' : 'text-danger' }}">
                        {{ $trx->type == 'incoming' ? '+' : '-' }}{{ number_format($trx->quantity) }}
                    </td>
                    <td>
                        <span class="badge {{ $trx->type == 'incoming' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                            {{ ucfirst($trx->type) }}
                        </span>
                    </td>
                    <td>{{ $trx->user->name ?? 'System' }}</td>
                    <td>
                        <span class="badge {{ $trx->status == 'completed' || $trx->status == 'verified' ? 'bg-success' : ($trx->status == 'pending' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                            {{ ucfirst($trx->status) }}
                        </span>
                    </td>
                    <td class="text-muted">{{ $trx->created_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">No transactions yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="restockModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('transactions.store') }}">
            @csrf
            <input type="hidden" name="type" value="incoming">
            <div class="modal-content">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Quick Restock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small text-uppercase">Select Low Stock Item</label>
                        <select name="item_id" class="form-select" required>
                            <option value="">-- Choose Item --</option>
                            @foreach($lowStockItems as $item)
                                <option value="{{ $item->id }}">{{ $item->item_code }} - {{ $item->name }} (Current: {{ $item->stock_level }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small text-uppercase">Restock Quantity</label>
                        <input type="number" name="quantity" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small text-uppercase">Notes</label>
                        <input type="text" name="notes" class="form-control" placeholder="Optional notes">
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Restock</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('stockChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartData['days']),
                datasets: [
                    {
                        label: 'Incoming',
                        data: @json($chartData['incoming']),
                        backgroundColor: '#003c90',
                        borderRadius: 4,
                    },
                    {
                        label: 'Outgoing',
                        data: @json($chartData['outgoing']),
                        backgroundColor: '#006c49',
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#e5eeff' } },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>
@endpush

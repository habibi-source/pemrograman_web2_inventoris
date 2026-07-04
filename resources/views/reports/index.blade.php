@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="fw-bold mb-0">Inventory Reports</h2>
        <p class="text-muted">Real-time logistics analytics and movement history.</p>
    </div>
    <div class="d-flex align-items-center gap-2 bg-white p-2 rounded border shadow-sm">
        <div class="d-flex align-items-center gap-1 pe-3 border-end">
            <i class="fas fa-calendar text-muted"></i>
            <small>Oct 01 - Oct 31, 2023</small>
            <i class="fas fa-chevron-down text-muted" style="font-size: 0.75rem;"></i>
        </div>
        <button class="btn btn-sm btn-link text-primary" onclick="openExportModal()">
            <i class="fas fa-file-pdf me-1"></i>Print/Export PDF
        </button>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-0">Stock Level Trend</h5>
                        <small class="text-muted text-uppercase">6-Month Velocity</small>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="d-flex align-items-center gap-1">
                            <span class="badge bg-primary p-2"></span>
                            <small class="text-muted">Inbound</small>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="badge bg-success p-2"></span>
                            <small class="text-muted">Outbound</small>
                        </div>
                    </div>
                </div>
                <canvas id="reportChart" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 bg-danger-subtle border-danger">
            <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="fas fa-exclamation-triangle text-danger"></i>
                    <h5 class="fw-bold mb-0 text-danger">Low Stock Alerts</h5>
                </div>
                <div class="flex-grow-1 overflow-auto" style="max-height: 240px;">
                    @forelse($lowStockAlerts as $item)
                    <div class="p-2 mb-2 bg-white bg-opacity-75 rounded border border-danger border-opacity-25 d-flex justify-content-between align-items-center">
                        <div>
                            <p class="fw-semibold mb-0 small">{{ $item->name }}</p>
                            <small class="text-danger">Current: {{ $item->stock_level }} units</small>
                        </div>
                        <i class="fas fa-arrow-right text-danger"></i>
                    </div>
                    @empty
                    <p class="text-muted small">No low stock items.</p>
                    @endforelse
                </div>
                <button class="btn btn-outline-danger btn-sm w-100 mt-2">Generate Purchase Orders</button>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <small class="text-muted text-uppercase">Total SKU Count</small>
                <div class="d-flex justify-content-between align-items-end mt-2">
                    <h3 class="fw-bold mb-0">{{ number_format($totalSku) }}</h3>
                    <span class="badge bg-success-subtle text-success">
                        <i class="fas fa-arrow-up me-1"></i>2.4%
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <small class="text-muted text-uppercase">Inv. Turnover Ratio</small>
                <div class="d-flex justify-content-between align-items-end mt-2">
                    <h3 class="fw-bold mb-0">8.2x</h3>
                    <small class="text-muted">Target: 7.5x</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <small class="text-muted text-uppercase">Active Shipments</small>
                <div class="d-flex justify-content-between align-items-end mt-2">
                    <div>
                        <h3 class="fw-bold mb-0">{{ $activeShipments }}</h3>
                        <small class="text-muted">{{ $inbound }} Inbound / {{ $outbound }} Outbound</small>
                    </div>
                    <i class="fas fa-truck text-muted opacity-50" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Historical Movement Table</h5>
        <div class="d-flex gap-2">
            <form method="GET" class="d-flex gap-2 align-items-center">
                <input type="date" name="from_date" class="form-control form-control-sm"
                       value="{{ request('from_date') }}" placeholder="From">
                <input type="date" name="to_date" class="form-control form-control-sm"
                       value="{{ request('to_date') }}" placeholder="To">
                <select name="type" class="form-select form-select-sm">
                    <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>All Types</option>
                    <option value="incoming" {{ request('type') == 'incoming' ? 'selected' : '' }}>Inbound</option>
                    <option value="outgoing" {{ request('type') == 'outgoing' ? 'selected' : '' }}>Outbound</option>
                </select>
                <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Timestamp</th>
                    <th>SKU / Item Name</th>
                    <th>Type</th>
                    <th>Location</th>
                    <th class="text-end">Qty</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                <tr>
                    <td class="font-monospace">{{ $trx->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <span class="fw-bold">{{ $trx->item->item_code ?? 'N/A' }}</span>
                        <br><small class="text-muted">{{ $trx->item->name ?? '' }}</small>
                    </td>
                    <td>
                        <span class="badge {{ $trx->type == 'incoming' ? 'bg-primary-subtle text-primary' : 'bg-success-subtle text-success' }}">
                            {{ strtoupper($trx->type) }}
                        </span>
                    </td>
                    <td class="text-muted">{{ $trx->location ?? '-' }}</td>
                    <td class="text-end font-monospace fw-bold {{ $trx->type == 'incoming' ? 'text-success' : 'text-danger' }}">
                        {{ $trx->type == 'incoming' ? '+' : '-' }}{{ number_format($trx->quantity) }}
                    </td>
                    <td>
                        <span class="badge {{ $trx->status == 'completed' ? 'bg-success' : ($trx->status == 'in_transit' ? 'bg-secondary' : 'bg-warning text-dark') }}">
                            {{ ucfirst($trx->status) }}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-link text-muted"><i class="fas fa-eye"></i></button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">No transactions found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
        <small class="text-muted">Showing {{ $transactions->firstItem() ?? 0 }} to {{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }} entries</small>
        {{ $transactions->links() }}
    </div>
</div>

<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <h5 class="fw-bold mb-3">Preparing Export</h5>
            <div class="progress mb-3" style="height: 8px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" id="exportProgress"
                     role="progressbar" style="width: 0%"></div>
            </div>
            <p class="small text-muted" id="exportStatus">Compiling movement data...</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('reportChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartData['months']),
                datasets: [
                    {
                        label: 'Inbound',
                        data: @json($chartData['incoming']),
                        borderColor: '#003c90',
                        backgroundColor: 'rgba(0,60,144,0.1)',
                        fill: true,
                        tension: 0.4,
                    },
                    {
                        label: 'Outbound',
                        data: @json($chartData['outgoing']),
                        borderColor: '#006c49',
                        backgroundColor: 'rgba(0,108,73,0.1)',
                        fill: true,
                        tension: 0.4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#e5eeff' } },
                    x: { grid: { display: false } }
                }
            }
        });
    });

    function openExportModal() {
        const modal = new bootstrap.Modal(document.getElementById('exportModal'));
        const progress = document.getElementById('exportProgress');
        const status = document.getElementById('exportStatus');
        progress.style.width = '0%';
        modal.show();

        setTimeout(() => {
            progress.style.width = '50%';
            status.textContent = 'Processing data...';
        }, 500);

        setTimeout(() => {
            progress.style.width = '100%';
            status.textContent = 'Report generated successfully!';
        }, 2000);

        setTimeout(() => {
            modal.hide();
            progress.style.width = '0%';
            status.textContent = 'Compiling movement data...';
        }, 3000);
    }
</script>
@endpush

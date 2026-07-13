@extends('layouts.app')

@section('title', 'Transactions')

@section('content')
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h2 class="fw-bold mb-0">Stock Movement</h2>
        <p class="text-muted">Track and record real-time inventory flow.</p>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#movementModal">
        <i class="fas fa-exchange-alt me-1"></i>Record Movement
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <small class="text-muted text-uppercase">Incoming Today</small>
                <div class="d-flex justify-content-between align-items-end">
                    <h4 class="fw-bold text-success mb-0">{{ $incomingToday }}</h4>
                    <span class="badge bg-success-subtle text-success">+12%</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <small class="text-muted text-uppercase">Outgoing Today</small>
                <div class="d-flex justify-content-between align-items-end">
                    <h4 class="fw-bold text-danger mb-0">{{ $outgoingToday }}</h4>
                    <span class="badge bg-danger-subtle text-danger">-5%</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <small class="text-muted text-uppercase">Pending Approvals</small>
                <div class="d-flex justify-content-between align-items-end">
                    <h4 class="fw-bold mb-0">{{ $pendingApprovals }}</h4>
                    <i class="fas fa-clock text-muted"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <small class="text-muted text-uppercase">Net Change</small>
                <div class="d-flex justify-content-between align-items-end">
                    <h4 class="fw-bold text-primary mb-0">+{{ $incomingToday - $outgoingToday }}</h4>
                    <i class="fas fa-chart-line text-primary"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link {{ !request('type') || request('type') == 'all' ? 'active' : '' }}"
                   href="{{ route('transactions.index', ['type' => 'all']) }}">
                    <i class="fas fa-list me-1"></i>All Transactions
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('type') == 'incoming' ? 'active' : '' }}"
                   href="{{ route('transactions.index', ['type' => 'incoming']) }}">
                    <i class="fas fa-arrow-down text-success me-1"></i>Incoming
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('type') == 'outgoing' ? 'active' : '' }}"
                   href="{{ route('transactions.index', ['type' => 'outgoing']) }}">
                    <i class="fas fa-arrow-up text-danger me-1"></i>Outgoing
                </a>
            </li>
            <li class="nav-item ms-auto d-flex align-items-center gap-2">
                <button class="btn btn-sm btn-outline-secondary" type="button" onclick="toggleFilter()">
                    <i class="fas fa-filter me-1"></i>Filter
                </button>
                <a href="{{ route('transactions.export') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-download me-1"></i>Export
                </a>
            </li>
        </ul>
    </div>
    <div id="filterForm" class="px-3 py-2 border-bottom bg-light" style="display: none;">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <small class="fw-bold text-muted text-uppercase">Filter By:</small>
            </div>
            <div class="col-md-2">
                <input type="date" name="date" class="form-control form-control-sm"
                       value="{{ request('date') }}" placeholder="Filter Date">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="in_transit" {{ request('status') == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="damaged" {{ request('status') == 'damaged' ? 'selected' : '' }}>Damaged</option>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-outline-primary" type="submit"><i class="fas fa-search"></i> Apply</button>
            </div>
            <div class="col-auto">
                <a href="{{ route('transactions.index', ['type' => request('type', 'all')]) }}" class="btn btn-sm btn-link text-danger">Clear</a>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Type</th>
                    <th>Timestamp</th>
                    <th>SKU / Item</th>
                    <th>Location</th>
                    <th class="text-end">Qty</th>
                    <th>Operator</th>
                    <th>Status</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-1 {{ $trx->type == 'incoming' ? 'text-success' : 'text-danger' }}">
                            <i class="fas {{ $trx->type == 'incoming' ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
                            <span class="fw-semibold">{{ ucfirst($trx->type) }}</span>
                        </div>
                    </td>
                    <td class="font-monospace text-muted">{{ $trx->created_at->format('M d, Y · H:i:s') }}</td>
                    <td>
                        <div>
                            <span class="fw-bold">{{ $trx->item->item_code ?? 'N/A' }}</span>
                            <br><small class="text-muted">{{ $trx->item->name ?? '' }}</small>
                        </div>
                    </td>
                    <td>{{ $trx->location ?? '-' }}</td>
                    <td class="text-end font-monospace fw-bold {{ $trx->type == 'incoming' ? 'text-success' : 'text-danger' }}">
                        {{ $trx->type == 'incoming' ? '+' : '-' }}{{ number_format($trx->quantity) }}
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-1">
                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white"
                                 style="width: 24px; height: 24px; font-size: 10px;">
                                {{ substr($trx->user->name ?? 'S', 0, 1) }}{{ substr($trx->user->name ?? 'Y', 1, 1) }}
                            </div>
                            <small>{{ $trx->user->name ?? 'System' }}</small>
                        </div>
                    </td>
                    <td>
                        @php
                            $statusClass = match($trx->status) {
                                'verified', 'completed' => 'bg-success',
                                'shipped' => 'bg-info',
                                'pending' => 'bg-warning text-dark',
                                'in_transit' => 'bg-secondary',
                                'damaged' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ ucfirst($trx->status) }}</span>
                    </td>
                    <td class="text-end">
                        <form action="{{ route('transactions.destroy', $trx) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this transaction? This will reverse the stock changes.')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-link text-danger p-1" title="Delete & Reverse Stock"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">No transactions found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
        <small class="text-muted">
            Showing {{ $transactions->firstItem() ?? 0 }} to {{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }} transactions
        </small>
        {{ $transactions->links() }}
    </div>
</div>

<div class="modal fade" id="movementModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('transactions.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Record New Stock Movement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="type" id="typeIncoming" value="incoming" checked>
                            <label class="btn btn-outline-success" for="typeIncoming">
                                <i class="fas fa-arrow-down me-1"></i>Incoming
                            </label>
                            <input type="radio" class="btn-check" name="type" id="typeOutgoing" value="outgoing">
                            <label class="btn btn-outline-danger" for="typeOutgoing">
                                <i class="fas fa-arrow-up me-1"></i>Outgoing
                            </label>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small text-uppercase text-muted">Item</label>
                            <select name="item_id" class="form-select" required>
                                <option value="">Select Item</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}">{{ $item->item_code }} - {{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-uppercase text-muted">Quantity</label>
                            <input type="number" name="quantity" class="form-control" min="1" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-uppercase text-muted">Location</label>
                            <select name="location" class="form-select">
                                <option value="">Select Location</option>
                                <option value="Warehouse A - Bay 12">Warehouse A - Bay 12</option>
                                <option value="Warehouse A - Bay 13">Warehouse A - Bay 13</option>
                                <option value="Warehouse B - Dock 1">Warehouse B - Dock 1</option>
                                <option value="In-Transit">In-Transit</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-uppercase text-muted">Reference #</label>
                            <input type="text" name="reference" class="form-control" placeholder="PO-99283">
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label small text-uppercase text-muted">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"
                                  placeholder="Additional details about this movement..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Confirm Transaction</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleFilter() {
        const el = document.getElementById('filterForm');
        el.style.display = el.style.display === 'none' ? 'flex' : 'none';
    }

    @if(request('date') || request('status'))
        document.getElementById('filterForm').style.display = 'flex';
    @endif
</script>
@endpush

@extends('layouts.app')

@section('title', 'Items')

@section('content')
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="#">Master Data</a></li>
                <li class="breadcrumb-item active">Item Management</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-0">Item Catalog</h2>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('items.export') }}" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-download me-1"></i>Export CSV
        </a>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#itemModal">
            <i class="fas fa-plus me-1"></i>Add New Item
        </button>
    </div>
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
                <small class="text-muted">Total SKUs</small>
                <h4 class="fw-bold mb-0">{{ $items->total() }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm border-start border-danger border-4">
            <div class="card-body">
                <small class="text-muted">Low Stock</small>
                <h4 class="fw-bold text-danger mb-0">
                    {{ \App\Models\Item::where('status', 'low_stock')->count() }}
                </h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <small class="text-muted">Categories</small>
                <h4 class="fw-bold mb-0">{{ \App\Models\Category::count() }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <small class="text-muted">Total Value</small>
                <h4 class="fw-bold mb-0">
                    Rp {{ number_format(\App\Models\Item::sum(\DB::raw('unit_price * stock_level')), 0, ',', '.') }}
                </h4>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <i class="fas fa-filter text-muted me-1"></i>
                <span class="small fw-bold text-muted text-uppercase">Filters:</span>
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Stock Status</option>
                    <option value="in_stock" {{ request('status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                    <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                    <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search by name or code..."
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('items.index') }}" class="btn btn-link btn-sm text-primary">Clear All</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th width="40"><input type="checkbox" class="form-check-input"></th>
                    <th>ITEM CODE</th>
                    <th>NAME</th>
                    <th>CATEGORY</th>
                    <th class="text-end">UNIT PRICE</th>
                    <th class="text-end">STOCK LEVEL</th>
                    <th>STATUS</th>
                    <th class="text-center">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td><input type="checkbox" class="form-check-input"></td>
                    <td class="text-primary fw-bold font-monospace">{{ $item->item_code }}</td>
                    <td class="fw-semibold">{{ $item->name }}</td>
                    <td>
                        <span class="badge bg-light text-dark">{{ $item->category->name ?? 'N/A' }}</span>
                    </td>
                    <td class="text-end font-monospace">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="text-end font-monospace {{ $item->status == 'low_stock' ? 'text-danger fw-bold' : '' }}">
                        {{ number_format($item->stock_level) }} units
                    </td>
                    <td>
                        @if($item->status == 'in_stock')
                            <span class="badge bg-success-subtle text-success">
                                <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>In Stock
                            </span>
                        @elseif($item->status == 'low_stock')
                            <span class="badge bg-danger-subtle text-danger">
                                <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>Low Stock
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary">
                                <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>Out of Stock
                            </span>
                        @endif
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-link text-primary p-1 edit-item"
                                data-id="{{ $item->id }}"
                                data-code="{{ $item->item_code }}"
                                data-name="{{ $item->name }}"
                                data-category="{{ $item->category_id }}"
                                data-price="{{ $item->unit_price }}"
                                data-stock="{{ $item->stock_level }}"
                                data-status="{{ $item->status }}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('items.destroy', $item) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this item?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-link text-danger p-1">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">No items found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
        <small class="text-muted">Showing {{ $items->firstItem() ?? 0 }} to {{ $items->lastItem() ?? 0 }} of {{ $items->total() }} items</small>
        {{ $items->links() }}
    </div>
</div>

<div class="modal fade" id="itemModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('items.store') }}" id="itemForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add New Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="_method" value="POST" id="methodField">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small text-uppercase text-muted">Item Code</label>
                            <input type="text" name="item_code" id="item_code" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-uppercase text-muted">Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-uppercase text-muted">Category</label>
                            <select name="category_id" id="category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-uppercase text-muted">Unit Price</label>
                            <input type="number" step="0.01" name="unit_price" id="unit_price" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-uppercase text-muted">Stock Level</label>
                            <input type="number" name="stock_level" id="stock_level" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-uppercase text-muted">Status</label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="in_stock">In Stock</option>
                                <option value="low_stock">Low Stock</option>
                                <option value="out_of_stock">Out of Stock</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Item</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.edit-item').forEach(btn => {
        btn.addEventListener('click', function () {
            const modal = new bootstrap.Modal(document.getElementById('itemModal'));
            document.getElementById('modalTitle').textContent = 'Edit Item';
            document.getElementById('methodField').value = 'PUT';
            document.getElementById('itemForm').action = '/items/' + this.dataset.id;
            document.getElementById('item_code').value = this.dataset.code;
            document.getElementById('name').value = this.dataset.name;
            document.getElementById('category_id').value = this.dataset.category;
            document.getElementById('unit_price').value = this.dataset.price;
            document.getElementById('stock_level').value = this.dataset.stock;
            document.getElementById('status').value = this.dataset.status;
            modal.show();
        });
    });

    document.getElementById('itemModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('modalTitle').textContent = 'Add New Item';
        document.getElementById('methodField').value = 'POST';
        document.getElementById('itemForm').action = '{{ route("items.store") }}';
        document.getElementById('itemForm').reset();
    });

    @if(request('add'))
        const autoModal = new bootstrap.Modal(document.getElementById('itemModal'));
        autoModal.show();
    @endif
</script>
@endpush

@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h2 class="fw-bold mb-0">Categories</h2>
        <p class="text-muted">Manage product categories.</p>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#categoryModal">
        <i class="fas fa-plus me-1"></i>Add Category
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>NAME</th>
                    <th>SLUG</th>
                    <th class="text-center">TOTAL ITEMS</th>
                    <th>CREATED</th>
                    <th class="text-center">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                <tr>
                    <td>{{ $cat->id }}</td>
                    <td class="fw-semibold">{{ $cat->name }}</td>
                    <td class="text-muted">{{ $cat->slug }}</td>
                    <td class="text-center">
                        <span class="badge bg-primary rounded-pill">{{ $cat->items_count }}</span>
                    </td>
                    <td class="text-muted">{{ $cat->created_at->format('M d, Y') }}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-link text-primary p-1 edit-category"
                                data-id="{{ $cat->id }}"
                                data-name="{{ $cat->name }}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('categories.destroy', $cat) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this category?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-link text-danger p-1"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">No categories found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
        <small class="text-muted">Showing {{ $categories->firstItem() ?? 0 }} to {{ $categories->lastItem() ?? 0 }} of {{ $categories->total() }}</small>
        {{ $categories->links() }}
    </div>
</div>

<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('categories.store') }}" id="categoryForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="catModalTitle">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="_method" value="POST" id="catMethodField">
                    <div class="mb-3">
                        <label class="form-label small text-uppercase text-muted">Category Name</label>
                        <input type="text" name="name" id="cat_name" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.edit-category').forEach(btn => {
        btn.addEventListener('click', function () {
            const modal = new bootstrap.Modal(document.getElementById('categoryModal'));
            document.getElementById('catModalTitle').textContent = 'Edit Category';
            document.getElementById('catMethodField').value = 'PUT';
            document.getElementById('categoryForm').action = '/categories/' + this.dataset.id;
            document.getElementById('cat_name').value = this.dataset.name;
            modal.show();
        });
    });
    document.getElementById('categoryModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('catModalTitle').textContent = 'Add Category';
        document.getElementById('catMethodField').value = 'POST';
        document.getElementById('categoryForm').action = '{{ route("categories.store") }}';
        document.getElementById('categoryForm').reset();
    });
</script>
@endpush

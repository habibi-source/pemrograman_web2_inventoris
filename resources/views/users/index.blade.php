@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h2 class="fw-bold mb-0">User Management</h2>
        <p class="text-muted">Manage system access, roles, and account permissions.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary btn-sm">
            <i class="fas fa-download me-1"></i>Export List
        </button>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#userModal">
            <i class="fas fa-user-plus me-1"></i>Create User
        </button>
    </div>
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

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <small class="text-muted text-uppercase">Total Users</small>
                <h4 class="fw-bold text-primary mb-0">{{ number_format($totalUsers) }}</h4>
                <small class="text-success"><i class="fas fa-arrow-up me-1"></i>+12% from last month</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <small class="text-muted text-uppercase">Active Staff</small>
                <h4 class="fw-bold mb-0">{{ number_format($activeStaff) }}</h4>
                @if($totalUsers > 0)
                <div class="progress mt-2" style="height: 4px;">
                    <div class="progress-bar bg-success" style="width: {{ ($activeStaff / max($totalUsers, 1)) * 100 }}%"></div>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <small class="text-muted text-uppercase">Admins</small>
                <h4 class="fw-bold mb-0">{{ $admins }}</h4>
                <small class="text-muted">Strict access control</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <small class="text-muted text-uppercase">Pending Approval</small>
                <h4 class="fw-bold text-danger mb-0">{{ $pendingApproval }}</h4>
                <a href="#" class="small text-danger">Review now</a>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>USER</th>
                    <th>ROLE</th>
                    <th>STATUS</th>
                    <th>LAST LOGIN</th>
                    <th class="text-end">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                                 style="width: 36px; height: 36px; background-color: {{ $user->role == 'admin' ? '#003c90' : '#434653' }};">
                                {{ substr($user->name, 0, 1) }}{{ substr($user->name, strpos($user->name, ' ') + 1, 1) }}
                            </div>
                            <div>
                                <p class="fw-semibold mb-0">{{ $user->name }}</p>
                                <small class="text-muted">{{ $user->email }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ strtoupper($user->role) }}</span>
                    </td>
                    <td>
                        @if($user->status == 'active')
                            <span class="badge bg-success-subtle text-success">
                                <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>Active
                            </span>
                        @else
                            <span class="badge bg-danger-subtle text-danger">
                                <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>Inactive
                            </span>
                        @endif
                    </td>
                    <td class="text-muted">{{ $user->last_login_at ?? $user->created_at->format('Y-m-d H:i') }}</td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-link text-primary p-1 edit-user"
                                data-id="{{ $user->id }}"
                                data-name="{{ $user->name }}"
                                data-email="{{ $user->email }}"
                                data-role="{{ $user->role }}"
                                data-status="{{ $user->status }}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this user?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-link text-danger p-1"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4 text-muted">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
        <small class="text-muted">Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} entries</small>
        {{ $users->links() }}
    </div>
</div>

<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('users.store') }}" id="userForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalTitle">Add New System User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="_method" value="POST" id="userMethodField">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small text-uppercase text-muted">Name</label>
                            <input type="text" name="name" id="user_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-uppercase text-muted">Email</label>
                            <input type="email" name="email" id="user_email" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-uppercase text-muted">Role</label>
                            <select name="role" id="user_role" class="form-select" required>
                                <option value="staff">Staff</option>
                                <option value="admin">Admin</option>
                                <option value="manager">Manager</option>
                                <option value="auditor">Auditor</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-uppercase text-muted">Status</label>
                            <select name="status" id="user_status" class="form-select" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-uppercase text-muted">Password</label>
                            <input type="password" name="password" id="user_password" class="form-control" minlength="8">
                            <small class="text-muted">Min. 8 characters</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Account</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.edit-user').forEach(btn => {
        btn.addEventListener('click', function () {
            const modal = new bootstrap.Modal(document.getElementById('userModal'));
            document.getElementById('userModalTitle').textContent = 'Edit User';
            document.getElementById('userMethodField').value = 'PUT';
            document.getElementById('userForm').action = '/users/' + this.dataset.id;
            document.getElementById('user_name').value = this.dataset.name;
            document.getElementById('user_email').value = this.dataset.email;
            document.getElementById('user_role').value = this.dataset.role;
            document.getElementById('user_status').value = this.dataset.status;
            document.getElementById('user_password').required = false;
            document.getElementById('user_password').placeholder = 'Leave empty to keep current';
            modal.show();
        });
    });

    document.getElementById('userModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('userModalTitle').textContent = 'Add New System User';
        document.getElementById('userMethodField').value = 'POST';
        document.getElementById('userForm').action = '{{ route("users.store") }}';
        document.getElementById('userForm').reset();
        document.getElementById('user_password').required = true;
        document.getElementById('user_password').placeholder = '';
    });
</script>
@endpush

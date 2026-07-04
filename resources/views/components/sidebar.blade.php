<aside class="sidebar bg-white border-end d-flex flex-column flex-shrink-0 position-fixed" style="width: 240px; z-index: 1000;">
    <div class="p-3 border-bottom">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-cubes text-primary fs-4"></i>
            <div>
                <h1 class="fs-5 fw-bold text-primary mb-0">LogisticsPro</h1>
                <small class="text-muted">Inventory Control</small>
            </div>
        </div>
    </div>

    <nav class="flex-grow-1 p-2">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie me-2" style="width: 20px;"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('items.index') }}" class="nav-link {{ request()->routeIs('items.*') ? 'active' : '' }}">
                    <i class="fas fa-boxes me-2" style="width: 20px;"></i>
                    Items
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <i class="fas fa-tags me-2" style="width: 20px;"></i>
                    Categories
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                    <i class="fas fa-exchange-alt me-2" style="width: 20px;"></i>
                    Transactions
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar me-2" style="width: 20px;"></i>
                    Reports
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="fas fa-users me-2" style="width: 20px;"></i>
                    User Management
                </a>
            </li>
        </ul>
    </nav>

    <div class="p-3 border-top">
        <button class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2" data-bs-toggle="modal" data-bs-target="#addItemModal">
            <i class="fas fa-plus"></i>
            Add New SKU
        </button>
    </div>
</aside>

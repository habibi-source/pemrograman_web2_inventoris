<header class="bg-white border-bottom px-4 d-flex align-items-center justify-content-between position-fixed top-0 end-0" style="left: 240px; height: 56px; z-index: 999;">
    <div class="d-flex align-items-center flex-grow-1" style="max-width: 400px;">
        <div class="input-group input-group-sm">
            <span class="input-group-text bg-light border-0">
                <i class="fas fa-search text-muted"></i>
            </span>
            <input type="text" class="form-control bg-light border-0" placeholder="Search master data..." id="globalSearch">
        </div>
    </div>

    <div class="d-flex align-items-center gap-3">
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-light btn-sm rounded-circle position-relative p-2 border-0">
                <i class="fas fa-bell"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.5rem;">3</span>
            </button>
            <button class="btn btn-light btn-sm rounded-circle p-2 border-0">
                <i class="fas fa-cog"></i>
            </button>
        </div>

        <div class="vr"></div>

        <div class="dropdown">
            <button class="btn dropdown-toggle d-flex align-items-center gap-2 border-0" data-bs-toggle="dropdown">
                <div class="text-end d-none d-sm-block">
                    <p class="mb-0 small fw-bold">{{ Auth::user()->name ?? 'Admin User' }}</p>
                    <small class="text-muted" style="font-size: 0.65rem;">{{ ucfirst(Auth::user()->role ?? 'Administrator') }}</small>
                </div>
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white" style="width: 36px; height: 36px;">
                    <i class="fas fa-user"></i>
                </div>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a></li>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </ul>
        </div>
    </div>
</header>

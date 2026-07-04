<header class="bg-white border-bottom px-4 d-flex align-items-center justify-content-between position-fixed top-0 end-0" style="left: 240px; height: 56px; z-index: 999;">
    <div class="d-flex align-items-center flex-grow-1" style="max-width: 400px;">
        <form action="{{ route('items.index') }}" method="GET" class="w-100">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light border-0">
                    <i class="fas fa-search text-muted"></i>
                </span>
                <input type="text" name="search" class="form-control bg-light border-0" placeholder="Search master data..." id="globalSearch" value="{{ request('search') }}">
            </div>
        </form>
    </div>

    <div class="d-flex align-items-center gap-3">
        <div class="d-flex align-items-center gap-2">
            <div class="dropdown">
                <button class="btn btn-light btn-sm rounded-circle position-relative p-2 border-0" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell"></i>
                    @if(isset($notifications) && $notifications->count() > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.5rem;">
                            {{ $notifications->count() }}
                        </span>
                    @endif
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="width: 320px;">
                    <li><h6 class="dropdown-header fw-bold text-dark border-bottom pb-2">System Notifications</h6></li>
                    @forelse($notifications ?? [] as $notif)
                        <li>
                            <a class="dropdown-item d-flex gap-3 py-3 border-bottom text-wrap" href="{{ $notif['link'] }}">
                                <div class="mt-1"><i class="fas {{ $notif['icon'] }} fs-5"></i></div>
                                <div>
                                    <p class="mb-1 small" style="line-height: 1.4;">{{ $notif['text'] }}</p>
                                    <small class="text-muted" style="font-size: 0.75rem;">{{ $notif['time'] }}</small>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li><span class="dropdown-item-text text-muted py-4 text-center d-block">No new notifications.</span></li>
                    @endforelse
                </ul>
            </div>
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

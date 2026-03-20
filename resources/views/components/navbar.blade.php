<nav class="navbar navbar-expand-lg navbar-light bg-white px-4 sticky-top shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center gap-3">
            <div class="brand-logo">
                <img
                    src="{{ asset('images/logo-left.png') }}"
                    alt="School Logo"
                    class="logo-img"
                >
            </div>
            <div class="brand-info">
                <div class="system-name">
                    {{ config('app.name') }}
                </div>
                <div class="school-name">
                    {{ config('app.client_name') }}
                </div>
            </div>
        </a>

        <div class="d-flex align-items-center ms-auto gap-3">
            <button class="nav-icon">
                <i class="bi bi-bell"></i>
            </button>
            <div class="dropdown">
                <button class="nav-user dropdown-toggle" data-bs-toggle="dropdown">
                    <span class="user-avatar">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U',0,1)) }}
                    </span>
                    <span class="user-name">
                        {{ auth()->user()->name ?? 'User' }}
                    </span>
                </button>

                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                    <li>
                        <a class="dropdown-item" href="#">Profile</a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#">Settings</a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

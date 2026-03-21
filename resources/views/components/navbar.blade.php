<nav class="navbar navbar-expand-lg bg-white px-3 px-lg-4 sticky-top shadow-sm">

    <div class="container-fluid d-flex align-items-center justify-content-between">

        <div class="d-flex align-items-center gap-2">

            <button id="toggleSidebar" class="btn nav-icon d-lg-none">
                <i class="bi bi-list"></i>
            </button>

            <a class="navbar-brand d-flex align-items-center gap-2 gap-md-3 m-0">
                <div class="brand-logo">
                    <img src="{{ asset('images/logo-left.png') }}" class="logo-img">
                </div>

                <div class="brand-info d-none d-sm-flex">
                    <div class="system-name">
                        {{ config('app.name') }}
                    </div>
                    <div class="school-name">
                        {{ config('app.client_name') }}
                    </div>
                </div>
            </a>

        </div>

        <div class="d-flex align-items-center gap-2 gap-md-3">

            <button class="nav-icon position-relative">
                <i class="bi bi-bell"></i>
            </button>

            <div class="dropdown">
                <button class="nav-user dropdown-toggle" data-bs-toggle="dropdown">
                    <span class="user-avatar">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U',0,1)) }}
                    </span>

                    <span class="user-name d-none d-md-inline">
                        {{ auth()->user()->name ?? 'User' }}
                    </span>
                </button>

                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                    <li><a class="dropdown-item" href="#">Profile</a></li>
                    <li><a class="dropdown-item" href="#">Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
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

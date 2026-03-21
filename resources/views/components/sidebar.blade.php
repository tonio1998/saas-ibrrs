<button class="btn nav-icon d-lg-none" id="toggleSidebar">
    <i class="bi bi-list"></i>
</button>

<div class="sidebar" id="sidebar">
    <ul class="sidebar-menu">

        <li class="sidebar-item">
            <a href="{{ route('dashboard.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('dashboard.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 sidebar-icon"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link" data-bs-toggle="collapse" href="#CertificateMenu">
                <i class="bi bi-person-badge sidebar-icon"></i>
                <span class="text-truncate">Certificate Management</span>
                <i class="bi bi-chevron-down dropdown-icon"></i>
            </a>
            <div class="collapse sidebar-dropdown" id="CertificateMenu">
                <a href="{{ route('certificates_request.create') }}" class="sidebar-sublink">Issue Certificate</a>
                <a href="{{ route('certificates_request.index') }}" class="sidebar-sublink">Masterlist</a>
            </div>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link" data-bs-toggle="collapse" href="#teacherMenu">
                <i class="bi bi-person-badge sidebar-icon"></i>
                <span class="text-truncate">Resident Management</span>
                <i class="bi bi-chevron-down dropdown-icon"></i>
            </a>
            <div class="collapse sidebar-dropdown" id="teacherMenu">
                <a href="{{ route('residents.create') }}" class="sidebar-sublink">Add Resident</a>
                <a href="{{ route('residents.index') }}" class="sidebar-sublink">Masterlist</a>
            </div>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link" data-bs-toggle="collapse" href="#HouseholdMenu">
                <i class="bi bi-person-badge sidebar-icon"></i>
                <span class="text-truncate">Household Management</span>
                <i class="bi bi-chevron-down dropdown-icon"></i>
            </a>
            <div class="collapse sidebar-dropdown" id="HouseholdMenu">
                <a href="{{ route('households.create') }}" class="sidebar-sublink">Add Household</a>
                <a href="{{ route('households.index') }}" class="sidebar-sublink">Masterlist</a>
            </div>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link" data-bs-toggle="collapse" href="#PurokMenu">
                <i class="bi bi-person-badge sidebar-icon"></i>
                <span class="text-truncate">Purok Management</span>
                <i class="bi bi-chevron-down dropdown-icon"></i>
            </a>
            <div class="collapse sidebar-dropdown" id="PurokMenu">
                <a href="{{ route('puroks.create') }}" class="sidebar-sublink">Add Purok</a>
                <a href="{{ route('puroks.index') }}" class="sidebar-sublink">Masterlist</a>
            </div>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link d-flex justify-content-between" href="#">
                <span><i class="bi bi-bar-chart sidebar-icon"></i> Reports</span>
                <span class="badge bg-warning text-dark">Soon</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link d-flex justify-content-between" href="#">
                <span><i class="bi bi-gear sidebar-icon"></i> Settings</span>
                <span class="badge bg-warning text-dark">Soon</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link" data-bs-toggle="collapse" href="#certificatetypesMenu">
                <i class="bi bi-shield-lock sidebar-icon"></i>
                <span class="text-truncate">Certificate Types</span>
                <i class="bi bi-chevron-down dropdown-icon"></i>
            </a>
            <div class="collapse sidebar-dropdown" id="certificatetypesMenu">
                <a href="{{ route('certificate-types.create') }}" class="sidebar-sublink">Add Certificate Type</a>
                <a href="{{ route('certificate-types.index') }}" class="sidebar-sublink">Masterlist</a>
            </div>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link" data-bs-toggle="collapse" href="#usersMenu">
                <i class="bi bi-shield-lock sidebar-icon"></i>
                <span class="text-truncate">User Management</span>
                <i class="bi bi-chevron-down dropdown-icon"></i>
            </a>
            <div class="collapse sidebar-dropdown" id="usersMenu">
                <a href="{{ route('permissions.index') }}" class="sidebar-sublink">Permissions</a>
                <a href="{{ route('roles.index') }}" class="sidebar-sublink">Roles</a>
                <a href="{{ route('users.index') }}" class="sidebar-sublink">Masterlist</a>
            </div>
        </li>

    </ul>
</div>

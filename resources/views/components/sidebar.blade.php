<div class="sidebar">
    <ul class="sidebar-menu">

        <li class="sidebar-item">
            <a
                href="{{ route('dashboard.dashboard') }}"
                class="sidebar-link {{ request()->routeIs('dashboard.dashboard') ? 'active' : '' }}"
            >
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
                <a href="{{ route('certificates_request.create') }}" class="sidebar-sublink">
                    <i class="bi bi-person-plus sidebar-subicon"></i>
                    Issue Certificate
                </a>
                <a href="{{ route('certificates_request.index') }}" class="sidebar-sublink">
                    <i class="bi bi-person-vcard sidebar-subicon"></i>
                    Masterlist
                </a>
            </div>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link" data-bs-toggle="collapse" href="#teacherMenu">
                <i class="bi bi-person-badge sidebar-icon"></i>
                <span class="text-truncate">Resident Management</span>
                <i class="bi bi-chevron-down dropdown-icon"></i>
            </a>
            <div class="collapse sidebar-dropdown" id="teacherMenu">
                <a href="{{ route('residents.create') }}" class="sidebar-sublink">
                    <i class="bi bi-person-plus sidebar-subicon"></i>
                    Add Resident
                </a>
                <a href="{{ route('residents.index') }}" class="sidebar-sublink">
                    <i class="bi bi-person-vcard sidebar-subicon"></i>
                    Masterlist
                </a>
            </div>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link" data-bs-toggle="collapse" href="#HouseholdMenu">
                <i class="bi bi-person-badge sidebar-icon"></i>
                <span class="text-truncate">Household Management</span>
                <i class="bi bi-chevron-down dropdown-icon"></i>
            </a>
            <div class="collapse sidebar-dropdown" id="HouseholdMenu">
                <a href="{{ route('households.create') }}" class="sidebar-sublink">
                    <i class="bi bi-person-plus sidebar-subicon"></i>
                    Add Household
                </a>
                <a href="{{ route('households.index') }}" class="sidebar-sublink">
                    <i class="bi bi-person-vcard sidebar-subicon"></i>
                    Masterlist
                </a>
            </div>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link" data-bs-toggle="collapse" href="#PurokMenu">
                <i class="bi bi-person-badge sidebar-icon"></i>
                <span class="text-truncate">Purok Management</span>
                <i class="bi bi-chevron-down dropdown-icon"></i>
            </a>
            <div class="collapse sidebar-dropdown" id="PurokMenu">
                <a href="{{ route('puroks.create') }}" class="sidebar-sublink">
                    <i class="bi bi-person-plus sidebar-subicon"></i>
                    Add Purok
                </a>
                <a href="{{ route('puroks.index') }}" class="sidebar-sublink">
                    <i class="bi bi-person-vcard sidebar-subicon"></i>
                    Masterlist
                </a>
            </div>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link d-flex align-items-center justify-content-between" href="#">
                <span class="d-flex align-items-center gap-2">
                    <i class="bi bi-bar-chart sidebar-icon"></i>
                    <span>Reports</span>
                </span>
                <span class="badge bg-warning text-dark">Soon</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link d-flex align-items-center justify-content-between" href="#">
                <span class="d-flex align-items-center gap-2">
                    <i class="bi bi-gear sidebar-icon"></i>
                    <span>Settings</span>
                </span>
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
                <a href="{{ route('certificate-types.create') }}" class="sidebar-sublink">
                    <i class="bi bi-key sidebar-subicon"></i>
                    Add Certificate Type
                </a>
                <a href="{{ route('certificate-types.index') }}" class="sidebar-sublink">
                    <i class="bi bi-people sidebar-subicon"></i>
                    Masterlist
                </a>
            </div>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link" data-bs-toggle="collapse" href="#usersMenu">
                <i class="bi bi-shield-lock sidebar-icon"></i>
                <span class="text-truncate">User Management</span>
                <i class="bi bi-chevron-down dropdown-icon"></i>
            </a>
            <div class="collapse sidebar-dropdown" id="usersMenu">
                <a href="{{ route('permissions.index') }}" class="sidebar-sublink">
                    <i class="bi bi-key sidebar-subicon"></i>
                    Permissions
                </a>
                <a href="{{ route('roles.index') }}" class="sidebar-sublink">
                    <i class="bi bi-person-gear sidebar-subicon"></i>
                    Roles
                </a>
                <a href="{{ route('users.index') }}" class="sidebar-sublink">
                    <i class="bi bi-people sidebar-subicon"></i>
                    Masterlist
                </a>
            </div>
        </li>


    </ul>
</div>

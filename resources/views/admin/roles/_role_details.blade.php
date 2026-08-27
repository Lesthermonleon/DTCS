<div id="role-details-container" class="card-body">
    @if(isset($selectedRole))
        <div class="row g-4 mb-3">
            <div class="col-md-3">
                <span class="text-uppercase text-muted small fw-semibold d-block mb-1">Role Name</span>
                <span class="fs-6 fw-bold text-body">{{ $selectedRole->name }}</span>
            </div>
            <div class="col-md-3">
                <span class="text-uppercase text-muted small fw-semibold d-block mb-1">Identifier</span>
                <code class="font-monospace text-success fs-6">{{ $selectedRole->slug }}</code>
            </div>
            <div class="col-md-3">
                <span class="text-uppercase text-muted small fw-semibold d-block mb-1">Status</span>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-medium">
                    <i class="bi bi-check-circle me-1"></i>Active
                </span>
            </div>
            <div class="col-md-3">
                <span class="text-uppercase text-muted small fw-semibold d-block mb-1">Assigned Users</span>
                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                    <i class="bi bi-people me-1"></i>{{ $selectedRole->users_count }} Users
                </span>
            </div>
        </div>

        @if($selectedRole->description)
        <div class="mb-4 pt-3 border-top">
            <span class="text-uppercase text-muted small fw-semibold d-block mb-1">Role Description</span>
            <p class="text-body-secondary small mb-0">{{ $selectedRole->description }}</p>
        </div>
        @endif

        <div class="pt-3 border-top">
            <span class="text-uppercase text-muted small fw-semibold d-block mb-2">Access Scope</span>
            <div class="d-flex flex-wrap gap-2">
                @php
                    $rawModules = str_replace(['All Modules (', ')'], '', $selectedRole->accessible_modules);
                    $modules = array_map('trim', explode(',', $rawModules));
                    $isAllModules = str_contains($selectedRole->accessible_modules, 'All Modules') || in_array($selectedRole->slug, ['system-administrator', 'admin']);
                @endphp

                @if($isAllModules)
                    <span class="badge bg-success-subtle text-success border border-success-subtle py-2 px-3 fw-medium">
                        <i class="bi bi-shield-check me-1"></i>All System Modules
                    </span>
                @else
                    @foreach($modules as $m)
                        @if(!empty($m))
                            <span class="badge bg-success-subtle text-success border border-success-subtle py-2 px-3 fw-medium">
                                <i class="bi bi-check-circle me-1"></i>{{ $m }}
                            </span>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
    @endif
</div>

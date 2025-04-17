<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Permission</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
@include('layouts.adminNavbar')
<div class="container">
    <h2>Assign Permissions to Roles</h2>

    <form method="GET" action="{{ route('admin.assign-permissions') }}" class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search Permissions">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-secondary w-100">Search</button>
            </div>
        </div>
    </form>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif


    <form method="POST" action="{{ route('admin.assign-permissions.update') }}">
        @csrf
        @method('POST')

        <div class="mb-3">
            <label class="form-label">Roles</label>
            <div class="row">
                @foreach($roles as $role)
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="role_ids[]" value="{{ $role->id }}" id="role-{{ $role->id }}">
                            <label class="form-check-label" for="role-{{ $role->id }}">
                                {{ ucfirst($role->name) }}
                            </label>
                        </div>

                        <div class="permissions-for-role-{{ $role->id }} mt-3">
                            <label class="form-label">Permissions</label>
                            @foreach($permissions as $permission)  <!-- Show all permissions from the table -->
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="permissions[{{ $role->id }}][]" value="{{ $permission->id }}" id="perm-{{ $permission->id }}"
                                       @if($role->permissions->contains($permission->id)) checked @endif>
                                <label class="form-check-label" for="perm-{{ $permission->id }}">
                                    {{ ucfirst($permission->name) }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Assign Permissions</button>
    </form>
</div>
</body>
</html>

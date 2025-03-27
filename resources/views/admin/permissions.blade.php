<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Permission</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
@include('layouts.navbar')
<div class="container">
    <h2>Assign Permissions to Roles</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.assign-permissions.update') }}">
        @csrf
        @method('POST')

        <div class="mb-3">
            <label for="role" class="form-label">Select Role</label>
            <select name="role_id" id="role" class="form-control" required>
                <option value="">Choose Role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Permissions</label>
            <div class="row">
                @foreach($permissions as $permission)
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="perm-{{ $permission->id }}"
                            >
                            <label class="form-check-label" for="perm-{{ $permission->id }}" >
                                {{ ucfirst(str_replace('_', ' ', $permission->name)) }}
                            </label>
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

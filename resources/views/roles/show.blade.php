<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users with Role: {{ $role->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
@include('layouts.adminNavbar')

<div class="container mt-5">

    <h1 class="text-center mb-5">
        Users with the Role: <span class="text-primary">{{ ucfirst($role->name) }}</span>
    </h1>

    <div class="card p-4 mb-4">
        <form method="GET" action="{{ route('roles.show', $role->id) }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="search" class="form-label">Search Users</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Search by name or email" value="{{ request()->input('search') }}">
                </div>
                <div class="col-md-3 d-grid">
                    <button type="submit" class="btn btn-outline-primary">Search</button>
                </div>
            </div>
        </form>
    </div>

    @if ($users->isEmpty())
        <div class="alert alert-warning text-center" role="alert">
            No users found for this role.
        </div>
    @else
        <div class="list-group mb-4">
            @foreach ($users as $user)
                <div class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                    <div class="mb-2 mb-md-0">
                        <strong>{{ $user->first_name }}</strong> ({{ $user->email }})
                    </div>

                    <div>
                        @if(!$user->hasRole($role->name))
                            <form action="{{ route('roles.update', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="role_id" value="{{ $role->id }}">
                                <button type="submit" class="btn btn-sm btn-success">Assign {{ ucfirst($role->name) }}</button>
                            </form>
                        @else
                            <form action="{{ route('roles.updateRemove', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="role_id" value="{{ $role->id }}">
                                <button type="submit" class="btn btn-sm btn-warning">Remove {{ ucfirst($role->name) }}</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="d-flex justify-content-between mt-4">
        @if ($users->onFirstPage())
            <span class="btn btn-outline-secondary disabled">← Previous</span>
        @else
            <a href="{{ $users->previousPageUrl() }}" class="btn btn-outline-primary">← Previous</a>
        @endif

        @if ($users->hasMorePages())
            <a href="{{ $users->nextPageUrl() }}" class="btn btn-outline-primary">Next →</a>
        @else
            <span class="btn btn-outline-secondary disabled">Next →</span>
        @endif
    </div>

</div>
</body>
</html>

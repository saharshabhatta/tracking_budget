<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
@include('layouts.adminNavbar')

<div class="container mt-5">
    <h1 class="text-center mb-5">Roles List</h1>

    <div class="card p-4 mb-4">
        <form method="GET" action="{{ route('roles.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control" placeholder="Search Roles">
                </div>
                <div class="col-md-3 d-grid">
                    <button type="submit" class="btn btn-outline-primary">Search</button>
                </div>
            </div>
        </form>
    </div>

    <div class="text-end mb-3">
        <a href="{{ route('roles.create') }}" class="btn btn-primary">+ Create New Role</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Role Name</th>
                </tr>
                </thead>
                <tbody>
                @foreach($roles as $role)
                    <tr>
                        <td>{{ $role->id }}</td>
                        <td>
                            <a href="{{ route('roles.show', $role) }}" class="text-decoration-none">
                                {{ ucfirst($role->name) }}
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4">
        @if ($roles->onFirstPage())
            <span class="btn btn-outline-secondary disabled">← Previous</span>
        @else
            <a href="{{ $roles->previousPageUrl() }}" class="btn btn-outline-primary">← Previous</a>
        @endif

        @if ($roles->hasMorePages())
            <a href="{{ $roles->nextPageUrl() }}" class="btn btn-outline-primary">Next →</a>
        @else
            <span class="btn btn-outline-secondary disabled">Next →</span>
        @endif
    </div>
</div>

</body>
</html>

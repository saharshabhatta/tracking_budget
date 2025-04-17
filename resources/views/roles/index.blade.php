<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
@include('layouts.adminNavbar')

<div class="container mt-5">
    <h1 class="mb-4">Roles List</h1>

    <form method="GET" action="{{ route('roles.index') }}" class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search Roles">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-secondary w-100">Search</button>
            </div>
        </div>
    </form>

    <div class="mb-3 text-end">
        <a href="{{ route('roles.create') }}" class="btn btn-primary">Create New Role</a>
    </div>

    <table class="table table-bordered">
        <thead class="thead-dark">
        <tr>
            <th>ID</th>
            <th>Role Name</th>
        </tr>
        </thead>
        <tbody>
        @foreach($roles as $role)
            <tr>
                <td>{{ $role->id }}</td>
                <td><a href="{{ route('roles.show', $role) }}">{{ $role->name }}</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-between align-items-center">
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
